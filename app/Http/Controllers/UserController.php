<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $users = User::with('role')->get();

        return response()->json([
            'message' => 'Users retrieved successfully',
            'data' => $users,
        ]);
    }


    public function changeRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);


        $role = Role::find($validated['role_id']);

        if (!$role) {
            return response()->json([
                'message' => 'Role tidak ditemukan',
            ], 404);
        }


        $authUser = auth()->user();
        if ($authUser->id === $user->id && $authUser->hasRole('admin')) {
            return response()->json([
                'message' => 'Admin tidak dapat mengubah role dirinya sendiri',
            ], 403);
        }

        $user->update(['role_id' => $validated['role_id']]);

        return response()->json([
            'message' => 'Role user berhasil diubah',
            'data' => $user->load('role'),
        ]);
    }


    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
        ]);


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
}
