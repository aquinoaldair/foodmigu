<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MenuCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = MenuCategory::orderBy('display_order')->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'selection_type' => ['required', 'in:none,single,multiple'],
            'is_required' => ['boolean'],
            'is_active' => ['boolean'],
            'display_order' => ['nullable', 'integer'],
        ]);

        $validated['is_required'] = $validated['is_required'] ?? false;
        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['display_order'] = $validated['display_order'] ?? 0;

        $category = MenuCategory::create($validated);

        return response()->json([
            'success' => true,
            'data' => $category,
        ], 201);
    }

    public function show(MenuCategory $menuCategory): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $menuCategory,
        ]);
    }

    public function update(Request $request, MenuCategory $menuCategory): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'selection_type' => ['required', 'in:none,single,multiple'],
            'is_required' => ['boolean'],
            'is_active' => ['boolean'],
            'display_order' => ['nullable', 'integer'],
        ]);

        $validated['is_required'] = $validated['is_required'] ?? false;
        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['display_order'] = $validated['display_order'] ?? 0;

        $menuCategory->update($validated);

        return response()->json([
            'success' => true,
            'data' => $menuCategory->fresh(),
        ]);
    }

    public function destroy(MenuCategory $menuCategory): JsonResponse
    {
        $menuCategory->delete();

        return response()->json([
            'success' => true,
            'data' => null,
        ]);
    }
}
