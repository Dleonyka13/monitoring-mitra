<?php

namespace App\Http\Controllers\Api;

use App\Helpers\PaginationHelper;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $params = PaginationHelper::getParams($request);
        $search = $request->input('search');
        $role = $request->input('role');

        $query = User::query();

        // Search by name or email
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($role) {
            $query->where('role', $role);
        }

        // Order by latest
        $query->orderBy('created_at', 'desc');

        $users = $query->paginate($params['per_page']);

        // Transform users using UserResource
        $transformedData = PaginationHelper::transform($users);
        $transformedData['data'] = UserResource::collection(collect($transformedData['data']));

        return ResponseHelper::success(
            $transformedData,
            'Users retrieved successfully'
        );
    }

    /**
     * Store a newly created user
     * POST /api/{api_name}/{version}/users
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(['mitra', 'pegawai', 'kepala', 'admin'])],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return ResponseHelper::success(
            new UserResource($user),
            'User created successfully',
            201
        );
    }

    /**
     * Display the specified user
     * GET /api/{api_name}/{version}/users/{id}
     */
    public function show(string $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return ResponseHelper::notFound('User not found');
        }

        return ResponseHelper::success(
            new UserResource($user),
            'User retrieved successfully'
        );
    }

    /**
     * Update the specified user
     * PUT /api/{api_name}/{version}/users/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return ResponseHelper::notFound('User not found');
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'password' => 'sometimes|required|string|min:8',
            'role' => ['sometimes', 'required', Rule::in(['mitra', 'pegawai', 'kepala', 'admin'])],
        ]);

        // Update hanya field yang dikirim
        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->has('role')) {
            $user->role = $request->role;
        }

        $user->save();

        return ResponseHelper::success(
            new UserResource($user),
            'User updated successfully'
        );
    }

    /**
     * Remove the specified user (Soft Delete)
     * DELETE /api/{api_name}/{version}/users/{id}
     */
    public function destroy(string $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return ResponseHelper::notFound('User not found');
        }

        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return ResponseHelper::error(
                'You cannot delete your own account',
                null,
                403
            );
        }

        $user->delete();

        return ResponseHelper::success(
            null,
            'User deleted successfully'
        );
    }

    /**
     * Get authenticated user profile
     * GET /api/{api_name}/{version}/me
     */
    public function me(Request $request): JsonResponse
    {
        return ResponseHelper::success(
            new UserResource($request->user()),
            'User profile retrieved successfully'
        );
    }
}