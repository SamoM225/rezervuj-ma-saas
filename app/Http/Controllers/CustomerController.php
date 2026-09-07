<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BusinessSetting;
use App\Support\Tenancy;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Booking::query()
            ->select('customer_email')
            ->selectRaw('MAX(customer_name) as customer_name')
            ->selectRaw('MAX(customer_phone) as customer_phone')
            ->selectRaw('COUNT(*) as bookings_count')
            ->selectRaw('MAX(date) as last_booking_date')
            ->selectRaw('MAX(created_at) as last_created_at')
            ->groupBy('customer_email')
            ->orderByDesc('last_created_at')
            ->get();

        $emails = $customers->pluck('customer_email')->filter()->values();

        // Získame posledné rezervácie podľa dátumu a času rezervácie (nie created_at)
        $lastBookings = Booking::query()
            ->whereIn('customer_email', $emails)
            ->with(['service', 'worker'])
            ->orderByDesc('date')
            ->orderByDesc('start_time')
            ->get()
            ->unique('customer_email')
            ->keyBy('customer_email');

        $customers->transform(function ($customer) use ($lastBookings) {
            $customer->email_key = strtolower((string) $customer->customer_email);
            $customer->last_booking = $lastBookings[$customer->customer_email] ?? null;

            return $customer;
        });

        $stats = [
            'total' => $customers->count(),
            'repeat' => $customers->where('bookings_count', '>', 1)->count(),
            'recent' => $customers->filter(function ($customer) {
                return $customer->last_created_at &&
                    Carbon::parse($customer->last_created_at)->gte(now()->subDays(30));
            })->count(),
            'avgBookings' => round($customers->avg('bookings_count'), 1),
        ];

        return view('admin.customers.index', compact('customers', 'stats'));
    }

    public function show(Request $request, string $customer)
    {
        $email = urldecode($customer);

        $totalBookings = Booking::withTrashed()->where('customer_email', $email)->count();

        // History keeps deleted appointments too (they only vanish from the calendar).
        $bookings = Booking::withTrashed()->with(['service', 'worker'])
            ->where('customer_email', $email)
            ->orderByDesc('date')
            ->orderByDesc('start_time')
            ->limit($request->integer('limit', 20))
            ->get();

        $firstBooking = $bookings->first();

        $summary = [
            'email' => $email,
            'name' => $firstBooking?->customer_name,
            'phone' => $firstBooking?->customer_phone,
            'total_bookings' => $totalBookings,
            'last_booking' => $firstBooking ? [
                'id' => $firstBooking->id,
                'date' => $firstBooking->date,
                'start_time' => $firstBooking->start_time,
                'end_time' => $firstBooking->end_time,
                'status' => $firstBooking->status,
                'service' => $firstBooking->service ? ['name' => $firstBooking->service->name] : null,
                'worker' => $firstBooking->worker ? ['name' => $firstBooking->worker->name] : null,
            ] : null,
        ];

        // Transform bookings to ensure proper JSON serialization
        $bookingsData = $bookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'date' => $booking->date,
                'start_time' => $booking->start_time,
                'end_time' => $booking->end_time,
                'status' => $booking->trashed() ? 'deleted' : $booking->status,
                'notes' => $booking->notes,
                'service' => $booking->service ? ['id' => $booking->service->id, 'name' => $booking->service->name] : null,
                'worker' => $booking->worker ? ['id' => $booking->worker->id, 'name' => $booking->worker->name] : null,
            ];
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'summary' => $summary,
                'bookings' => $bookingsData,
            ]);
        }

        return redirect()->route('admin.customers.index');
    }

    public function sendEmails(Request $request)
    {
        $validated = $request->validate([
            'recipients' => 'required|array|min:1',
            'recipients.*' => 'required|email',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $emails = collect($validated['recipients'])->map(fn ($email) => strtolower(trim($email)))->unique()->values();

        // Only people who actually booked here can be written to from the admin.
        $knownEmails = Booking::query()
            ->whereIn('customer_email', $emails)
            ->pluck('customer_email')
            ->map(fn ($email) => strtolower($email))
            ->unique()
            ->values();

        if ($knownEmails->isEmpty()) {
            return back()->withErrors(['recipients' => __('ui.ziadny_z_vybranych_prijemcov_nema_rezervaciu')]);
        }

        $fromAddress = BusinessSetting::get('support_email', config('mail.from.address'));
        $fromName = BusinessSetting::get('business_name', config('app.name'));

        // The composer takes plain text; keep paragraphs and line breaks, escape everything else.
        $html = '<div style="font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:1.6;color:#1f1a14">'
            .nl2br(e($validated['body']))
            .'</div>';

        foreach ($knownEmails as $email) {
            try {
                Mail::html($html, function ($message) use ($email, $validated, $fromAddress, $fromName) {
                    if ($fromAddress) {
                        $message->from($fromAddress, $fromName ?? $fromAddress);
                    }
                    $message->to($email)->subject($validated['subject']);
                });
            } catch (\Throwable $e) {
                Log::error('Failed to send customer email', [
                    'email' => $email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'sent' => $knownEmails,
            ]);
        }

        return back()->with('success', __('ui.e_mail_bol_odoslany').$knownEmails->count().' zákazníkom.');
    }

    /** CSV of every customer (aggregated from bookings), UTF-8 with BOM so Excel reads the diacritics. */
    public function export(): StreamedResponse
    {
        $rows = Booking::query()
            ->select('customer_email')
            ->selectRaw('MAX(customer_name) as customer_name')
            ->selectRaw('MAX(customer_phone) as customer_phone')
            ->selectRaw('COUNT(*) as bookings_count')
            ->selectRaw('MIN(date) as first_booking')
            ->selectRaw('MAX(date) as last_booking')
            ->selectRaw('MAX(gdpr_consent_at) as gdpr_consent_at')
            ->groupBy('customer_email')
            ->orderBy('customer_name')
            ->get();

        $filename = 'customers-'.(Tenancy::current()?->slug ?? 'export').'-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['name', 'email', 'phone', 'bookings', 'first_booking', 'last_booking', 'gdpr_consent_at'], ';');
            foreach ($rows as $row) {
                fputcsv($out, [$row->customer_name, $row->customer_email, $row->customer_phone, $row->bookings_count, $row->first_booking, $row->last_booking, $row->gdpr_consent_at], ';');
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=utf-8']);
    }

    /**
     * Right to erasure: the person disappears, the appointments stay as
     * anonymous rows so statistics and the calendar history remain consistent.
     */
    public function destroy(string $customer)
    {
        $email = urldecode($customer);
        $replacement = 'deleted-'.substr(sha1($email.'|'.Tenancy::id()), 0, 12).'@anonymized.invalid';

        $count = Booking::withTrashed()->where('customer_email', $email)->update([
            'customer_name' => __('ui.anonymized_customer'),
            'customer_email' => $replacement,
            'customer_phone' => '',
            'notes' => null,
        ]);

        Log::info('Customer data erased', ['tenant_id' => Tenancy::id(), 'bookings' => $count, 'by' => auth()->id()]);

        return redirect()->route('admin.customers.index')->with('success', __('ui.customer_data_deleted'));
    }
}
