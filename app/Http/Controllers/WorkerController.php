<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\City;
use App\Models\Service;
use App\Models\User;
use App\Support\Tenancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class WorkerController extends Controller
{
    public function index()
    {
        return response()->json($this->workers()->get(['id', 'name', 'email', 'calendar_color', 'city_id']));
    }

    public function adminIndex()
    {
        return view('admin.workers.index', ['workers' => User::whereIn('role', ['worker', 'admin', 'superadmin'])->with(['city', 'categories'])->get()]);
    }

    public function create()
    {
        return view('admin.workers.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->where('tenant_id', Tenancy::id())],
            'password' => 'required|string|min:12|max:255',
            'city_id' => ['nullable', Rule::exists('cities', 'id')->where('tenant_id', Tenancy::id())],
            'role' => 'required|in:worker,admin,superadmin',
            'categories' => 'nullable|array',
            'categories.*' => ['integer', Rule::exists('categories', 'id')->where('tenant_id', Tenancy::id())],
            'services' => 'nullable|array',
            'services.*' => ['integer', Rule::exists('services', 'id')->where('tenant_id', Tenancy::id())],
            'calendar_color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'avatar' => 'nullable|image|max:4096',
        ]);
        $this->authorizeRoleAssignment($request->user(), $data['role']);

        if ($request->hasFile('avatar')) {
            $data['avatar_path'] = $request->file('avatar')->store('tenants/'.Tenancy::id().'/avatars', 'public');
        }
        $data['password'] = Hash::make($data['password']);
        $data['calendar_color'] = $data['calendar_color'] ?? self::PALETTE[User::count() % count(self::PALETTE)];
        [$categories, $services] = [$data['categories'] ?? [], $data['services'] ?? []];
        unset($data['categories'], $data['services']);
        $worker = User::create($data);
        $this->syncAssignments($worker, $categories, $services);

        return $request->wantsJson()
            ? response()->json(['user' => $worker], 201)
            : redirect()->route('admin.workers')->with('success', __('ui.pouzivatel_bol_vytvoreny'));
    }

    public function edit(User $worker)
    {
        $this->authorizeTarget($worker, request()->user());
        $worker->load(['categories', 'services']);

        return view('admin.workers.edit', ['worker' => $worker] + $this->formData());
    }

    public function update(Request $request, User $worker)
    {
        $this->authorizeTarget($worker, $request->user());
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$worker->id,
            'password' => 'nullable|string|min:12|max:255',
            'city_id' => 'nullable|exists:cities,id',
            'role' => 'required|in:worker,admin,superadmin',
            'categories' => 'nullable|array',
            'categories.*' => 'integer|exists:categories,id',
            'services' => 'nullable|array',
            'services.*' => 'integer|exists:services,id',
            'calendar_color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'avatar' => 'nullable|image|max:4096',
            'remove_avatar' => 'nullable|boolean',
        ]);
        $this->authorizeRoleAssignment($request->user(), $data['role']);

        [$categories, $services] = [$data['categories'] ?? [], $data['services'] ?? []];
        unset($data['categories'], $data['services']);
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
            $worker->tokens()->delete();
        }
        if ($request->hasFile('avatar')) {
            if ($worker->avatar_path) {
                Storage::disk('public')->delete($worker->avatar_path);
            }
            $data['avatar_path'] = $request->file('avatar')->store('avatars', 'public');
        } elseif ($request->boolean('remove_avatar')) {
            if ($worker->avatar_path) {
                Storage::disk('public')->delete($worker->avatar_path);
            }
            $data['avatar_path'] = null;
        }
        unset($data['remove_avatar']);
        $worker->update($data);
        $this->syncAssignments($worker, $categories, $services);

        return $request->wantsJson() ? response()->json(['user' => $worker->fresh()]) : redirect()->route('admin.workers')->with('success', __('ui.pouzivatel_bol_upraveny'));
    }

    public function delete(Request $request, User $worker)
    {
        $this->authorizeTarget($worker, $request->user());
        abort_if($worker->is($request->user()), 422, 'Nie je možné odstrániť vlastný účet.');
        $worker->delete();

        return $request->wantsJson() ? response()->json(status: 204) : redirect()->route('admin.workers')->with('success', __('ui.pouzivatel_bol_odstraneny'));
    }

    public function getWorkers($categoryId = null)
    {
        $workers = $this->workers()
            ->when($categoryId, fn ($query) => $query->whereHas('categories', fn ($categories) => $categories->whereKey($categoryId)))
            ->with(['city', 'categories', 'services'])->get();

        return response()->json(['workers' => $workers]);
    }

    public function getWorkersByService($serviceId)
    {
        return response()->json($this->workers()->whereHas('services', fn ($query) => $query->whereKey($serviceId))->with(['city', 'categories', 'services'])->get());
    }

    public function getCities()
    {
        return response()->json(City::orderBy('name')->get());
    }

    public function getCitiesWeb()
    {
        return response()->json(City::orderBy('name')->get(['id', 'name', 'address']));
    }

    /**
     * Staff for the public booking flow. Narrowed to the chosen service and
     * location so every listed person can actually take the booking.
     */
    public function getWorkersWeb(Request $request, $categoryId)
    {
        $workers = $this->workers()
            ->whereHas('categories', fn ($query) => $query->whereKey($categoryId))
            ->when($request->integer('service_id'), fn ($query, $serviceId) => $query->whereHas('services', fn ($services) => $services->whereKey($serviceId)))
            ->when($request->integer('city_id'), fn ($query, $cityId) => $query->where('city_id', $cityId))
            ->with('city:id,name')
            ->orderBy('name')
            ->get()
            ->map(fn (User $worker) => [
                'id' => $worker->id,
                'name' => $worker->name,
                'calendar_color' => $worker->calendar_color,
                'avatar_url' => $worker->avatar_path ? Storage::url($worker->avatar_path) : null,
                'city' => $worker->city?->name,
            ])
            ->values();

        return response()->json($workers);
    }

    public function updateColor(Request $request, User $worker)
    {
        $this->authorizeTarget($worker, $request->user());
        $data = $request->validate(['color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/']);
        $worker->update(['calendar_color' => $data['color']]);

        return response()->json(['color' => $worker->calendar_color]);
    }

    /** Distinct, print-friendly calendar colours handed out to new staff. */
    private const PALETTE = ['#C19A3E', '#D98AA8', '#5B8DB8', '#6FA287', '#B86A5B', '#8B79B5', '#C98A3A', '#4F8F9E'];

    private function workers()
    {
        return User::where('role', 'worker');
    }

    /** Shared data for the create/edit forms. */
    private function formData(): array
    {
        return [
            'cities' => City::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->with(['services' => fn ($query) => $query->orderBy('name')])->get(),
            'palette' => self::PALETTE,
        ];
    }

    /**
     * Staff offer services; a category is implied by the services picked so the
     * public flow (category → worker) and booking validation (worker → service)
     * always agree.
     */
    private function syncAssignments(User $worker, array $categoryIds, array $serviceIds): void
    {
        if ($worker->role !== 'worker') {
            $worker->categories()->sync([]);
            $worker->services()->sync([]);

            return;
        }

        $implied = Service::whereIn('id', $serviceIds)->pluck('category_id')->filter()->unique();
        $worker->services()->sync($serviceIds);
        $worker->categories()->sync(collect($categoryIds)->merge($implied)->unique()->values()->all());
    }

    private function authorizeTarget(User $target, User $actor): void
    {
        if ($target->role !== 'worker' && $actor->role !== 'superadmin') {
            abort(403, __('ui.len_superadmin_moze_spravovat_administratorske_ucty'));
        }
    }

    private function authorizeRoleAssignment(User $actor, string $role): void
    {
        if ($role !== 'worker' && $actor->role !== 'superadmin') {
            abort(403, __('ui.len_superadmin_moze_vytvorit_alebo_povysit'));
        }
    }
}
