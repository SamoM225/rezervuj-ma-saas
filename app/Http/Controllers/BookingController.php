<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BusinessSetting;
use App\Models\City;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use App\Models\WorkerAvailability;
use App\Services\BlacklistService;
use App\Services\IcsCalendarService;
use App\Support\BookingMailer;
use App\Support\PlanLimits;
use App\Support\SlotHolds;
use App\Support\Tenancy;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    /** Public booking page. */
    public function home(Request $request)
    {
        // Widget mode (no top bar / footer) is decided per request, never stored:
        // ?embed=1 from the widget script, or any navigation inside an iframe.
        $embed = $request->boolean('embed') || $request->header('Sec-Fetch-Dest') === 'iframe';

        $cities = City::orderBy('name')->get(['id', 'name', 'address']);

        return view('home', [
            'embed' => $embed,
            'cities' => $cities,
            'holdMinutes' => max(1, (int) BusinessSetting::get('slot_hold_minutes', 5)),
            'onlineBookingEnabled' => (bool) BusinessSetting::get('allow_online_booking', true),
        ]);
    }

    /**
     * Embeddable script for the client's own website. It only draws a button and
     * an iframe: the booking itself always runs on this domain, and the browser
     * enforces the "allowed websites" list through the frame-ancestors policy.
     */
    public function widgetScript()
    {
        if (! PlanLimits::currentAllows('embed_widget')) {
            return response('/* rezervuj-ma: the embed widget is available on the Pro plan. */', 403)->header('Content-Type', 'application/javascript; charset=utf-8');
        }

        $base = rtrim(route('home'), '/');
        $accent = Setting::get('main_accent', Setting::DEFAULT_ACCENT);
        $label = BusinessSetting::get('business_name', config('app.name'));
        $config = json_encode(['base' => $base, 'accent' => $accent, 'name' => $label], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG);
        $jsText = fn (string $text) => json_encode($text, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS);
        $bookLabel = $jsText(__('widget.book_now'));
        $titleSuffix = $jsText(' – '.__('widget.reservation_title'));
        $closeLabel = $jsText(__('customer.bug.close'));

        $js = <<<JS
(function () {
  var cfg = {$config};
  var s = document.currentScript || (function () { var a = document.getElementsByTagName('script'); return a[a.length - 1]; })();
  var d = s.dataset || {};
  var mode = d.mode || 'modal';
  var label = d.label || {$bookLabel};
  var accent = d.color || cfg.accent;
  var url = cfg.base + '/?embed=1';
  var target = d.target ? document.querySelector(d.target) : null;

  function iframe(height) {
    var f = document.createElement('iframe');
    f.src = url; f.title = cfg.name + {$titleSuffix}; f.loading = 'lazy';
    f.setAttribute('allow', ''); f.setAttribute('referrerpolicy', 'strict-origin-when-cross-origin');
    f.style.cssText = 'width:100%;height:' + height + ';border:0;display:block;background:transparent;';
    return f;
  }

  if (mode === 'inline') {
    if (!target) { console.warn('[rezervuj-ma] data-target element not found'); return; }
    target.appendChild(iframe(d.height || '760px'));
    return;
  }

  var overlay = null;
  function close() { if (overlay) { overlay.remove(); overlay = null; document.documentElement.style.overflow = ''; } }
  function open(e) {
    if (e) e.preventDefault();
    if (mode === 'tab') { window.open(url, '_blank', 'noopener'); return; }
    if (overlay) return;
    overlay = document.createElement('div');
    overlay.setAttribute('role', 'dialog'); overlay.setAttribute('aria-modal', 'true'); overlay.setAttribute('aria-label', label);
    overlay.style.cssText = 'position:fixed;inset:0;z-index:2147483000;background:rgba(20,16,10,.62);display:flex;align-items:center;justify-content:center;padding:1rem;box-sizing:border-box;';
    var box = document.createElement('div');
    box.style.cssText = 'position:relative;width:min(960px,100%);height:min(860px,100%);background:#faf7f2;border-radius:1.25rem;overflow:hidden;box-shadow:0 30px 80px rgba(0,0,0,.35);';
    var x = document.createElement('button');
    x.type = 'button'; x.setAttribute('aria-label', {$closeLabel}); x.innerHTML = '&times;';
    x.style.cssText = 'position:absolute;top:.6rem;right:.6rem;z-index:2;width:2.25rem;height:2.25rem;border:0;border-radius:999px;background:rgba(31,26,20,.85);color:#fff;font:600 1.25rem/1 sans-serif;cursor:pointer;';
    x.addEventListener('click', close);
    box.appendChild(x); box.appendChild(iframe('100%'));
    overlay.appendChild(box);
    overlay.addEventListener('click', function (ev) { if (ev.target === overlay) close(); });
    document.addEventListener('keydown', function esc(ev) { if (ev.key === 'Escape') { close(); document.removeEventListener('keydown', esc); } });
    document.documentElement.style.overflow = 'hidden';
    document.body.appendChild(overlay);
  }

  if (target) { target.addEventListener('click', open); return; }

  var b = document.createElement('button');
  b.type = 'button'; b.textContent = label;
  b.style.cssText = 'position:fixed;right:1.25rem;bottom:1.25rem;z-index:2147482000;padding:.85rem 1.35rem;border:0;border-radius:999px;background:' + accent + ';color:#fff;font:600 .95rem/1 "Plus Jakarta Sans",system-ui,sans-serif;box-shadow:0 12px 30px rgba(0,0,0,.22);cursor:pointer;';
  b.addEventListener('click', open);
  document.body.appendChild(b);
})();
JS;

        return response($js, 200, [
            'Content-Type' => 'application/javascript; charset=utf-8',
            'Cache-Control' => 'public, max-age=3600',
            'Access-Control-Allow-Origin' => '*',
        ]);
    }

    public function index()
    {
        return response()->json(Booking::with(['service', 'worker'])->latest()->get());
    }

    public function adminIndex()
    {
        $workers = User::query()
            ->where('role', 'worker')
            ->with('services:id')
            ->orderBy('name')
            ->get()
            ->map(fn (User $worker) => [
                'id' => $worker->id,
                'name' => $worker->name,
                'calendar_color' => $worker->calendar_color,
                'service_ids' => $worker->services->pluck('id')->values(),
            ]);
        $services = Service::query()
            ->select(['id', 'name', 'duration', 'price', 'category_id'])
            ->orderBy('name')
            ->get();

        return view('admin.bookings.index', [
            'workers' => $workers,
            'services' => $services,
            'businessHours' => [
                'start' => BusinessSetting::get('business_start_time', '08:00'),
                'end' => BusinessSetting::get('business_end_time', '18:00'),
            ],
            'requireConfirmation' => (bool) BusinessSetting::get('require_confirmation', false),
        ]);
    }

    public function store(Request $request)
    {
        $actor = $request->user();
        $isPublic = $actor === null;

        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time' => 'nullable|date_format:H:i',
            'start_time' => 'nullable|date_format:H:i',
            'worker_id' => ['required', 'integer', Rule::exists('users', 'id')->where('tenant_id', Tenancy::id())],
            'service_id' => ['required', 'integer', Rule::exists('services', 'id')->where('tenant_id', Tenancy::id())],
            'city_id' => ['nullable', 'integer', Rule::exists('cities', 'id')->where('tenant_id', Tenancy::id())],
            'name' => 'nullable|string|max:255',
            'customer_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'customer_email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:30',
            'customer_phone' => 'nullable|string|max:30',
            'notes' => 'nullable|string|max:2000',
            'gdpr' => $isPublic ? 'accepted' : 'nullable|boolean',
            'admin_override' => 'nullable|boolean',
        ]);

        $startInput = $validated['time'] ?? $validated['start_time'] ?? null;
        if (! $startInput) {
            return $this->validationFailure($request, 'start_time', __('customer.errors.pick_time'));
        }

        if ($actor?->is_worker && $actor->id !== (int) $validated['worker_id']) {
            abort(403, __('customer.errors.worker_only_self'));
        }
        if ($actor && ! $actor->hasAnyRole(['worker', 'admin', 'superadmin'])) {
            abort(403);
        }
        if ($isPublic && ! BusinessSetting::get('allow_online_booking', true)) {
            return $this->validationFailure($request, 'date', __('customer.errors.online_unavailable'));
        }
        if (($tenant = Tenancy::current()) && ! PlanLimits::canCreateBooking($tenant)) {
            return $this->validationFailure($request, 'date', $isPublic
                ? __('customer.errors.quota_public')
                : __('customer.errors.quota_admin', ['limit' => (int) config('tenancy.plans.free.bookings_per_month', 50)]));
        }

        $customerName = trim((string) ($validated['customer_name'] ?? $validated['name'] ?? ''));
        $customerEmail = strtolower(trim((string) ($validated['customer_email'] ?? $validated['email'] ?? '')));
        $customerPhone = trim((string) ($validated['customer_phone'] ?? $validated['phone'] ?? ''));
        if ($customerName === '' || $customerEmail === '' || $customerPhone === '') {
            return $this->validationFailure($request, 'customer_name', __('customer.errors.contact_required'));
        }

        $date = Carbon::parse($validated['date'])->toDateString();
        $start = $this->time($startInput);
        $service = Service::findOrFail($validated['service_id']);
        $end = Carbon::parse($start)->addMinutes((int) $service->duration)->format('H:i:s');
        $city = isset($validated['city_id']) ? City::find($validated['city_id']) : null;

        if ($city && $city->id !== optional(User::find($validated['worker_id']))->city_id) {
            return $this->validationFailure($request, 'city_id', __('customer.errors.location_mismatch'));
        }

        $minimum = now()->addHours((int) BusinessSetting::get('booking_advance_hours', 2));
        $requestedAt = Carbon::parse($date.' '.$start);
        if ($requestedAt->lt($minimum)) {
            return $this->validationFailure($request, 'date', __('customer.errors.min_advance'));
        }
        if ($requestedAt->gt(now()->startOfDay()->addDays((int) BusinessSetting::getMaxBookingAdvanceDays())->endOfDay())) {
            return $this->validationFailure($request, 'date', __('customer.errors.out_of_range'));
        }
        if (in_array($date, (array) BusinessSetting::get('business_holidays', []), true)) {
            return $this->validationFailure($request, 'date', __('customer.errors.closed_day'));
        }

        $blacklist = BlacklistService::check($customerEmail, $customerPhone);
        $canOverride = $actor && $actor->canViewAllBookings() && $request->boolean('admin_override') && $blacklist['can_override'];
        if ($blacklist['blocked'] && ! $canOverride) {
            return BlacklistService::blockedResponse($request);
        }

        $limit = (int) BusinessSetting::get('max_daily_slots_per_customer', 0);
        if ($limit > 0 && Booking::where('customer_email', $customerEmail)->where('date', $date)->where('status', '!=', 'cancelled')->count() >= $limit) {
            return $this->validationFailure($request, 'date', __('customer.errors.daily_limit'));
        }

        // The visitor's own temporary hold must not count as a conflict.
        $ownHold = $request->hasSession() ? $this->holdToken($request) : null;

        $booking = DB::transaction(function () use ($validated, $date, $start, $end, $service, $city, $customerName, $customerEmail, $customerPhone, $actor, $isPublic, $ownHold) {
            // A lock on the durable worker row prevents two empty-slot checks from
            // passing concurrently for the same calendar.
            $worker = User::lockForUpdate()->findOrFail($validated['worker_id']);
            if (! $worker->is_worker) {
                abort(422, __('customer.errors.not_a_worker'));
            }
            if (! $worker->services()->whereKey($service->id)->exists()) {
                abort(422, __('customer.errors.service_not_offered'));
            }
            if ($isPublic && BusinessSetting::get('enforce_fixed_start_times', false) && ! $this->isFixedSlotStart($worker, $date, $start, $service)) {
                abort(422, __('customer.errors.fixed_slots'));
            }
            if (! $this->isSlotBookable($worker, $date, $start, $end, $ownHold)) {
                abort(422, __('customer.errors.slot_unavailable'));
            }

            $booking = Booking::create([
                'user_id' => $worker->id,
                'service_id' => $service->id,
                'city_id' => $city?->id,
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'customer_phone' => $customerPhone,
                'city' => $city?->name ?? $worker->city?->name ?? '',
                'date' => $date,
                'start_time' => $start,
                'end_time' => $end,
                'notes' => $validated['notes'] ?? null,
                'status' => BusinessSetting::get('require_confirmation', false) ? 'pending' : 'confirmed',
                'locale' => app()->getLocale(),
                'gdpr_consent_at' => $actor ? null : now(),
                'gdpr_policy_version' => $actor ? null : (string) config('gdpr.policy_version', '1.0'),
            ]);

            if ($ownHold) {
                SlotHolds::release($worker->id, $date, $ownHold);
            }

            return $booking;
        });

        BookingMailer::send($booking->fresh(['service', 'worker', 'location']), $booking->status === 'pending' ? BookingMailer::PENDING : BookingMailer::CONFIRMED);

        $confirmationUrl = URL::temporarySignedRoute('booking.confirmation', now()->addDays(7), ['booking' => $booking->id]);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'booking' => $booking,
                'message' => __('customer.flash.created'),
                'redirect' => $isPublic ? $confirmationUrl : null,
            ], 201);
        }

        return redirect()->to($confirmationUrl);
    }

    public function confirmation(Booking $booking)
    {
        $booking->load(['service', 'worker', 'location']);

        return view('booking.confirmation', ['booking' => $booking, 'calendarUrls' => IcsCalendarService::buildCalendarUrls($booking)]);
    }

    public function showCancellation(Booking $booking)
    {
        return view('booking.cancellation', ['booking' => $booking]);
    }

    public function cancelByCustomer(Request $request, Booking $booking)
    {
        if ($booking->status === 'cancelled') {
            return back()->with('success', __('customer.flash.already_cancelled'));
        }
        $minimum = (int) BusinessSetting::get('min_cancel_hours', 0);
        if ($minimum > 0 && Carbon::parse($booking->date.' '.$booking->start_time)->lt(now()->addHours($minimum))) {
            return back()->withErrors(['booking' => __('customer.flash.too_late_online')]);
        }

        $booking->update(['status' => 'cancelled']);
        BookingMailer::send($booking, BookingMailer::CANCELLED);

        return back()->with('success', __('customer.flash.cancelled'));
    }

    public function getAvaiableDates($workerId)
    {
        return response()->json($this->availableDates((int) $workerId)->values());
    }

    public function getAvaiableDatesWeb(Request $request, $workerId)
    {
        if ($workerId === 'any') {
            $request->validate(['service_id' => 'required|integer|exists:services,id']);
            $workers = $this->eligibleWorkersForAny($request->integer('category_id') ?: null, $request->integer('service_id'), $request->integer('city_id') ?: null);
            $dates = $workers->reduce(fn ($carry, $worker) => $carry->merge($this->availableDates($worker->id)), collect())->unique()->sort()->values();
        } else {
            $dates = $this->availableDates((int) $workerId)->values();
        }

        return $request->wantsJson() ? response()->json(['availableDates' => $dates]) : response()->json($dates);
    }

    public function getAvailableTimesWeb(Request $request, $workerId, $date)
    {
        $request->validate(['service_id' => 'required|integer|exists:services,id']);
        $service = Service::findOrFail($request->integer('service_id'));

        // "Nezáleží mi na odborníkovi": merge every eligible worker's free slots into one
        // list, keeping the first (by id) worker free at each time so it stays deterministic.
        if ($workerId === 'any') {
            $workers = $this->eligibleWorkersForAny($request->integer('category_id') ?: null, $service->id, $request->integer('city_id') ?: null);
            $byStart = [];
            foreach ($workers as $worker) {
                foreach ($this->availableSlots($worker, Carbon::parse($date)->toDateString(), $service) as $slot) {
                    if (! isset($byStart[$slot['start_time']])) {
                        $byStart[$slot['start_time']] = $slot + ['worker_id' => $worker->id, 'worker_name' => $worker->name];
                    }
                }
            }
            ksort($byStart);
            $slots = array_values($byStart);
        } else {
            $worker = User::findOrFail($workerId);
            if (! $worker->is_worker || ! $worker->services()->whereKey($service->id)->exists()) {
                abort(404);
            }
            $slots = array_map(fn ($slot) => $slot + ['worker_id' => $worker->id, 'worker_name' => $worker->name], $this->availableSlots($worker, Carbon::parse($date)->toDateString(), $service));
        }

        return response()->json(['timeSlots' => $slots]);
    }

    public function holdSlot(Request $request)
    {
        $data = $request->validate([
            'worker_id' => 'required|integer|exists:users,id',
            'service_id' => 'required|integer|exists:services,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
        ]);
        $worker = User::findOrFail($data['worker_id']);
        $service = Service::findOrFail($data['service_id']);
        $date = Carbon::parse($data['date'])->toDateString();
        $start = $this->time($data['start_time']);
        $end = Carbon::parse($start)->addMinutes((int) $service->duration)->format('H:i:s');
        if (BusinessSetting::get('enforce_fixed_start_times', false) && ! $this->isFixedSlotStart($worker, $date, $start, $service)) {
            return response()->json(['message' => __('customer.errors.fixed_slots')], 422);
        }
        if (! $worker->is_worker || ! $worker->services()->whereKey($service->id)->exists() || ! $this->isSlotBookable($worker, $date, $start, $end)) {
            return response()->json(['message' => __('customer.errors.slot_gone')], 409);
        }

        $token = $this->holdToken($request);
        $minutes = max(1, (int) BusinessSetting::get('slot_hold_minutes', 5));
        $hold = SlotHolds::place($worker->id, $service->id, $date, $start, $end, $token, $minutes);
        if (! $hold) {
            return response()->json(['message' => __('customer.errors.slot_held_by_other')], 409);
        }

        $expiresAt = Carbon::parse($hold['expires_at']);

        return response()->json([
            'message' => __('customer.flash.slot_held'),
            'expires_at' => $expiresAt->toIso8601String(),
            'hold_seconds' => max(0, now()->diffInSeconds($expiresAt, false)),
        ]);
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $data = $request->validate(['status' => 'required|in:pending,confirmed,cancelled,completed']);
        $actor = $request->user();
        if ($actor?->is_worker && $booking->user_id !== $actor->id) {
            abort(403, __('customer.errors.worker_only_own'));
        }

        $previous = $booking->status;
        $booking->update($data);

        if ($previous !== $booking->status) {
            if ($booking->status === 'cancelled') {
                BookingMailer::send($booking, BookingMailer::CANCELLED);
            } elseif ($previous === 'pending' && $booking->status === 'confirmed') {
                BookingMailer::send($booking, BookingMailer::CONFIRMED);
            }
        }

        return $request->wantsJson()
            ? response()->json(['booking' => $booking->fresh(['service', 'worker'])])
            : back()->with('success', __('customer.flash.status_updated'));
    }

    public function reschedule(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
        ]);
        $actor = $request->user();
        if ($actor?->is_worker && $booking->user_id !== $actor->id) {
            abort(403, __('customer.errors.worker_only_own'));
        }
        if ($booking->status === 'cancelled') {
            return response()->json(['message' => __('customer.errors.cannot_move_cancelled')], 422);
        }
        $worker = $booking->worker;
        $start = $this->time($data['start_time']);
        $end = Carbon::parse($start)->addMinutes((int) $booking->service->duration)->format('H:i:s');
        $date = Carbon::parse($data['date'])->toDateString();

        DB::transaction(function () use ($booking, $worker, $date, $start, $end) {
            User::lockForUpdate()->findOrFail($worker->id);
            if (! $this->isSlotBookable($worker, $date, $start, $end, null, $booking->id)) {
                abort(422, __('customer.errors.slot_unavailable'));
            }
            $booking->update(['date' => $date, 'start_time' => $start, 'end_time' => $end, 'reminder_sent' => false]);
        });

        BookingMailer::send($booking->fresh(['service', 'worker', 'location']), $booking->status === 'pending' ? BookingMailer::PENDING : BookingMailer::CONFIRMED);

        return response()->json(['booking' => $booking->fresh(['service', 'worker']), 'message' => __('customer.flash.moved')]);
    }

    public function downloadIcs(Booking $booking)
    {
        return IcsCalendarService::download(IcsCalendarService::fromBooking($booking), 'rezervacia-'.$booking->id.'.ics');
    }

    public function destroy(Request $request, Booking $booking)
    {
        if ($request->user()?->is_worker) {
            abort(403);
        }
        // Soft delete: gone from the calendar (slot is free again), kept for the customer's history.
        $booking->delete();

        return $request->wantsJson() ? response()->json(status: 204) : back()->with('success', __('customer.flash.deleted'));
    }

    /** Workers who can take this service (optionally narrowed to a category/city), for "any worker" bookings. */
    private function eligibleWorkersForAny(?int $categoryId, int $serviceId, ?int $cityId)
    {
        return User::where('role', 'worker')
            ->whereHas('services', fn ($query) => $query->whereKey($serviceId))
            ->when($categoryId, fn ($query, $categoryId) => $query->whereHas('categories', fn ($categories) => $categories->whereKey($categoryId)))
            ->when($cityId, fn ($query, $cityId) => $query->where('city_id', $cityId))
            ->orderBy('id')
            ->get();
    }

    private function availableDates(int $workerId)
    {
        $worker = User::find($workerId);
        if (! $worker || ! $worker->is_worker) {
            return collect();
        }
        $start = now()->addHours((int) BusinessSetting::get('booking_advance_hours', 2))->startOfDay();
        $end = now()->addDays((int) BusinessSetting::getMaxBookingAdvanceDays())->endOfDay();
        $holidays = (array) BusinessSetting::get('business_holidays', []);
        $availability = WorkerAvailability::where('user_id', $worker->id)->where('is_active', true)->get();

        $dates = collect();
        for ($day = $start->copy(); $day->lte($end); $day->addDay()) {
            $date = $day->toDateString();
            if (! in_array($date, $holidays, true)
                && $this->hasDailyBookingCapacity($worker, $date)
                && $availability->contains(fn ($item) => $this->availabilityCovers($item, $date, '00:00:00', '23:59:59', false))) {
                $dates->push($date);
            }
        }

        return $dates;
    }

    private function availableSlots(User $worker, string $date, Service $service): array
    {
        if (in_array($date, (array) BusinessSetting::get('business_holidays', []), true)) {
            return [];
        }
        $slots = [];
        $minimum = now()->addHours((int) BusinessSetting::get('booking_advance_hours', 2));
        $buffer = (int) ($service->break_time ?? BusinessSetting::get('service_buffer_minutes', 0));
        $availability = WorkerAvailability::where('user_id', $worker->id)->where('is_active', true)->get();
        foreach ($availability as $item) {
            if (! $this->availabilityCovers($item, $date, '00:00:00', '23:59:59', false)) {
                continue;
            }
            $cursor = Carbon::parse($item->start_time)->seconds(0);
            $limit = Carbon::parse($item->end_time)->seconds(0);
            while ($cursor->copy()->addMinutes((int) $service->duration)->lte($limit)) {
                $start = $cursor->format('H:i:s');
                $end = $cursor->copy()->addMinutes((int) $service->duration)->format('H:i:s');
                if (Carbon::parse($date.' '.$start)->gte($minimum) && $this->isSlotBookable($worker, $date, $start, $end)) {
                    $slots[] = ['start_time' => substr($start, 0, 5), 'end_time' => substr($end, 0, 5), 'display' => substr($start, 0, 5)];
                }
                $cursor->addMinutes((int) $service->duration + $buffer);
            }
        }

        return array_values(array_unique($slots, SORT_REGULAR));
    }

    /**
     * Public bookings can be limited to the schedule's generated start times.
     * Staff can still place exceptional appointments from the internal calendar.
     */
    private function isFixedSlotStart(User $worker, string $date, string $start, Service $service): bool
    {
        return in_array($start, $this->fixedSlotStarts($worker, $date, $service), true);
    }

    /** @return array<int, string> */
    private function fixedSlotStarts(User $worker, string $date, Service $service): array
    {
        $starts = [];
        $buffer = (int) ($service->break_time ?? BusinessSetting::get('service_buffer_minutes', 0));
        $availability = WorkerAvailability::where('user_id', $worker->id)->where('is_active', true)->get();

        foreach ($availability as $item) {
            if (! $this->availabilityCovers($item, $date, '00:00:00', '23:59:59', false)) {
                continue;
            }

            $cursor = Carbon::parse($item->start_time)->seconds(0);
            $limit = Carbon::parse($item->end_time)->seconds(0);
            while ($cursor->copy()->addMinutes((int) $service->duration)->lte($limit)) {
                $starts[] = $cursor->format('H:i:s');
                $cursor->addMinutes((int) $service->duration + $buffer);
            }
        }

        return array_values(array_unique($starts));
    }

    private function hasDailyBookingCapacity(User $worker, string $date, ?int $exceptBooking = null): bool
    {
        $limit = (int) BusinessSetting::get('max_bookings_per_worker_per_day', 0);
        if ($limit <= 0) {
            return true;
        }

        return Booking::where('user_id', $worker->id)
            ->where('date', $date)
            ->where('status', '!=', 'cancelled')
            ->when($exceptBooking, fn ($query) => $query->where('id', '!=', $exceptBooking))
            ->count() < $limit;
    }

    private function isSlotBookable(User $worker, string $date, string $start, string $end, ?string $ownHold = null, ?int $exceptBooking = null): bool
    {
        $availability = WorkerAvailability::where('user_id', $worker->id)->where('is_active', true)->get();
        if (! $availability->contains(fn ($item) => $this->availabilityCovers($item, $date, $start, $end))) {
            return false;
        }
        if (! $this->hasDailyBookingCapacity($worker, $date, $exceptBooking)) {
            return false;
        }
        $conflict = Booking::where('user_id', $worker->id)->where('date', $date)->where('status', '!=', 'cancelled')
            ->when($exceptBooking, fn ($query) => $query->where('id', '!=', $exceptBooking))
            ->get(['start_time', 'end_time'])->contains(fn ($entry) => $this->overlaps($entry->start_time, $entry->end_time, $start, $end));
        if ($conflict) {
            return false;
        }
        if (Schedule::where('user_id', $worker->id)->where('date', $date)->whereIn('status', ['unavailable', 'blocked'])
            ->get(['start_time', 'end_time'])->contains(fn ($entry) => $this->overlaps($entry->start_time, $entry->end_time, $start, $end))) {
            return false;
        }

        return ! SlotHolds::conflicts($worker->id, $date, $start, $end, $ownHold);
    }

    private function availabilityCovers(WorkerAvailability $availability, string $date, string $start, string $end, bool $checkTimes = true): bool
    {
        $day = Carbon::parse($date);
        if (! in_array($day->dayOfWeek, (array) $availability->days_of_week, true)
            || $day->lt(Carbon::parse($availability->start_date)->startOfDay())
            || ($availability->end_date && $day->gt(Carbon::parse($availability->end_date)->endOfDay()))) {
            return false;
        }

        return ! $checkTimes || ($start >= $this->time((string) $availability->start_time) && $end <= $this->time((string) $availability->end_time));
    }

    private function holdToken(Request $request): string
    {
        $token = $request->session()->get('booking_hold_token');
        if (! $token) {
            $token = (string) Str::uuid();
            $request->session()->put('booking_hold_token', $token);
        }

        return $token;
    }

    private function time(string $time): string
    {
        return Carbon::parse($time)->format('H:i:s');
    }

    private function overlaps(string $existingStart, string $existingEnd, string $start, string $end): bool
    {
        return $this->time($existingStart) < $end && $this->time($existingEnd) > $start;
    }

    private function validationFailure(Request $request, string $field, string $message)
    {
        return $request->expectsJson() || $request->wantsJson()
            ? response()->json(['message' => $message, 'errors' => [$field => [$message]]], 422)
            : back()->withErrors([$field => $message])->withInput();
    }
}
