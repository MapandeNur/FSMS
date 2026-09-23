<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a paginated list of users.
     * Supports search by name or email.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');

        $users = User::with('roles')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return response()->json([
            'success' => true,
            'message' => 'Users retrieved successfully.',
            'data' => UserResource::collection($users),
            'errors' => null,
        ]);
    }

    /**
     * Create a new user and assign a role.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'phone' => [
                'required',
                'string',
                'max:20',
                'unique:users,phone',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
            ],

            'is_active' => [
                'sometimes',
                'boolean',
            ],

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

        $user = DB::transaction(function () use ($validated) {

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'is_active' => $validated['is_active'] ?? true,
            ]);

            $user->roles()->attach($validated['role_id'], [
                'start_date' => $validated['start_date'] ?? now()->toDateString(),
                'end_date' => $validated['end_date'] ?? null,
            ]);

            return $user;
        });

        $user->load('roles');

        return response()->json([
            'success' => true,
            'message' => 'User created and role assigned successfully.',
            'data' => new UserResource($user),
            'errors' => null,
        ], 201);
    }

    /**
     * Display a specific user.
     */
    public function show(User $user): JsonResponse
    {
        $user->load('roles');

        return response()->json([
            'success' => true,
            'message' => 'User retrieved successfully.',
            'data' => new UserResource($user),
            'errors' => null,
        ]);
    }

    /**
     * Update a user.
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],

            'phone' => [
                'sometimes',
                'required',
                'string',
                'max:20',
                Rule::unique('users', 'phone')->ignore($user->id),
            ],

            'password' => [
                'sometimes',
                'nullable',
                'string',
                'min:8',
            ],

            'is_active' => [
                'sometimes',
                'boolean',
            ],

            'role_id' => [
                'sometimes',
                'nullable',
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

        if (array_key_exists('password', $validated)) {
            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }
        }

        $roleId = $validated['role_id'] ?? null;

        unset(
            $validated['role_id'],
            $validated['start_date'],
            $validated['end_date']
        );

        $user->update($validated);

        if ($roleId !== null) {
            $user->roles()->syncWithoutDetaching([
                $roleId => [
                    'start_date' => $request->input('start_date')
                        ?? now()->toDateString(),

                    'end_date' => $request->input('end_date'),
                ],
            ]);
        }

        $user->load('roles');

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully.',
            'data' => new UserResource($user),
            'errors' => null,
        ]);
    }

    /**
     * Activate or deactivate a user.
     */
    public function updateStatus(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        $user->update([
            'is_active' => $validated['is_active'],
        ]);

        $user->load('roles');

        return response()->json([
            'success' => true,
            'message' => $user->is_active
                ? 'User activated successfully.'
                : 'User deactivated successfully.',
            'data' => new UserResource($user),
            'errors' => null,
        ]);
    }

    /**
     * Assign a role to a user.
     */
    public function assignRole(Request $request, User $user): JsonResponse
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
                'start_date' => $validated['start_date']
                    ?? now()->toDateString(),

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
            'errors' => null,
        ]);
    }

    /**
     * Remove a role from a user.
     */
    public function removeRole(User $user, Role $role): JsonResponse
    {
        $user->roles()->detach($role->id);

        $user->load('roles');

        return response()->json([
            'success' => true,
            'message' => 'Role removed successfully.',
            'data' => [
                'user' => new UserResource($user),
            ],
            'errors' => null,
        ]);
    }

    /**
     * Delete a user.
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.',
            'data' => null,
            'errors' => null,
        ]);
    }
}