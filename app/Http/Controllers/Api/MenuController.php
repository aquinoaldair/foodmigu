<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    public function index(): JsonResponse
    {
        $menus = Menu::with('categories')->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $menus,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'is_active' => ['boolean'],
            'categories' => ['array'],
            'categories.*.id' => ['required', 'exists:menu_categories,id'],
            'categories.*.display_order' => ['nullable', 'integer'],
        ]);

        $validated['is_active'] = $validated['is_active'] ?? true;

        try {
            DB::beginTransaction();

            $menu = Menu::create([
                'name' => $validated['name'],
                'is_active' => $validated['is_active'],
            ]);

            $this->syncCategories($menu, $validated['categories'] ?? []);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $menu->load('categories'),
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function show(Menu $menu): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $menu->load('categories'),
        ]);
    }

    public function update(Request $request, Menu $menu): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'is_active' => ['boolean'],
            'categories' => ['array'],
            'categories.*.id' => ['required', 'exists:menu_categories,id'],
            'categories.*.display_order' => ['nullable', 'integer'],
        ]);

        $validated['is_active'] = $validated['is_active'] ?? true;

        try {
            DB::beginTransaction();

            $menu->update([
                'name' => $validated['name'],
                'is_active' => $validated['is_active'],
            ]);

            $this->syncCategories($menu, $validated['categories'] ?? []);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $menu->fresh()->load('categories'),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy(Menu $menu): JsonResponse
    {
        $menu->delete();

        return response()->json([
            'success' => true,
            'data' => null,
        ]);
    }

    private function syncCategories(Menu $menu, array $categories): void
    {
        $syncData = [];
        foreach ($categories as $item) {
            $syncData[$item['id']] = [
                'display_order' => $item['display_order'] ?? 0,
            ];
        }
        $menu->categories()->sync($syncData);
    }
}
