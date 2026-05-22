<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Get all users - Super admin only
     */
    public function index(Request $request): JsonResponse
    {
        if (!auth()->user()?->isSuperAdmin()) {
            return response()->json([
                'message' => 'Unauthorized. Only super admin can view all users.',
                'error_code' => 'UNAUTHORIZED'
            ], 403);
        }

        $query = User::query();

        // Search by name or email
        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
        }

        // Filter by role
        if ($role = $request->get('role')) {
            $query->where('role', $role);
        }

        $users = $query->select('id', 'name', 'email', 'phone', 'role', 'last_login_at', 'created_at')
                       ->paginate($request->integer('per_page', 15));

        return response()->json($users);
    }

    /**
     * Create a new user - Super admin only
     */
    public function store(Request $request): JsonResponse
    {
        if (!auth()->user()?->isSuperAdmin()) {
            return response()->json([
                'message' => 'Unauthorized. Only super admin can create users.',
                'error_code' => 'UNAUTHORIZED'
            ], 403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'phone' => ['required', 'string', 'max:20'],
            'role' => ['required', 'in:super_admin,admin,operator'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'role' => $validated['role'],
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user->only(['id', 'name', 'email', 'phone', 'role'])
        ], 201);
    }

    /**
     * Get user by ID - Own user or super admin only
     */
    public function show(User $user): JsonResponse
    {
        $authUser = auth()->user();

        if ($authUser?->id !== $user->id && !$authUser?->isSuperAdmin()) {
            return response()->json([
                'message' => 'Unauthorized. You can only view your own user profile.',
                'error_code' => 'UNAUTHORIZED'
            ], 403);
        }

        return response()->json([
            'user' => $user->only(['id', 'name', 'email', 'phone', 'role', 'bio', 'created_at', 'last_login_at'])
        ]);
    }

    /**
     * Update user - Own user or super admin only
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $authUser = auth()->user();

        if ($authUser?->id !== $user->id && !$authUser?->isSuperAdmin()) {
            return response()->json([
                'message' => 'Unauthorized. You can only update your own profile.',
                'error_code' => 'UNAUTHORIZED'
            ], 403);
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'string', 'max:20'],
            'bio' => ['sometimes', 'string', 'max:500'],
            'password' => ['sometimes', 'string', 'min:8'],
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user->only(['id', 'name', 'email', 'phone', 'role', 'bio'])
        ]);
    }

    /**
     * Delete user - Super admin only
     */
    public function destroy(User $user): JsonResponse
    {
        if (!auth()->user()?->isSuperAdmin()) {
            return response()->json([
                'message' => 'Unauthorized. Only super admin can delete users.',
                'error_code' => 'UNAUTHORIZED'
            ], 403);
        }

        // Prevent deleting the only super admin
        if ($user->isSuperAdmin() && User::where('role', 'super_admin')->count() === 1) {
            return response()->json([
                'message' => 'Cannot delete the last super admin user.',
                'error_code' => 'VALIDATION_ERROR'
            ], 422);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }

    /**
     * Change user role - Super admin only
     */
    public function changeRole(Request $request, User $user): JsonResponse
    {
        if (!auth()->user()?->isSuperAdmin()) {
            return response()->json([
                'message' => 'Unauthorized. Only super admin can change roles.',
                'error_code' => 'UNAUTHORIZED'
            ], 403);
        }

        $validated = $request->validate([
            'role' => ['required', 'in:super_admin,admin,operator'],
        ]);

        // Prevent removing all super admins
        if ($user->isSuperAdmin() && $validated['role'] !== 'super_admin' && User::where('role', 'super_admin')->count() === 1) {
            return response()->json([
                'message' => 'Cannot remove the last super admin.',
                'error_code' => 'VALIDATION_ERROR'
            ], 422);
        }

        $oldRole = $user->role;
        $user->update(['role' => $validated['role']]);

        return response()->json([
            'message' => "User role changed from $oldRole to {$validated['role']}",
            'user' => $user->only(['id', 'name', 'email', 'role'])
        ]);
    }
}
