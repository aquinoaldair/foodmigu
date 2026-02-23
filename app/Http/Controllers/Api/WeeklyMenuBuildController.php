<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WeeklyMenuBuild;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WeeklyMenuBuildController extends Controller
{
    public function index(): JsonResponse
    {
        $builds = WeeklyMenuBuild::with('menu', 'days', 'diningHalls')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $builds,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string'],
            'menu_id' => ['required', 'exists:menus,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'dining_halls' => ['array'],
            'dining_halls.*' => ['exists:dining_halls,id'],
        ]);

        $build = WeeklyMenuBuild::create([
            'title' => $validated['title'],
            'menu_id' => $validated['menu_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => WeeklyMenuBuild::STATUS_DRAFT,
        ]);

        $build->diningHalls()->sync($request->dining_halls ?? []);

        return response()->json([
            'success' => true,
            'data' => $build->load(['menu', 'diningHalls']),
        ], 201);
    }

    public function show(WeeklyMenuBuild $weeklyMenuBuild): JsonResponse
    {
        $weeklyMenuBuild->load(['menu.categories', 'days.items.menuCategory']);

        return response()->json([
            'success' => true,
            'data' => $weeklyMenuBuild,
        ]);
    }

    public function update(Request $request, WeeklyMenuBuild $weeklyMenuBuild): JsonResponse
    {
        if ($weeklyMenuBuild->status === WeeklyMenuBuild::STATUS_PUBLISHED) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede modificar un menú ya publicado.',
            ], 403);
        }

        $validated = $request->validate([
            'title' => ['nullable', 'string'],
            'days' => ['array'],
            'days.*.date' => ['required', 'date'],
            'days.*.items' => ['array'],
            'days.*.items.*.menu_category_id' => ['required', 'exists:menu_categories,id'],
            'days.*.items.*.name' => ['required', 'string'],
            'days.*.items.*.description' => ['nullable', 'string'],
            'days.*.items.*.price' => ['nullable', 'numeric'],
            'days.*.items.*.display_order' => ['nullable', 'integer'],
        ]);

        try {
            DB::beginTransaction();

            if (!empty($validated['title'])) {
                $weeklyMenuBuild->update(['title' => $validated['title']]);
            }

            if (isset($validated['days'])) {
                foreach ($validated['days'] as $dayData) {
                    $date = $dayData['date'];
                    $day = $weeklyMenuBuild->days()->firstOrCreate(
                        ['date' => $date],
                        ['date' => $date]
                    );

                    $items = [];
                    foreach ($dayData['items'] ?? [] as $index => $itemData) {
                        $items[] = [
                            'menu_category_id' => $itemData['menu_category_id'],
                            'name' => $itemData['name'],
                            'description' => $itemData['description'] ?? null,
                            'price' => $itemData['price'] ?? null,
                            'display_order' => $itemData['display_order'] ?? $index,
                        ];
                    }

                    $day->items()->delete();
                    foreach ($items as $item) {
                        $day->items()->create($item);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $weeklyMenuBuild->fresh()->load(['menu.categories', 'days.items.menuCategory']),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateDiningHalls(Request $request, WeeklyMenuBuild $weeklyMenuBuild): JsonResponse
    {
        if ($weeklyMenuBuild->status === WeeklyMenuBuild::STATUS_PUBLISHED) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede modificar un menú ya publicado.',
            ], 403);
        }

        $validated = $request->validate([
            'dining_halls' => ['required', 'array'],
            'dining_halls.*' => ['integer', 'exists:dining_halls,id'],
        ]);

        $weeklyMenuBuild->diningHalls()->sync($validated['dining_halls']);

        return response()->json([
            'success' => true,
            'data' => $weeklyMenuBuild->fresh()->load(['menu.categories', 'days.items.menuCategory', 'diningHalls']),
        ]);
    }

    public function destroy(WeeklyMenuBuild $weeklyMenuBuild): JsonResponse
    {
        $weeklyMenuBuild->delete();

        return response()->json([
            'success' => true,
            'data' => null,
        ]);
    }

    public function publish(WeeklyMenuBuild $weeklyMenuBuild): JsonResponse
    {
        if ($weeklyMenuBuild->status === WeeklyMenuBuild::STATUS_PUBLISHED) {
            return response()->json([
                'success' => false,
                'message' => 'Este menú ya está publicado.',
            ], 400);
        }

        $weeklyMenuBuild->update([
            'status' => WeeklyMenuBuild::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $weeklyMenuBuild->fresh()->load(['menu', 'days']),
        ]);
    }
}
