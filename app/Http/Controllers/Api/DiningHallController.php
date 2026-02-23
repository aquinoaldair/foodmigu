<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DiningHall;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DiningHallController extends Controller
{
    public function index(): JsonResponse
    {
        $halls = DiningHall::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $halls,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'code' => ['nullable', 'string', 'unique:dining_halls,code'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $validated['is_active'] ?? true;

        if (empty(trim((string) ($validated['code'] ?? '')))) {
            $validated['code'] = $this->generateUniqueCode($validated['name']);
        } else {
            $validated['code'] = trim($validated['code']);
        }

        $hall = DiningHall::create($validated);

        return response()->json([
            'success' => true,
            'data' => $hall,
        ], 201);
    }

    public function show(DiningHall $diningHall): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $diningHall,
        ]);
    }

    public function update(Request $request, DiningHall $diningHall): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'code' => ['required', 'string', Rule::unique('dining_halls', 'code')->ignore($diningHall->id)],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $validated['is_active'] ?? true;

        $diningHall->update($validated);

        return response()->json([
            'success' => true,
            'data' => $diningHall->fresh(),
        ]);
    }

    public function destroy(DiningHall $diningHall): JsonResponse
    {
        $diningHall->delete();

        return response()->json([
            'success' => true,
            'data' => null,
        ]);
    }

    private function generateUniqueCode(string $name): string
    {
        $baseSlug = Str::slug($name);
        $code = $baseSlug ?: 'comedor';
        $counter = 1;
        while (DiningHall::where('code', $code)->exists()) {
            $code = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $code;
    }

    public function publicUrl(DiningHall $diningHall): JsonResponse
    {
        $url = rtrim(config('app.url'), '/') . '/c/' . $diningHall->code;

        return response()->json([
            'success' => true,
            'data' => [
                'url' => $url,
            ],
        ]);
    }
}
