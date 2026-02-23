<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Models\Diner;
use App\Models\DiningHall;
use App\Models\WeeklyMenuBuild;
use App\Models\WeeklyMenuDay;
use App\Models\WeeklyMenuSelection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController
{
    public function weeks(): JsonResponse
    {
        $rows = DB::table('weekly_menu_build_dining_hall')
            ->join('weekly_menu_builds', 'weekly_menu_builds.id', '=', 'weekly_menu_build_dining_hall.weekly_menu_build_id')
            ->join('dining_halls', 'dining_halls.id', '=', 'weekly_menu_build_dining_hall.dining_hall_id')
            ->select(
                'weekly_menu_builds.id as weekly_menu_build_id',
                'weekly_menu_builds.title',
                'weekly_menu_builds.status',
                'weekly_menu_builds.published_at',
                'dining_halls.id as dining_hall_id',
                'dining_halls.name as dining_hall_name',
                'dining_halls.code as dining_hall_code'
            )
            ->orderByDesc('weekly_menu_builds.published_at')
            ->orderByDesc('weekly_menu_builds.created_at')
            ->get();

        $data = $rows->map(fn ($r) => [
            'weekly_menu_build_id' => $r->weekly_menu_build_id,
            'title' => $r->title,
            'status' => $r->status,
            'published_at' => $r->published_at,
            'dining_hall' => [
                'id' => $r->dining_hall_id,
                'name' => $r->dining_hall_name,
                'code' => $r->dining_hall_code,
            ],
        ])->toArray();

        return response()->json(['data' => $data]);
    }

    public function week(Request $request, string $id): JsonResponse
    {
        $diningHallId = $request->query('dining_hall_id');
        if (!$diningHallId) {
            return response()->json(['message' => 'dining_hall_id es requerido'], 422);
        }

        $build = WeeklyMenuBuild::with(['days' => fn ($q) => $q->orderBy('date')])
            ->find($id);

        if (!$build) {
            return response()->json(['message' => 'Semana no encontrada'], 404);
        }

        $hall = DiningHall::find($diningHallId);
        if (!$hall) {
            return response()->json(['message' => 'Comedor no encontrado'], 404);
        }

        $assigned = $build->diningHalls()->where('dining_halls.id', $diningHallId)->exists();
        if (!$assigned) {
            return response()->json(['message' => 'El comedor no está asignado a esta semana'], 403);
        }

        $totalDiners = Diner::where('dining_hall_id', $diningHallId)->count();

        $selectionByDay = WeeklyMenuSelection::whereIn('weekly_menu_day_id', $build->days->pluck('id'))
            ->join('diners', 'diners.id', '=', 'weekly_menu_selections.diner_id')
            ->where('diners.dining_hall_id', $diningHallId)
            ->select('weekly_menu_selections.weekly_menu_day_id', DB::raw('COUNT(DISTINCT weekly_menu_selections.diner_id) as cnt'))
            ->groupBy('weekly_menu_selections.weekly_menu_day_id')
            ->pluck('cnt', 'weekly_menu_day_id');

        $days = $build->days->map(function (WeeklyMenuDay $day) use ($totalDiners, $selectionByDay) {
            $totalSelected = (int) ($selectionByDay[$day->id] ?? 0);
            return [
                'id' => $day->id,
                'date' => $day->date->format('Y-m-d'),
                'total_diners' => $totalDiners,
                'total_selected' => $totalSelected,
            ];
        });

        return response()->json([
            'data' => [
                'id' => $build->id,
                'title' => $build->title,
                'status' => $build->status,
                'published_at' => $build->published_at?->toIso8601String(),
                'dining_hall' => [
                    'id' => $hall->id,
                    'name' => $hall->name,
                    'code' => $hall->code,
                ],
                'days' => $days,
                'total_diners' => $totalDiners,
            ],
        ]);
    }

    public function day(Request $request, string $dayId): JsonResponse
    {
        $diningHallId = $request->query('dining_hall_id');
        if (!$diningHallId) {
            return response()->json(['message' => 'dining_hall_id es requerido'], 422);
        }

        $day = WeeklyMenuDay::with(['weeklyMenuBuild.diningHalls', 'items.menuCategory'])
            ->find($dayId);

        if (!$day) {
            return response()->json(['message' => 'Día no encontrado'], 404);
        }

        $build = $day->weeklyMenuBuild;
        $hall = DiningHall::find($diningHallId);
        if (!$hall) {
            return response()->json(['message' => 'Comedor no encontrado'], 404);
        }

        $assigned = $build->diningHalls()->where('dining_halls.id', $diningHallId)->exists();
        if (!$assigned) {
            return response()->json(['message' => 'El comedor no está asignado a este menú'], 403);
        }

        $allDiners = Diner::where('dining_hall_id', $diningHallId)->get(['id', 'id_code', 'name']);

        $selectedDinerIds = WeeklyMenuSelection::where('weekly_menu_day_id', $day->id)
            ->whereIn('diner_id', $allDiners->pluck('id'))
            ->distinct()
            ->pluck('diner_id');

        $dinersSelected = $allDiners->whereIn('id', $selectedDinerIds);
        $dinersPending = $allDiners->whereNotIn('id', $selectedDinerIds);

        $selectionsByDiner = WeeklyMenuSelection::where('weekly_menu_day_id', $day->id)
            ->whereIn('diner_id', $allDiners->pluck('id'))
            ->with('weeklyMenuDayItem.menuCategory')
            ->get()
            ->groupBy('diner_id');

        $dinerSelections = $dinersSelected->map(function (Diner $diner) use ($selectionsByDiner) {
            $items = $selectionsByDiner->get($diner->id, collect());
            $opts = [];
            foreach ($items->groupBy(fn ($s) => $s->weeklyMenuDayItem?->menu_category_id) as $catId => $catItems) {
                $cat = $catItems->first()?->weeklyMenuDayItem?->menuCategory;
                $opts[] = [
                    'category_id' => $catId,
                    'category_name' => $cat?->name ?? '',
                    'items' => $catItems->map(fn ($s) => $s->weeklyMenuDayItem?->name)->filter()->values()->toArray(),
                ];
            }
            return [
                'diner_id' => $diner->id,
                'id_code' => $diner->id_code,
                'name' => $diner->name,
                'options_by_category' => $opts,
            ];
        })->values();

        $dinersPendingFlat = $dinersPending->map(fn (Diner $d) => [
            'diner_id' => $d->id,
            'id_code' => $d->id_code,
            'name' => $d->name,
        ])->values();

        $countByOption = WeeklyMenuSelection::where('weekly_menu_day_id', $day->id)
            ->whereIn('diner_id', $allDiners->pluck('id'))
            ->select('weekly_menu_day_item_id', DB::raw('COUNT(*) as total_selections'))
            ->groupBy('weekly_menu_day_item_id')
            ->pluck('total_selections', 'weekly_menu_day_item_id');

        $optionsSummary = [];
        foreach ($day->items as $item) {
            $total = (int) ($countByOption[$item->id] ?? 0);
            $optionsSummary[] = [
                'item_id' => $item->id,
                'item_name' => $item->name,
                'menu_category_id' => $item->menu_category_id,
                'menu_category_name' => $item->menuCategory?->name ?? '',
                'total_selections' => $total,
            ];
        }
        usort($optionsSummary, fn ($a, $b) => $b['total_selections'] <=> $a['total_selections']);

        $categoryTotals = [];
        foreach ($optionsSummary as $row) {
            $cid = $row['menu_category_id'];
            if (!isset($categoryTotals[$cid])) {
                $categoryTotals[$cid] = [
                    'menu_category_id' => $cid,
                    'menu_category_name' => $row['menu_category_name'],
                    'total_selections' => 0,
                ];
            }
            $categoryTotals[$cid]['total_selections'] += $row['total_selections'];
        }

        return response()->json([
            'data' => [
                'day' => [
                    'id' => $day->id,
                    'date' => $day->date->format('Y-m-d'),
                    'build_title' => $build->title,
                ],
                'dining_hall' => [
                    'id' => $hall->id,
                    'name' => $hall->name,
                    'code' => $hall->code,
                ],
                'summary' => [
                    'total_diners' => $allDiners->count(),
                    'total_selected' => $selectedDinerIds->count(),
                    'total_pending' => $dinersPending->count(),
                ],
                'diners_selected' => $dinerSelections->toArray(),
                'diners_pending' => $dinersPendingFlat->toArray(),
                'count_by_option' => $optionsSummary,
                'total_by_category' => array_values($categoryTotals),
            ],
        ]);
    }
}
