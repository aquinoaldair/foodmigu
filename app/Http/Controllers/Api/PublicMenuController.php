<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Models\Diner;
use App\Models\DiningHall;
use App\Models\MenuCategory;
use App\Models\WeeklyMenuBuild;
use App\Models\WeeklyMenuDay;
use App\Models\WeeklyMenuSelection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PublicMenuController
{
    private function findDiningHall(string $code): ?DiningHall
    {
        return DiningHall::where('code', $code)->where('is_active', true)->first();
    }

    public function hall(string $code): JsonResponse
    {
        $hall = $this->findDiningHall($code);
        if (!$hall) {
            return response()->json(['message' => 'Comedor no encontrado o inactivo'], 404);
        }
        return response()->json([
            'id' => $hall->id,
            'name' => $hall->name,
            'code' => $hall->code,
            'description' => $hall->description,
        ]);
    }

    public function identify(Request $request, string $code): JsonResponse
    {
        $hall = $this->findDiningHall($code);
        if (!$hall) {
            return response()->json(['message' => 'Comedor no encontrado o inactivo'], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_code' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $idCode = $request->input('id_code');
        $name = $request->input('name');

        $diners = Diner::where('dining_hall_id', $hall->id)
            ->where('id_code', 'LIKE', '%' . $idCode . '%')
            ->where('name', 'LIKE', '%' . $name . '%')
            ->get();

        if ($diners->isEmpty()) {
            return response()->json(['message' => 'No se encontró ningún comensal'], 404);
        }
        if ($diners->count() === 1) {
            return response()->json(['diner' => $diners->first(), 'multiple' => false]);
        }
        return response()->json(['diners' => $diners, 'multiple' => true]);
    }

    public function menus(string $code): JsonResponse
    {
        $hall = $this->findDiningHall($code);
        if (!$hall) {
            return response()->json(['message' => 'Comedor no encontrado o inactivo'], 404);
        }

        $today = Carbon::today();

        $builds = WeeklyMenuBuild::where('status', WeeklyMenuBuild::STATUS_PUBLISHED)
            ->whereHas('diningHalls', fn ($q) => $q->where('dining_halls.id', $hall->id))
            ->with([
                'days' => fn ($q) => $q->where('date', '>=', $today)->orderBy('date'),
                'days.items.menuCategory',
            ])
            ->orderByDesc('start_date')
            ->get();

        $builds = $builds
            ->map(function (WeeklyMenuBuild $build) {
                $build->days = $build->days->values();
                return $build;
            })
            ->filter(fn (WeeklyMenuBuild $b) => $b->days->isNotEmpty())
            ->values();

        return response()->json(['menu_builds' => $builds]);
    }

    public function dayDetail(string $dayId): JsonResponse
    {
        $day = WeeklyMenuDay::with(['weeklyMenuBuild.diningHalls', 'items.menuCategory'])
            ->find($dayId);
        if (!$day) {
            return response()->json(['message' => 'Día no encontrado'], 404);
        }

        $build = $day->weeklyMenuBuild;
        if ($build->status !== WeeklyMenuBuild::STATUS_PUBLISHED) {
            return response()->json(['message' => 'Menú no publicado'], 403);
        }

        $today = Carbon::today();
        if ($day->date->lt($today)) {
            return response()->json(['message' => 'Fecha no disponible'], 403);
        }

        return response()->json([
            'day' => $day,
            'deadline_passed' => false,
        ]);
    }

    public function select(Request $request, string $dayId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'diner_id' => ['required', 'integer', 'exists:diners,id'],
            'selections' => ['required', 'array'],
            'selections.*' => ['integer', 'exists:weekly_menu_day_items,id'],
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $day = WeeklyMenuDay::with(['weeklyMenuBuild.diningHalls', 'items.menuCategory'])
            ->find($dayId);
        if (!$day) {
            return response()->json(['message' => 'Día no encontrado'], 404);
        }

        $build = $day->weeklyMenuBuild;
        if ($build->status !== WeeklyMenuBuild::STATUS_PUBLISHED) {
            return response()->json(['message' => 'Menú no publicado'], 403);
        }

        $hallIds = $build->diningHalls->pluck('id')->toArray();
        $diner = Diner::find($request->input('diner_id'));
        if (!$diner || !in_array($diner->dining_hall_id, $hallIds)) {
            return response()->json(['message' => 'Comensal no asignado a este comedor'], 403);
        }

        $today = Carbon::today();
        if ($day->date->lt($today)) {
            return response()->json(['message' => 'Fecha no disponible'], 403);
        }

        $itemIds = collect($request->input('selections'));
        $items = $day->items->keyBy('id');

        $byCategory = $itemIds->map(fn ($id) => $items->get($id))->filter()->groupBy('menu_category_id');

        foreach ($day->items->groupBy('menu_category_id') as $categoryId => $categoryItems) {
            $category = $categoryItems->first()->menuCategory;
            if (!$category) continue;

            $selected = $byCategory->get($categoryId, collect());
            $selectedIds = $selected->pluck('id')->toArray();

            if ($category->selection_type === MenuCategory::TYPE_NONE) {
                if ($category->is_required && $selected->isEmpty()) {
                    return response()->json([
                        'message' => "La categoría \"{$category->name}\" es obligatoria",
                    ], 422);
                }
                continue;
            }
            if ($category->is_required && $selected->isEmpty()) {
                return response()->json([
                    'message' => "La categoría \"{$category->name}\" es obligatoria",
                ], 422);
            }
            if ($category->selection_type === MenuCategory::TYPE_SINGLE && $selected->count() > 1) {
                return response()->json([
                    'message' => "La categoría \"{$category->name}\" permite solo una opción",
                ], 422);
            }
        }

        DB::transaction(function () use ($day, $diner, $itemIds) {
            WeeklyMenuSelection::where('weekly_menu_day_id', $day->id)
                ->where('diner_id', $diner->id)
                ->delete();

            foreach ($itemIds as $itemId) {
                WeeklyMenuSelection::create([
                    'weekly_menu_day_id' => $day->id,
                    'diner_id' => $diner->id,
                    'weekly_menu_day_item_id' => $itemId,
                ]);
            }
        });

        return response()->json(['message' => 'Selección guardada correctamente']);
    }

    public function mySelections(string $dayId, Request $request): JsonResponse
    {
        $dinerId = $request->query('diner_id');
        if (!$dinerId) {
            return response()->json(['message' => 'diner_id requerido'], 422);
        }

        $day = WeeklyMenuDay::with('weeklyMenuBuild')->find($dayId);
        if (!$day) {
            return response()->json(['message' => 'Día no encontrado'], 404);
        }

        $ids = WeeklyMenuSelection::where('weekly_menu_day_id', $day->id)
            ->where('diner_id', $dinerId)
            ->pluck('weekly_menu_day_item_id');

        return response()->json(['selections' => $ids]);
    }
}
