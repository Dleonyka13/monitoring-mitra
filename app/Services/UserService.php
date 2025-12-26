<?php

namespace App\Services;

use App\Common\User\UserFilterDto;
use App\Common\User\UserOrderDto;
use App\DTOs\User\CreateUserDto;
use App\DTOs\User\ImportUserDto;
use App\DTOs\User\UpdateUserDto;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserService
{
    /**
     * Get paginated users with filters and sorting
     */
    public function pagination(
        int $perPage,
        UserFilterDto $filter,
        UserOrderDto $order
    ): LengthAwarePaginator {
        $query = User::query();

        // Apply filters
        $this->applyFilters($query, $filter);

        // Apply sorting
        $query->orderBy($order->orderBy, $order->orderDirection);

        return $query->paginate($perPage);
    }

    /**
     * Get all users with filters (for export)
     */
    public function findAll(UserFilterDto $filter, UserOrderDto $order): Collection
    {
        $query = User::query();

        // Apply filters
        $this->applyFilters($query, $filter);

        // Apply sorting
        $query->orderBy($order->orderBy, $order->orderDirection);

        return $query->get();
    }

    /**
     * Find user by ID
     */
    public function findById(string $id): ?User
    {
        return User::find($id);
    }

    /**
     * Create new user
     */
    public function create(CreateUserDto $dto): User
    {
        return User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
            'role' => $dto->role,
        ]);
    }

    /**
     * Update existing user
     */
    public function update(User $user, UpdateUserDto $dto): User
    {
        if ($dto->name !== null) {
            $user->name = $dto->name;
        }

        if ($dto->email !== null) {
            $user->email = $dto->email;
        }

        if ($dto->password !== null) {
            $user->password = Hash::make($dto->password);
        }

        if ($dto->role !== null) {
            $user->role = $dto->role;
        }

        $user->save();

        return $user;
    }

    /**
     * Delete user (soft delete)
     */
    public function delete(User $user): bool
    {
        return $user->delete();
    }

    /**
     * Validate import user data
     */
    public function validateImportUser(ImportUserDto $dto): array
    {
        $validator = Validator::make($dto->toArray(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(['mitra', 'pegawai', 'kepala', 'admin'])],
        ]);

        if ($validator->fails()) {
            return [
                'valid' => false,
                'errors' => $validator->errors()->all(),
            ];
        }

        return ['valid' => true];
    }

    /**
     * Create user from import
     */
    public function createFromImport(ImportUserDto $dto): User
    {
        return User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
            'role' => $dto->role,
        ]);
    }

    /**
     * Apply filters to query
     */
    private function applyFilters($query, UserFilterDto $filter): void
    {
        // Global search (name or email)
        if ($filter->search) {
            $query->where(function ($q) use ($filter) {
                $q->where('name', 'like', "%{$filter->search}%")
                    ->orWhere('email', 'like', "%{$filter->search}%");
            });
        }

        // Specific name filter
        if ($filter->name) {
            $query->where('name', 'like', "%{$filter->name}%");
        }

        // Specific email filter
        if ($filter->email) {
            $query->where('email', 'like', "%{$filter->email}%");
        }

        // Role filter
        if ($filter->role) {
            $query->where('role', $filter->role);
        }
    }
}