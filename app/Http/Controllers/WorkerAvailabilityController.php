<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WorkerAvailability;
use App\Support\Tenancy;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WorkerAvailabilityController extends Controller
{
    public function index(User $worker)
    {
        abort_unless($worker->is_worker, 404);

        return view('admin.workers.availability', ['worker' => $worker, 'availabilities' => $worker->availability()->latest('start_date')->get()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $worker = User::findOrFail($request->integer('user_id'));
        abort_unless($worker->is_worker, 422, 'Zvolený používateľ nie je pracovník.');
        $this->createAvailability($worker, $data);

        return back()->with('success', __('ui.dostupnost_bola_pridana'));
    }

    public function edit(WorkerAvailability $availability)
    {
        return response()->json($availability);
    }

    public function update(Request $request, WorkerAvailability $availability)
    {
        $availability->update($this->validated($request));

        return back()->with('success', __('ui.dostupnost_bola_upravena'));
    }

    public function destroy(WorkerAvailability $availability)
    {
        $availability->delete();

        return back()->with('success', __('ui.dostupnost_bola_odstranena'));
    }

    public function workerIndex(Request $request)
    {
        $worker = $request->user();

        return view('worker.availability', ['worker' => $worker, 'availabilities' => $worker->availability()->latest('start_date')->get()]);
    }

    public function workerStore(Request $request)
    {
        $availability = $this->createAvailability($request->user(), $this->validated($request));

        return $request->expectsJson() ? response()->json(['success' => true, 'availability' => $availability], 201) : back()->with('success', __('ui.dostupnost_bola_pridana'));
    }

    public function workerEdit(Request $request, WorkerAvailability $availability)
    {
        $this->assertOwner($request, $availability);

        return response()->json($availability);
    }

    public function workerUpdate(Request $request, WorkerAvailability $availability)
    {
        $this->assertOwner($request, $availability);
        $availability->update($this->validated($request));

        return $request->expectsJson() ? response()->json(['success' => true, 'availability' => $availability]) : back()->with('success', __('ui.dostupnost_bola_upravena'));
    }

    public function workerDestroy(Request $request, WorkerAvailability $availability)
    {
        $this->assertOwner($request, $availability);
        $availability->delete();

        return $request->expectsJson() ? response()->json(['success' => true]) : back()->with('success', __('ui.dostupnost_bola_odstranena'));
    }

    public function checkAvailability(Request $request, User $worker)
    {
        abort_unless($worker->is_worker, 404);
        $data = $request->validate(['date' => 'required|date', 'time' => 'required|date_format:H:i']);

        return response()->json(['available' => $worker->isAvailableAt($data['date'], $data['time']), 'worker_id' => $worker->id, ...$data]);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'user_id' => ['nullable', 'integer', Rule::exists('users', 'id')->where('tenant_id', Tenancy::id())], 'start_date' => 'required|date', 'end_date' => 'nullable|date|after_or_equal:start_date',
            'days_of_week' => 'required|array|min:1', 'days_of_week.*' => 'integer|between:0,6', 'start_time' => 'required|date_format:H:i', 'end_time' => 'required|date_format:H:i|after:start_time',
            'repeat_until_end_of_year' => 'nullable|boolean', 'is_active' => 'nullable|boolean', 'notes' => 'nullable|string|max:1000',
        ]);
        unset($data['user_id']);
        if ($request->boolean('repeat_until_end_of_year')) {
            $data['end_date'] = now()->endOfYear()->toDateString();
        }
        $data['repeat_until_end_of_year'] = $request->boolean('repeat_until_end_of_year');
        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }

    private function createAvailability(User $worker, array $data): WorkerAvailability
    {
        abort_unless($worker->is_worker, 403);

        return $worker->availability()->create($data);
    }

    private function assertOwner(Request $request, WorkerAvailability $availability): void
    {
        abort_unless($availability->user_id === $request->user()->id, 403);
    }
}
