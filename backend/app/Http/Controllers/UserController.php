<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->latest()->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Users retrieved successfully.',
            'data' => UserResource::collection($users),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['required', 'string', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully.',
            'data' => new UserResource($user),
        ], 201);
    }

    public function show(User $user)
    {
        $user->load('roles');

        return response()->json([
            'success' => true,
            'message' => 'User retrieved successfully.',
            'data' => new UserResource($user),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => [
                'sometimes',
                'required',
                'string',
                Rule::unique('users', 'phone')->ignore($user->id),
            ],
            'password' => ['sometimes', 'nullable', 'string', 'min:8'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        $user->load('roles');

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully.',
            'data' => new UserResource($user),
        ]);
    }

    /**
     * Assign a role to a user.
     */
    public function assignRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role_id' => [
                'required',
                'integer',
                'exists:roles,id',
            ],
            'start_date' => [
                'nullable',
                'date',
            ],
            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],
        ]);

        $role = Role::findOrFail($validated['role_id']);

        $user->roles()->syncWithoutDetaching([
            $role->id => [
                'start_date' => $validated['start_date'] ?? now()->toDateString(),
                'end_date' => $validated['end_date'] ?? null,
            ],
        ]);

        $user->load('roles');

        return response()->json([
            'success' => true,
            'message' => 'Role assigned successfully.',
            'data' => [
                'user' => new UserResource($user),
                'role' => $role,
            ],
        ]);
    }

    /**
     * Remove a role from a user.
     */
    public function removeRole(User $user, Role $role)
    {
        $user->roles()->detach($role->id);

        $user->load('roles');

        return response()->json([
            'success' => true,
            'message' => 'Role removed successfully.',
            'data' => [
                'user' => new UserResource($user),
            ],
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.',
            'data' => [],
        ]);
    }
}