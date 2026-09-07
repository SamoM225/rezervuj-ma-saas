<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BusinessSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class WorkerDashboardController extends Controller
{
    public function index()
    {
        $worker = Auth::user();
        $requireConfirmation = BusinessSetting::get('require_confirmation', false);

        $baseQuery = Booking::where('user_id', $worker->id);

        $stats = [
            'total_bookings' => (clone $baseQuery)->count(),
            'upcoming_bookings' => (clone $baseQuery)->where('date', '>=', Carbon::today()->toDateString())->count(),
            'today_bookings' => (clone $baseQuery)->where('date', Carbon::today()->toDateString())->count(),
            'pending_bookings' => $requireConfirmation ? (clone $baseQuery)->where('status', 'pending')->count() : 0,
            'completed_bookings' => (clone $baseQuery)->where('status', 'completed')->count(),
            'cancelled_bookings' => (clone $baseQuery)->where('status', 'cancelled')->count(),
        ];

        $recentBookings = (clone $baseQuery)
            ->with('service')
            ->orderByDesc('date')
            ->orderByDesc('start_time')
            ->take(5)
            ->get();

        $upcomingBookings = (clone $baseQuery)
            ->with('service')
            ->where('date', '>=', Carbon::today()->toDateString())
            ->orderBy('date')
            ->orderBy('start_time')
            ->take(5)
            ->get();

        $bookingChartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $bookingChartData[] = [
                'date' => $date->format('d.m'),
                'bookings' => (clone $baseQuery)->where('date', $date->format('Y-m-d'))->count(),
            ];
        }

        return view('worker.dashboard', [
            'worker' => $worker,
            'stats' => $stats,
            'recentBookings' => $recentBookings,
            'upcomingBookings' => $upcomingBookings,
            'bookingChartData' => $bookingChartData,
            'requireConfirmation' => $requireConfirmation,
        ]);
    }

    public function bookings()
    {
        return redirect()->route('worker.calendar')
            ->with('status', __('ui.kalendar_teraz_zobrazuje_vsetky_vase_rezervacie'));
    }

    public function calendar()
    {
        $worker = Auth::user();

        $services = $worker->services()
            ->select('services.id', 'services.name', 'services.duration', 'services.price', 'services.category_id')
            ->orderBy('services.name')
            ->get();

        return view('worker.calendar', [
            'worker' => $worker,
            'workers' => collect([[
                'id' => $worker->id,
                'name' => $worker->name,
                'calendar_color' => $worker->calendar_color,
                'service_ids' => $services->pluck('id')->values(),
            ]]),
            'services' => $services,
            'businessHours' => [
                'start' => BusinessSetting::get('business_start_time', '08:00'),
                'end' => BusinessSetting::get('business_end_time', '18:00'),
            ],
            'requireConfirmation' => (bool) BusinessSetting::get('require_confirmation', false),
        ]);
    }
}
