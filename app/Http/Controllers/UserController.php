<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get all users (Admin only)
     */
    public function index(Request $request)
    {
        $users = User::with('role')->get();

        return response()->json([
            'message' => 'Users retrieved successfully',
            'data' => $users,
        ]);
    }

    /**
     * Change user role (Admin only)
     */
    public function changeRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        // Get the role to validate it's a valid role
        $role = Role::find($validated['role_id']);

        if (!$role) {
            return response()->json([
                'message' => 'Role tidak ditemukan',
            ], 404);
        }

        $user->update(['role_id' => $validated['role_id']]);

        return response()->json([
            'message' => 'Role user berhasil diubah',
            'data' => $user->load('role'),
        ]);
    }

    /**
     * Update user details (Admin only)
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
        ]);

        // Hash password if provided
        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'User berhasil diperbarui',
            'data' => $user->load('role'),
        ]);
    }
