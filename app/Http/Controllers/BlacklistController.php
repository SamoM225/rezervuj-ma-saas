<?php

namespace App\Http\Controllers;

use App\Models\BlacklistEntry;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BlacklistController extends Controller
{
    /**
     * Display list of all blacklist entries
     */
    public function index(Request $request)
    {
        $query = BlacklistEntry::with(['createdBy', 'updatedBy'])
            ->orderByDesc('created_at');

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'expired') {
                $query->where(function ($q) {
                    $q->where('is_active', false)
                        ->orWhere('expires_at', '<=', now());
                });
            }
        }

        // Filter by severity
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        // Filter by reason category
        if ($request->filled('reason_category')) {
            $query->where('reason_category', $request->reason_category);
        }

        // Filter by identifier type
        if ($request->filled('identifier_type')) {
            $query->where('identifier_type', $request->identifier_type);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('identifier_value', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%")
                    ->orWhere('internal_notes', 'like', "%{$search}%");
            });
        }

        $entries = $query->paginate(20)->withQueryString();

        // Statistics
        $stats = [
            'total' => BlacklistEntry::count(),
            'active' => BlacklistEntry::active()->count(),
            'hard_bans' => BlacklistEntry::active()->where('severity', 'hard_ban')->count(),
            'expiring_soon' => BlacklistEntry::active()
                ->whereNotNull('expires_at')
                ->where('expires_at', '<=', now()->addDays(7))
                ->count(),
        ];

        return view('admin.blacklist.index', [
            'entries' => $entries,
            'stats' => $stats,
            'severityOptions' => BlacklistEntry::getSeverityOptions(),
            'identifierTypeOptions' => BlacklistEntry::getIdentifierTypeOptions(),
            'reasonCategoryOptions' => BlacklistEntry::getReasonCategoryOptions(),
            'filters' => $request->only(['status', 'severity', 'reason_category', 'identifier_type', 'search']),
        ]);
    }

    /**
     * Show form to create new blacklist entry
     */
    public function create(Request $request)
    {
        // Pre-fill from query params (when coming from customers page)
        $prefill = [
            'email' => $request->get('email'),
            'phone' => $request->get('phone'),
            'name' => $request->get('name'),
        ];

        return view('admin.blacklist.create', [
            'severityOptions' => BlacklistEntry::getSeverityOptions(),
            'identifierTypeOptions' => BlacklistEntry::getIdentifierTypeOptions(),
            'reasonCategoryOptions' => BlacklistEntry::getReasonCategoryOptions(),
            'prefill' => $prefill,
        ]);
    }

    /**
     * Store new blacklist entry
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'identifier_type' => ['required', Rule::in(['email', 'phone'])],
            'identifier_value' => ['required', 'string', 'max:255'],
            'severity' => ['required', Rule::in(['warning', 'soft_ban', 'hard_ban'])],
            'reason_category' => ['required', Rule::in(array_keys(BlacklistEntry::getReasonCategoryOptions()))],
            'reason' => ['nullable', 'string', 'max:1000'],
            'internal_notes' => ['nullable', 'string', 'max:2000'],
            'expires_at' => ['nullable', 'date', 'after:now'],
            'is_active' => ['boolean'],
        ]);

        // Normalize identifier value
        $identifierValue = $validated['identifier_type'] === 'email'
            ? strtolower(trim($validated['identifier_value']))
            : preg_replace('/[^0-9+]/', '', $validated['identifier_value']);

        // Check if already exists
        $existing = BlacklistEntry::where('identifier_type', $validated['identifier_type'])
            ->where('identifier_value', $identifierValue)
            ->first();

        if ($existing) {
            return back()
                ->withInput()
                ->withErrors(['identifier_value' => __('ui.tento_identifikator_je_uz_v_blackliste')]);
        }

        $entry = BlacklistEntry::create([
            'identifier_type' => $validated['identifier_type'],
            'identifier_value' => $identifierValue,
            'severity' => $validated['severity'],
            'reason_category' => $validated['reason_category'],
            'reason' => $validated['reason'] ?? null,
            'internal_notes' => $validated['internal_notes'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'created_by' => Auth::id(),
            'last_violation_at' => now(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Záznam bol pridaný do blacklistu.',
                'entry' => $entry,
            ]);
        }

        return redirect()
            ->route('admin.blacklist.index')
            ->with('success', __('ui.zaznam_bol_uspesne_pridany_do_blacklistu'));
    }

    /**
     * Show form to edit blacklist entry
     */
    public function edit(BlacklistEntry $blacklist)
    {
        $entry = $blacklist;

        return view('admin.blacklist.edit', [
            'entry' => $entry,
            'severityOptions' => BlacklistEntry::getSeverityOptions(),
            'identifierTypeOptions' => BlacklistEntry::getIdentifierTypeOptions(),
            'reasonCategoryOptions' => BlacklistEntry::getReasonCategoryOptions(),
        ]);
    }

    /**
     * Update blacklist entry
     */
    public function update(Request $request, BlacklistEntry $blacklist)
    {
        $entry = $blacklist;

        $validated = $request->validate([
            'severity' => ['required', Rule::in(['warning', 'soft_ban', 'hard_ban'])],
            'reason_category' => ['required', Rule::in(array_keys(BlacklistEntry::getReasonCategoryOptions()))],
            'reason' => ['nullable', 'string', 'max:1000'],
            'internal_notes' => ['nullable', 'string', 'max:2000'],
            'expires_at' => ['nullable', 'date'],
            'is_active' => ['boolean'],
        ]);

        $entry->update([
            'severity' => $validated['severity'],
            'reason_category' => $validated['reason_category'],
            'reason' => $validated['reason'] ?? null,
            'internal_notes' => $validated['internal_notes'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'updated_by' => Auth::id(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Záznam bol aktualizovaný.',
                'entry' => $entry->fresh(),
            ]);
        }

        return redirect()
            ->route('admin.blacklist.index')
            ->with('success', __('ui.zaznam_bol_uspesne_aktualizovany'));
    }

    /**
     * Delete blacklist entry
     */
    public function destroy(Request $request, BlacklistEntry $blacklist)
    {
        $entry = $blacklist;
        $entry->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Záznam bol odstránený z blacklistu.',
            ]);
        }

        return redirect()
            ->route('admin.blacklist.index')
            ->with('success', __('ui.zaznam_bol_uspesne_odstraneny_z_blacklistu'));
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(Request $request, BlacklistEntry $blacklist)
    {
        $entry = $blacklist;

        $entry->update([
            'is_active' => ! $entry->is_active,
            'updated_by' => Auth::id(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $entry->is_active ? 'Záznam bol aktivovaný.' : 'Záznam bol deaktivovaný.',
                'is_active' => $entry->is_active,
            ]);
        }

        return back()->with('success', $entry->is_active ? 'Záznam bol aktivovaný.' : 'Záznam bol deaktivovaný.');
    }

    /**
     * API: Check if customer is blacklisted
     */
    public function check(Request $request)
    {
        $validated = $request->validate([
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string'],
        ]);

        if (empty($validated['email']) && empty($validated['phone'])) {
            return response()->json([
                'blacklisted' => false,
                'message' => 'No identifier provided.',
            ]);
        }

        $entry = BlacklistEntry::checkBlacklist(
            $validated['email'] ?? null,
            $validated['phone'] ?? null
        );

        if (! $entry) {
            return response()->json([
                'blacklisted' => false,
                'message' => 'Customer is not blacklisted.',
            ]);
        }

        return response()->json([
            'blacklisted' => true,
            'blocks_booking' => $entry->blocksBookings(),
            'can_override' => $entry->canBeOverridden(),
            'severity' => $entry->severity,
            'severity_label' => $entry->getSeverityLabel(),
            'reason_category' => $entry->reason_category,
            'reason_category_label' => $entry->getReasonCategoryLabel(),
            'reason' => $entry->reason,
            'expires_at' => $entry->expires_at?->toIso8601String(),
            'violation_count' => $entry->violation_count,
        ]);
    }

    /**
     * Get customer blacklist status for drawer
     */
    public function customerStatus(Request $request, string $email)
    {
        $email = urldecode($email);

        // Get phone from customer's bookings
        $phone = Booking::where('customer_email', $email)
            ->whereNotNull('customer_phone')
            ->value('customer_phone');

        $entries = BlacklistEntry::getAllEntriesFor($email, $phone);

        return response()->json([
            'email' => $email,
            'phone' => $phone,
            'is_blacklisted' => $entries->isNotEmpty(),
            'entries' => $entries->map(fn ($e) => [
                'id' => $e->id,
                'identifier_type' => $e->identifier_type,
                'identifier_value' => $e->identifier_value,
                'severity' => $e->severity,
                'severity_label' => $e->getSeverityLabel(),
                'reason_category' => $e->reason_category,
                'reason_category_label' => $e->getReasonCategoryLabel(),
                'reason' => $e->reason,
                'violation_count' => $e->violation_count,
                'expires_at' => $e->expires_at?->format('d.m.Y H:i'),
                'is_active' => $e->is_active,
                'blocks_booking' => $e->blocksBookings(),
            ]),
        ]);
    }
}
