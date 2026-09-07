<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BusinessSetting;
use App\Models\Schedule;
use App\Models\User;
use App\Models\WorkerAvailability;
use App\Support\Tenancy;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class CalendarController extends Controller
{
    /**
     * Return calendar feed data for bookings and manual blocks.
     */
    public function feed(Request $request)
    {
        $request->validate([
            'start' => 'nullable|date',
            'end' => 'nullable|date|after_or_equal:start',
            'worker_ids' => 'sometimes|array',
            'worker_ids.*' => ['integer', Rule::exists('users', 'id')->where('tenant_id', Tenancy::id())],
            'statuses' => 'sometimes|array',
            'statuses.*' => 'string|in:pending,confirmed,cancelled,completed',
            'search' => 'nullable|string|max:255',
        ]);

        $user = $request->user();

        $startDate = $request->filled('start')
            ? Carbon::parse($request->input('start'))->toDateString()
            : now()->subMonthsNoOverflow(1)->toDateString();

        $endDate = $request->filled('end')
            ? Carbon::parse($request->input('end'))->toDateString()
            : now()->addMonthsNoOverflow(1)->toDateString();

        $workerIds = collect($request->input('worker_ids', []))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->values();

        $bookingQuery = Booking::query()
            ->with(['service:id,name,duration,price', 'worker:id,name,calendar_color'])
            ->whereBetween('date', [$startDate, $endDate]);

        if (! $user->canViewAllBookings()) {
            $bookingQuery->where('user_id', $user->id);
        } elseif ($workerIds->isNotEmpty()) {
            $bookingQuery->whereIn('user_id', $workerIds);
        }

        if ($statuses = $request->input('statuses')) {
            $bookingQuery->whereIn('status', $statuses);
        }

        if ($search = $request->input('search')) {
            $bookingQuery->where(function ($query) use ($search) {
                $query->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhereHas('service', function ($serviceQuery) use ($search) {
                        $serviceQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $bookings = $bookingQuery
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        $blockQuery = Schedule::with(['worker:id,name,calendar_color'])
            ->whereBetween('date', [$startDate, $endDate])
            ->whereIn('status', ['unavailable', 'blocked']);

        if (! $user->canViewAllBookings()) {
            $blockQuery->where('user_id', $user->id);
        } elseif ($workerIds->isNotEmpty()) {
            $blockQuery->whereIn('user_id', $workerIds);
        }

        $blocks = $blockQuery
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        // When the calendar shows exactly one person, their working windows
        // become the calendar's business hours so free time is visible at a glance.
        $scopedWorkerId = ! $user->canViewAllBookings() ? $user->id : ($workerIds->count() === 1 ? $workerIds->first() : null);
        $businessHours = $scopedWorkerId ? $this->workerBusinessHours((int) $scopedWorkerId, $startDate, $endDate) : null;

        $holidays = collect((array) BusinessSetting::get('business_holidays', []))
            ->filter(fn ($date) => $date >= $startDate && $date <= $endDate)
            ->values();

        return response()->json([
            'bookings' => $bookings,
            'blocks' => $blocks,
            'holidays' => $holidays,
            'business_hours' => $businessHours,
            'meta' => [
                'can_manage_all' => $user->canViewAllBookings(),
                'range' => [
                    'start' => $startDate,
                    'end' => $endDate,
                ],
            ],
        ]);
    }

    /**
     * FullCalendar businessHours entries for one worker's active availability
     * rules that touch the requested range.
     *
     * @return array<int, array{daysOfWeek: array<int>, startTime: string, endTime: string}>
     */
    private function workerBusinessHours(int $workerId, string $startDate, string $endDate): array
    {
        return WorkerAvailability::query()
            ->where('user_id', $workerId)
            ->where('is_active', true)
            ->whereDate('start_date', '<=', $endDate)
            ->where(fn ($query) => $query->whereNull('end_date')->orWhereDate('end_date', '>=', $startDate))
            ->get()
            ->map(fn (WorkerAvailability $rule) => [
                'daysOfWeek' => array_values(array_map('intval', (array) $rule->days_of_week)),
                'startTime' => Carbon::parse($rule->start_time)->format('H:i'),
                'endTime' => Carbon::parse($rule->end_time)->format('H:i'),
            ])
            ->values()
            ->all();
    }

    /**
     * Block time for a worker: one time window on one day, or (for holidays)
     * a range of days, optionally the whole working day of each.
     */
    public function storeBlock(Request $request)
    {
        $request->validate([
            'worker_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:date',
            'all_day' => 'nullable|boolean',
            'start_time' => 'nullable|date_format:H:i|required_without:all_day',
            'end_time' => 'nullable|date_format:H:i|required_without:all_day',
            'title' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:2000',
        ]);

        $user = $request->user();
        $workerId = (int) $request->input('worker_id');

        if (! $user->canViewAllBookings() && $user->id !== $workerId) {
            abort(403, 'Nemáte oprávnenie spravovať kalendár iného pracovníka.');
        }

        $allDay = $request->boolean('all_day');
        if (! $allDay && (! $request->filled('start_time') || ! $request->filled('end_time') || $request->input('end_time') <= $request->input('start_time'))) {
            return response()->json(['message' => 'Zadajte začiatok a koniec, koniec musí byť po začiatku.'], 422);
        }

        $startTime = $this->normalizeTime($allDay ? BusinessSetting::get('business_start_time', '08:00') : $request->input('start_time'));
        $endTime = $this->normalizeTime($allDay ? BusinessSetting::get('business_end_time', '18:00') : $request->input('end_time'));
        $firstDay = Carbon::parse($request->input('date'))->startOfDay();
        $lastDay = Carbon::parse($request->input('end_date') ?: $request->input('date'))->startOfDay();

        if ($firstDay->diffInDays($lastDay) > 92) {
            return response()->json(['message' => 'Naraz je možné blokovať najviac tri mesiace.'], 422);
        }

        $days = collect();
        for ($day = $firstDay->copy(); $day->lte($lastDay); $day->addDay()) {
            $days->push($day->toDateString());
        }

        $conflicts = Booking::where('user_id', $workerId)
            ->whereIn('date', $days)
            ->where('status', '!=', 'cancelled')
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            ->pluck('date')
            ->map(fn ($date) => Carbon::parse($date)->format('j. n.'))
            ->unique();

        if ($conflicts->isNotEmpty()) {
            return response()->json([
                'message' => 'V tomto čase už sú rezervácie ('.$conflicts->implode(', ').'). Najprv ich presuňte alebo zrušte.',
            ], 422);
        }

        $alreadyBlocked = Schedule::where('user_id', $workerId)
            ->whereIn('date', $days)
            ->whereIn('status', ['unavailable', 'blocked'])
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            ->pluck('date')
            ->map(fn ($date) => Carbon::parse($date)->toDateString());

        $worker = User::findOrFail($workerId);
        $title = $request->input('title') ?: ($allDay ? 'Zatvorené' : 'Blokované');
        $created = collect();

        foreach ($days as $date) {
            if ($alreadyBlocked->contains($date)) {
                continue;
            }
            $created->push(Schedule::create([
                'user_id' => $workerId,
                'date' => $date,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => 'unavailable',
                'type' => 'manual-block',
                'title' => $title,
                'notes' => $request->input('notes'),
                'city' => optional($worker->city)->name ?? 'Neuvedené',
                'created_by' => $user->id,
            ]));
        }

        if ($created->isEmpty()) {
            return response()->json(['message' => 'Tento čas je už blokovaný.'], 422);
        }

        return response()->json([
            'blocks' => $created->map(fn (Schedule $block) => $block->load('worker:id,name,calendar_color')),
            'block' => $created->first(),
            'message' => $created->count() > 1 ? 'Blokovaných dní: '.$created->count().'.' : 'Časový blok bol pridaný.',
        ], 201);
    }

    /**
     * Move a manual block (drag & drop in the calendar).
     */
    public function updateBlock(Request $request, Schedule $block)
    {
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
        ]);

        $user = $request->user();
        if ($block->type !== 'manual-block' || ! in_array($block->status, ['unavailable', 'blocked'], true)) {
            abort(403, 'Tento záznam nemožno presunúť.');
        }
        if (! $user->canViewAllBookings() && $user->id !== $block->user_id) {
            abort(403, 'Nemáte oprávnenie presunúť tento blok.');
        }

        $date = Carbon::parse($request->input('date'))->toDateString();
        $start = $this->normalizeTime($request->input('start_time'));
        $duration = Carbon::parse($block->start_time)->diffInMinutes(Carbon::parse($block->end_time));
        $end = $request->filled('end_time')
            ? $this->normalizeTime($request->input('end_time'))
            : Carbon::parse($start)->addMinutes($duration)->format('H:i:s');

        $overlap = Booking::where('user_id', $block->user_id)
            ->where('date', $date)
            ->where('status', '!=', 'cancelled')
            ->where('start_time', '<', $end)
            ->where('end_time', '>', $start)
            ->exists();
        if ($overlap) {
            return response()->json(['message' => 'V tomto čase už je rezervácia.'], 422);
        }

        $block->update(['date' => $date, 'start_time' => $start, 'end_time' => $end]);

        return response()->json(['block' => $block->fresh('worker:id,name,calendar_color'), 'message' => 'Blok bol presunutý.']);
    }

    /**
     * Remove manual block from calendar.
     */
    public function destroyBlock(Request $request, Schedule $block)
    {
        $user = $request->user();

        if (! in_array($block->status, ['unavailable', 'blocked'], true) || $block->type !== 'manual-block') {
            abort(403, 'Tento záznam nemožno odstrániť z kalendára.');
        }

        if (! $user->canViewAllBookings() && $user->id !== $block->user_id) {
            abort(403, 'Nemáte oprávnenie odstrániť tento blok.');
        }

        $block->delete();

        return response()->json(['message' => 'Blok bol odstránený.']);
    }

    private function normalizeTime(string $time): string
    {
        return Carbon::parse($time)->format('H:i:s');
    }
}
