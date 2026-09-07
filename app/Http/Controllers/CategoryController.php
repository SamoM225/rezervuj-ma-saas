<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\City;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request, $cityId = null)
    {
        return response()->json($this->categoriesForCity($cityId));
    }

    /** Categories offered at a location, for the public booking flow. */
    public function webCategories(Request $request, $cityId = null)
    {
        return response()->json($this->categoriesForCity($cityId)->map(fn (Category $category) => [
            'id' => $category->id,
            'name' => $category->name,
            'description' => $category->description,
        ])->values());
    }

    public function adminIndex()
    {
        return view('admin.categories.index', [
            'categories' => Category::with('cities')->withCount(['services', 'users'])->orderBy('name')->get(),
            'cities' => City::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $category = Category::create(['name' => $data['name'], 'description' => $data['description'] ?? null, 'city' => '']);
        $category->cities()->sync($data['cities'] ?? []);

        return $request->wantsJson()
            ? response()->json(['category' => $category], 201)
            : redirect()->route('admin.categories')->with('success', __('ui.kategoria').$category->name.'“ bola vytvorená.');
    }

    public function edit(Category $category)
    {
        $category->load(['cities', 'services' => fn ($query) => $query->orderBy('name')]);

        return view('admin.categories.edit', [
            'category' => $category,
            'cities' => City::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $data = $this->validated($request);
        $category->update(['name' => $data['name'], 'description' => $data['description'] ?? null]);
        $category->cities()->sync($data['cities'] ?? []);

        return redirect()->route('admin.categories')->with('success', __('ui.kategoria_bola_upravena'));
    }

    public function destroy(Category $category)
    {
        $name = $category->name;
        $category->cities()->detach();
        $category->users()->detach();
        $category->delete();

        return redirect()->route('admin.categories')->with('success', __('ui.kategoria').$name.'“ bola vymazaná.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'cities' => 'nullable|array',
            'cities.*' => 'integer|exists:cities,id',
        ]);
    }

    private function categoriesForCity($cityId)
    {
        return Category::query()
            ->when($cityId, fn ($query) => $query->whereHas('cities', fn ($cities) => $cities->whereKey($cityId)))
            ->orderBy('name')
            ->get();
    }
}
