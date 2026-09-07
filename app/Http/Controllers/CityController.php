<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\City;
use App\Models\User;
use App\Support\PlanLimits;
use App\Support\Tenancy;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CityController extends Controller
{
    public function index()
    {
        return view('admin.cities.index', ['cities' => City::with(['categories', 'workers'])->orderBy('name')->get()]);
    }

    public function create()
    {
        return view('admin.cities.create', ['categories' => Category::orderBy('name')->get(), 'workers' => User::where('role', 'worker')->with('city:id,name')->orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        if (! PlanLimits::currentCanAddLocation()) {
            return redirect()->route('admin.cities.index')->with('error', __('admin.pro.locations'));
        }

        $data = $this->validated($request);
        $city = City::create(['name' => $data['name'], 'address' => $data['address'] ?? null]);
        $city->categories()->sync($data['categories'] ?? []);
        $this->assignWorkers($city, $data['workers'] ?? []);

        return redirect()->route('admin.cities.index')->with('success', __('ui.mesto_bolo_vytvorene'));
    }

    public function edit(City $city)
    {
        return view('admin.cities.edit', ['city' => $city->load(['categories', 'workers']), 'categories' => Category::orderBy('name')->get(), 'workers' => User::where('role', 'worker')->with('city:id,name')->orderBy('name')->get()]);
    }

    public function update(Request $request, City $city)
    {
        $data = $this->validated($request, $city);
        $city->update(['name' => $data['name'], 'address' => $data['address'] ?? null]);
        $city->categories()->sync($data['categories'] ?? []);
        // Unassign only the people that were removed from this location.
        User::where('city_id', $city->id)->whereNotIn('id', $data['workers'] ?? [])->update(['city_id' => null]);
        $this->assignWorkers($city, $data['workers'] ?? []);

        return $request->wantsJson() ? response()->json(['city' => $city->fresh()]) : redirect()->route('admin.cities.index')->with('success', __('ui.mesto_bolo_upravene'));
    }

    public function destroy(City $city)
    {
        $city->categories()->detach();
        User::where('city_id', $city->id)->update(['city_id' => null]);
        $city->delete();

        return redirect()->route('admin.cities.index')->with('success', __('ui.mesto_bolo_vymazane'));
    }

    private function validated(Request $request, ?City $city = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('cities', 'name')->where('tenant_id', Tenancy::id())->ignore($city?->id)],
            'address' => 'nullable|string|max:255',
            'categories' => 'nullable|array', 'categories.*' => ['integer', Rule::exists('categories', 'id')->where('tenant_id', Tenancy::id())],
            'workers' => 'nullable|array', 'workers.*' => ['integer', Rule::exists('users', 'id')->where('tenant_id', Tenancy::id())],
        ]);
    }

    private function assignWorkers(City $city, array $workerIds): void
    {
        User::where('role', 'worker')->whereIn('id', $workerIds)->update(['city_id' => $city->id]);
    }
}
