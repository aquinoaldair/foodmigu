<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::with(['roles', 'diningHalls'])
            ->where('id', '!=', auth()->id())
            ->get();
            
        return response()->json(['data' => $users]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(['admin', 'user'])],
            'dining_halls' => 'array',
            'dining_halls.*' => 'exists:dining_halls,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['role']);

        if ($validated['role'] === 'user' && !empty($validated['dining_halls'])) {
            $user->diningHalls()->sync($validated['dining_halls']);
        }

        return response()->json([
            'message' => 'Usuario creado exitosamente',
            'data' => $user->load(['roles', 'diningHalls'])
        ], 201);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json(['data' => $user->load(['roles', 'diningHalls'])]);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
            'role' => ['required', Rule::in(['admin', 'user'])],
            'dining_halls' => 'array',
            'dining_halls.*' => 'exists:dining_halls,id',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();

        $user->syncRoles([$validated['role']]);

        // Si es rol usuario, sincronizar los comedores seleccionados. Si es admin, puede borrar las restricciones.
        if ($validated['role'] === 'user') {
            $user->diningHalls()->sync($validated['dining_halls'] ?? []);
        } else {
            $user->diningHalls()->detach();
        }

        return response()->json([
            'message' => 'Usuario actualizado exitosamente',
            'data' => $user->load(['roles', 'diningHalls'])
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'No puedes eliminar tu propio usuario'], 403);
        }
        $user->delete();
        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }
}
