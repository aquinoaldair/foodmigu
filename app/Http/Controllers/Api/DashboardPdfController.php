<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Models\Diner;
use App\Models\DiningHall;
use App\Models\WeeklyMenuBuild;
use App\Models\WeeklyMenuDay;
use App\Models\WeeklyMenuSelection;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class DashboardPdfController
{
    public function day(Request $request, string $dayId)
    {
        $diningHallId = $request->query('dining_hall_id');
        if (!$diningHallId) {
            return response()->json(['message' => 'dining_hall_id es requerido'], 422);
        }

        $day = WeeklyMenuDay::with(['weeklyMenuBuild', 'items.menuCategory'])
            ->find($dayId);
        if (!$day) {
            return response()->json(['message' => 'Día no encontrado'], 404);
        }

        $build = $day->weeklyMenuBuild;
        $hall = DiningHall::find($diningHallId);
        if (!$hall) {
            return response()->json(['message' => 'Comedor no encontrado'], 404);
        }

        if (!$build->diningHalls()->where('dining_halls.id', $diningHallId)->exists()) {
            return response()->json(['message' => 'El comedor no está asignado a esta semana'], 403);
        }

        $allDiners = Diner::where('dining_hall_id', $diningHallId)
            ->orderBy('name')
            ->get(['id', 'id_code', 'name']);

        $selectedDinerIds = WeeklyMenuSelection::where('weekly_menu_day_id', $day->id)
            ->whereIn('diner_id', $allDiners->pluck('id'))
            ->distinct()
            ->pluck('diner_id');

        $selectionsByDiner = WeeklyMenuSelection::where('weekly_menu_day_id', $day->id)
            ->whereIn('diner_id', $allDiners->pluck('id'))
            ->with('weeklyMenuDayItem.menuCategory')
            ->get()
            ->groupBy('diner_id');

        $rows = [];
        foreach ($allDiners as $diner) {
            $selected = $selectedDinerIds->contains($diner->id);
            $optionsText = '';
            if ($selected) {
                $items = $selectionsByDiner->get($diner->id, collect());
                $byCat = $items->groupBy(fn ($s) => $s->weeklyMenuDayItem?->menu_category_id);
                $parts = [];
                foreach ($byCat as $catItems) {
                    $cat = $catItems->first()?->weeklyMenuDayItem?->menuCategory;
                    $names = $catItems->map(fn ($s) => $s->weeklyMenuDayItem?->name)->filter()->values();
                    $parts[] = ($cat?->name ?? '-') . ': ' . $names->join(', ');
                }
                $optionsText = implode(' | ', $parts);
            }
            $rows[] = [
                'id_code' => $diner->id_code,
                'name' => $diner->name,
                'estado' => $selected ? 'Eligió' : 'Pendiente',
                'opciones' => $optionsText,
            ];
        }

        $dateFormatted = $day->date->locale('es')->isoFormat('dddd D [de] MMMM Y');
        $filename = 'dia_' . $day->date->format('Y-m-d') . '_' . \Illuminate\Support\Str::slug($hall->name) . '.pdf';

        $pdf = Pdf::loadView('pdf.day', [
            'title' => $build->title,
            'date' => $dateFormatted,
            'comedor' => $hall->name,
            'rows' => $rows,
        ]);

        return $pdf->download($filename);
    }

    public function week(Request $request, string $weekId)
    {
        $diningHallId = $request->query('dining_hall_id');
        if (!$diningHallId) {
            return response()->json(['message' => 'dining_hall_id es requerido'], 422);
        }

        $build = WeeklyMenuBuild::with(['days.items.menuCategory', 'diningHalls'])
            ->find($weekId);
        if (!$build) {
            return response()->json(['message' => 'Semana no encontrada'], 404);
        }

        $hall = DiningHall::find($diningHallId);
        if (!$hall) {
            return response()->json(['message' => 'Comedor no encontrado'], 404);
        }

        if (!$build->diningHalls()->where('dining_halls.id', $diningHallId)->exists()) {
            return response()->json(['message' => 'El comedor no está asignado a esta semana'], 403);
        }

        $dinerIds = Diner::where('dining_hall_id', $diningHallId)->pluck('id');

        $daysData = [];
        foreach ($build->days->sortBy('date') as $day) {
            $counts = WeeklyMenuSelection::where('weekly_menu_day_id', $day->id)
                ->whereIn('diner_id', $dinerIds)
                ->select('weekly_menu_day_item_id', DB::raw('COUNT(*) as total'))
                ->groupBy('weekly_menu_day_item_id')
                ->pluck('total', 'weekly_menu_day_item_id');

            $byCategory = [];
            foreach ($day->items as $item) {
                $total = (int) ($counts[$item->id] ?? 0);
                if ($total === 0) continue;
                $cid = $item->menu_category_id ?? 0;
                $cname = $item->menuCategory?->name ?? '-';
                if (!isset($byCategory[$cid])) {
                    $byCategory[$cid] = ['name' => $cname, 'options' => []];
                }
                $byCategory[$cid]['options'][] = ['name' => $item->name, 'total' => $total];
            }
            foreach ($byCategory as $cid => &$cat) {
                usort($cat['options'], fn ($a, $b) => $b['total'] <=> $a['total']);
            }
            unset($cat);

            $daysData[] = [
                'date' => $day->date->locale('es')->isoFormat('dddd D [de] MMMM'),
                'categories' => array_values($byCategory),
            ];
        }

        $filename = 'resumen_semana_' . \Illuminate\Support\Str::slug($build->title) . '_' . \Illuminate\Support\Str::slug($hall->name) . '.pdf';

        $pdf = Pdf::loadView('pdf.week', [
            'title' => $build->title,
            'comedor' => $hall->name,
            'days' => $daysData,
        ]);

        return $pdf->download($filename);
    }
}
