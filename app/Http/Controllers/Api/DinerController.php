<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Diner;
use App\Models\DiningHall;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DinersImport;

class DinerController extends Controller
{
    public function index(DiningHall $diningHall): JsonResponse
    {
        $diners = $diningHall->diners()->orderBy('id_code')->get();

        return response()->json([
            'success' => true,
            'data' => $diners,
        ]);
    }

    public function store(Request $request, DiningHall $diningHall): JsonResponse
    {
        $validated = $request->validate([
            'id_code' => [
                'required',
                'string',
                Rule::unique('diners', 'id_code')->where('dining_hall_id', $diningHall->id),
            ],
            'name' => ['required', 'string'],
        ]);

        $diner = $diningHall->diners()->create($validated);

        return response()->json([
            'success' => true,
            'data' => $diner,
        ], 201);
    }

    public function update(Request $request, DiningHall $diningHall, Diner $diner): JsonResponse
    {
        if ($diner->dining_hall_id !== (int) $diningHall->id) {
            return response()->json(['success' => false, 'message' => 'Diner not found.'], 404);
        }

        $validated = $request->validate([
            'id_code' => [
                'required',
                'string',
                Rule::unique('diners', 'id_code')->where('dining_hall_id', $diningHall->id)->ignore($diner->id),
            ],
            'name' => ['required', 'string'],
        ]);

        $diner->update($validated);

        return response()->json([
            'success' => true,
            'data' => $diner->fresh(),
        ]);
    }

    public function destroy(DiningHall $diningHall, Diner $diner): JsonResponse
    {
        if ($diner->dining_hall_id !== (int) $diningHall->id) {
            return response()->json(['success' => false, 'message' => 'Diner not found.'], 404);
        }

        $diner->delete();

        return response()->json([
            'success' => true,
            'data' => null,
        ]);
    }

    public function import(Request $request, DiningHall $diningHall): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,csv'],
        ]);

        $file = $request->file('file');

        try {
            $import = new DinersImport($diningHall->id);
            Excel::import($import, $file);

            return response()->json([
                'success' => true,
                'created' => $import->getCreatedCount(),
                'updated' => $import->getUpdatedCount(),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al importar: ' . $e->getMessage(),
            ], 422);
        }
    }
}
