<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
        ])->validate();

        $user = $request->user();
        $user->update(['name' => $validated['name']]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $validated = Validator::make($request->all(), [
            'current_password' => ['required'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ])->validate();

        $user = $request->user();

        if (! Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'La contraseña actual es incorrecta.',
                'errors' => [
                    'current_password' => ['La contraseña actual es incorrecta.'],
                ],
            ], 422);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'success' => true,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        auth()->logout();

        return response()->json([
            'success' => true,
        ]);
    }
}
