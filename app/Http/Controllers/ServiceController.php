<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\City;
use App\Models\Service;
use App\Support\Tenancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    public function adminIndex()
    {
        return view('admin.services.index', ['services' => Service::with('category')->orderBy('name')->get()]);
    }

    public function create()
    {
        return view('admin.services.create', ['categories' => Category::orderBy('name')->get(), 'cities' => City::orderBy('name')->get()]);
    }

    public function index()
    {
        return response()->json(['services' => Service::with('category')->orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $service = Service::create($this->data($request));

        return $request->wantsJson() ? response()->json(['service' => $service], 201) : redirect()->route('admin.services')->with('success', __('ui.sluzba_bola_vytvorena'));
    }

    public function edit(Service $service)
    {
        return view('admin.services.edit', ['service' => $service, 'categories' => Category::orderBy('name')->get(), 'cities' => City::orderBy('name')->get()]);
    }

    public function update(Request $request, Service $service)
    {
        $service->update($this->data($request, $service));

        return $request->wantsJson() ? response()->json(['service' => $service->fresh()]) : redirect()->route('admin.services')->with('success', __('ui.sluzba_bola_upravena'));
    }

    public function destroy(Service $service)
    {
        if ($service->image_path) {
            Storage::disk('public')->delete($service->image_path);
        }
        $service->delete();

        return redirect()->route('admin.services')->with('success', __('ui.sluzba_bola_vymazana'));
    }

    public function getProcedures($categoryId)
    {
        return response()->json(['services' => Service::where('category_id', $categoryId)->orWhereHas('categories', fn ($query) => $query->whereKey($categoryId))->get()]);
    }

    /** Services of one category for the public booking flow. */
    public function getProceduresWeb($categoryId)
    {
        $services = Service::where('category_id', $categoryId)
            ->orWhereHas('categories', fn ($query) => $query->whereKey($categoryId))
            ->orderBy('name')
            ->get()
            ->map(fn (Service $service) => [
                'id' => $service->id,
                'name' => $service->name,
                'description' => $service->description,
                'duration' => (int) $service->duration,
                'price' => is_numeric($service->price) ? (float) $service->price : null,
                'price_label' => is_numeric($service->price) ? number_format((float) $service->price, 2, ',', ' ').' €' : (string) $service->price,
                'image_url' => $service->image_url,
            ])
            ->values();

        return response()->json($services);
    }

    private function data(Request $request, ?Service $service = null): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => ['nullable', Rule::exists('categories', 'id')->where('tenant_id', Tenancy::id())],
            'price' => 'required|numeric|min:0|max:99999.99',
            'duration' => 'required|integer|min:5|max:600',
            'break_time' => 'required|integer|min:0|max:180',
            'city' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|max:4096',
            'remove_image' => 'nullable|boolean',
        ]);
        if ($request->hasFile('image')) {
            if ($service?->image_path) {
                Storage::disk('public')->delete($service->image_path);
            }
            $data['image_path'] = $request->file('image')->store('tenants/'.Tenancy::id().'/services', 'public');
        } elseif ($service && $request->boolean('remove_image')) {
            if ($service->image_path) {
                Storage::disk('public')->delete($service->image_path);
            }
            $data['image_path'] = null;
        }
        unset($data['image'], $data['remove_image']);
        $data['city'] = $data['city'] ?? '';

        return $data;
    }
}
