<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();
        $user = Auth::user();
        $user->update(['last_login_at' => now()]);

        return response()->json([
            'user' => $user->load('roles', 'permissions'),
            'role' => $user->role,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out']);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'user' => $user?->load('roles', 'permissions'),
            'role' => $user?->role,
            'roles' => $user?->getRoleNames(),
            'permissions' => $user?->getAllPermissions()->pluck('name'),
        ]);
    }

    public function signup(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'operator', // Default role for new users
        ]);

        return response()->json([
            'message' => 'Account created successfully. Please sign in with your credentials.',
            'user' => $user->load('roles', 'permissions'),
        ], 201);
    }

    public function signupAdmin(Request $request): JsonResponse
    {
        // Only super admin can create other admins
        if (!Auth::user()?->isSuperAdmin()) {
            return response()->json([
                'message' => 'Unauthorized. Only super admin can create admin users.',
                'error_code' => 'UNAUTHORIZED'
            ], 403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['required', 'string'],
            'role' => ['required', 'in:admin,operator'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'role' => $validated['role'],
        ]);

        return response()->json([
            'message' => 'Admin user created successfully',
            'user' => $user->load('roles', 'permissions'),
        ], 201);
    }
}
