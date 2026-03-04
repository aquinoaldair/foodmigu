<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreImageRequest;
use App\Models\Image;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    private const STORAGE_DIR = 'menu-images';

    /**
     * List all images in the pool.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 24), 100);
        $images = Image::orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $images,
        ]);
    }

    /**
     * Upload a new image to the pool.
     */
    public function store(StoreImageRequest $request): JsonResponse
    {
        $file = $request->file('image');
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $filename = Str::uuid() . '.' . $extension;
        $path = self::STORAGE_DIR . '/' . $filename;

        $file->storeAs('public', $path);

        $image = Image::create([
            'name' => pathinfo($originalName, PATHINFO_FILENAME),
            'path' => $path,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $image->id,
                'path' => $image->path,
                'url' => $image->url,
            ],
        ], 201);
    }

    /**
     * Delete an image from the pool (only if not in use).
     */
    public function destroy(Image $image): JsonResponse
    {
        $inUse = $image->weeklyMenuDayItems()->exists();

        if ($inUse) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar la imagen porque está en uso por una o más opciones del menú.',
            ], 422);
        }

        Storage::disk('public')->delete($image->path);
        $image->delete();

        return response()->json([
            'success' => true,
            'data' => null,
        ]);
    }
}
