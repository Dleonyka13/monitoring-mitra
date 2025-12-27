<?php

namespace App\Services;

use App\Common\PmlAllocation\PmlAllocationFilterDto;
use App\Common\PmlAllocation\PmlAllocationOrderDto;
use App\DTOs\PmlAllocation\BulkCreatePmlAllocationDto;
use App\DTOs\PmlAllocation\CreatePmlAllocationDto;
use App\DTOs\PmlAllocation\UpdatePmlAllocationDto;
use App\Models\PmlAllocation;
use App\Models\StatisticalActivity;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PmlAllocationService
{
    /**
     * Get paginated PML allocations with filters and sorting
     */
    public function getPaginatedAllocations(
        int $perPage,
        PmlAllocationFilterDto $filter,
        PmlAllocationOrderDto $order
    ): LengthAwarePaginator {
        $query = PmlAllocation::with(['user', 'statisticalActivity']);

        // Apply filters
        $this->applyFilters($query, $filter);

        // Apply sorting
        $query->orderBy($order->orderBy, $order->orderDirection);

        return $query->paginate($perPage);
    }

    /**
     * Get all PML allocations (for export)
     */
    public function getAllAllocations(
        PmlAllocationFilterDto $filter,
        PmlAllocationOrderDto $order
    ): Collection {
        $query = PmlAllocation::with(['user', 'statisticalActivity']);

        // Apply filters
        $this->applyFilters($query, $filter);

        // Apply sorting
        $query->orderBy($order->orderBy, $order->orderDirection);

        return $query->get();
    }

    /**
     * Find PML allocation by ID
     */
    public function findById(string $id): ?PmlAllocation
    {
        return PmlAllocation::with(['user', 'statisticalActivity'])->find($id);
    }

    /**
     * Validate and create new PML allocation
     */
    public function validateAndCreate(array $data): array
    {
        // Validate input
        $validator = Validator::make($data, [
            'user_id' => 'required|uuid|exists:users,id',
            'statistical_activity_id' => 'required|uuid|exists:statistical_activities,id',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors(),
            ];
        }

        try {
            $dto = new CreatePmlAllocationDto(
                user_id: $data['user_id'],
                statistical_activity_id: $data['statistical_activity_id']
            );

            $allocation = $this->create($dto);

            return [
                'success' => true,
                'data' => $allocation->load(['user', 'statisticalActivity']),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create new PML allocation
     */
    public function create(CreatePmlAllocationDto $dto): PmlAllocation
    {
        // Check if allocation already exists
        if (PmlAllocation::allocationExists($dto->user_id, $dto->statistical_activity_id)) {
            throw new \Exception('PML allocation already exists for this user and activity');
        }

        return PmlAllocation::create($dto->toArray());
    }

    /**
     * Validate and bulk create PML allocations
     */
    public function validateAndBulkCreate(array $data): array
    {
        // Validate input
        $validator = Validator::make($data, [
            'statistical_activity_id' => 'required|uuid|exists:statistical_activities,id',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'required|uuid|exists:users,id',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors(),
            ];
        }

        try {
            $dto = new BulkCreatePmlAllocationDto(
                statistical_activity_id: $data['statistical_activity_id'],
                user_ids: $data['user_ids']
            );

            $result = $this->bulkCreate($dto);

            return [
                'success' => true,
                'data' => $result,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Bulk create PML allocations
     */
    public function bulkCreate(BulkCreatePmlAllocationDto $dto): array
    {
        $successCount = 0;
        $failedCount = 0;
        $errors = [];
        $created = [];

        DB::beginTransaction();

        try {
            foreach ($dto->user_ids as $userId) {
                try {
                    // Check if user exists
                    $user = User::find($userId);
                    if (!$user) {
                        $failedCount++;
                        $errors[] = [
                            'user_id' => $userId,
                            'error' => 'User not found',
                        ];
                        continue;
                    }

                    // Check if allocation already exists
                    if (PmlAllocation::allocationExists($userId, $dto->statistical_activity_id)) {
                        $failedCount++;
                        $errors[] = [
                            'user_id' => $userId,
                            'error' => 'Allocation already exists',
                        ];
                        continue;
                    }

                    $allocation = PmlAllocation::create([
                        'user_id' => $userId,
                        'statistical_activity_id' => $dto->statistical_activity_id,
                    ]);

                    $created[] = $allocation->load(['user', 'statisticalActivity']);
                    $successCount++;
                } catch (\Exception $e) {
                    $failedCount++;
                    $errors[] = [
                        'user_id' => $userId,
                        'error' => $e->getMessage(),
                    ];
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return [
            'success_count' => $successCount,
            'failed_count' => $failedCount,
            'errors' => $errors,
            'created' => $created,
        ];
    }

    /**
     * Validate and update PML allocation
     */
    public function validateAndUpdate(PmlAllocation $allocation, array $data): array
    {
        // Validate input
        $validator = Validator::make($data, [
            'user_id' => 'sometimes|required|uuid|exists:users,id',
            'statistical_activity_id' => 'sometimes|required|uuid|exists:statistical_activities,id',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors(),
            ];
        }

        try {
            $dto = new UpdatePmlAllocationDto(
                user_id: $data['user_id'] ?? null,
                statistical_activity_id: $data['statistical_activity_id'] ?? null
            );

            if (!$dto->hasUpdates()) {
                return [
                    'success' => false,
                    'error' => 'No fields to update',
                ];
            }

            $updatedAllocation = $this->update($allocation, $dto);

            return [
                'success' => true,
                'data' => $updatedAllocation,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Update existing PML allocation
     */
    public function update(PmlAllocation $allocation, UpdatePmlAllocationDto $dto): PmlAllocation
    {
        // Check if new allocation already exists (if user_id or activity_id changed)
        if ($dto->user_id || $dto->statistical_activity_id) {
            $newUserId = $dto->user_id ?? $allocation->user_id;
            $newActivityId = $dto->statistical_activity_id ?? $allocation->statistical_activity_id;

            // Only check if it's different from current allocation
            if ($newUserId !== $allocation->user_id || $newActivityId !== $allocation->statistical_activity_id) {
                if (PmlAllocation::allocationExists($newUserId, $newActivityId)) {
                    throw new \Exception('PML allocation already exists for this user and activity');
                }
            }
        }

        if ($dto->user_id !== null) {
            $allocation->user_id = $dto->user_id;
        }

        if ($dto->statistical_activity_id !== null) {
            $allocation->statistical_activity_id = $dto->statistical_activity_id;
        }

        $allocation->save();

        return $allocation->load(['user', 'statisticalActivity']);
    }

    /**
     * Delete PML allocation
     */
    public function delete(PmlAllocation $allocation): bool
    {
        return $allocation->delete();
    }

    /**
     * Bulk delete PML allocations by activity
     */
    public function bulkDeleteByActivity(string $activityId): int
    {
        return PmlAllocation::where('statistical_activity_id', $activityId)->delete();
    }

    /**
     * Bulk delete PML allocations by user
     */
    public function bulkDeleteByUser(string $userId): int
    {
        return PmlAllocation::where('user_id', $userId)->delete();
    }

    /**
     * Get allocations by user
     */
    public function getAllocationsByUser(string $userId): Collection
    {
        return PmlAllocation::with(['statisticalActivity'])
            ->where('user_id', $userId)
            ->get();
    }

    /**
     * Get allocations by activity
     */
    public function getAllocationsByActivity(string $activityId): Collection
    {
        return PmlAllocation::with(['user'])
            ->where('statistical_activity_id', $activityId)
            ->get();
    }

    /**
     * Get statistics summary
     */
    public function getStatisticsSummary(): array
    {
        $total = PmlAllocation::count();
        $totalUsers = PmlAllocation::distinct('user_id')->count('user_id');
        $totalActivities = PmlAllocation::distinct('statistical_activity_id')
            ->count('statistical_activity_id');

        return [
            'total_allocations' => $total,
            'total_pml_assigned' => $totalUsers,
            'total_activities_with_pml' => $totalActivities,
        ];
    }

    /**
     * Validate and check allocation
     */
    public function validateAndCheckAllocation(array $data): array
    {
        $validator = Validator::make($data, [
            'user_id' => 'required|uuid|exists:users,id',
            'statistical_activity_id' => 'required|uuid|exists:statistical_activities,id',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors(),
            ];
        }

        $isAllocated = $this->isUserAllocated(
            $data['user_id'],
            $data['statistical_activity_id']
        );

        return [
            'success' => true,
            'data' => ['is_allocated' => $isAllocated],
        ];
    }

    /**
     * Check if user is already allocated to activity
     */
    public function isUserAllocated(string $userId, string $activityId): bool
    {
        return PmlAllocation::allocationExists($userId, $activityId);
    }

    /**
     * Process import from Excel
     */
    public function processImport(array $rows): array
    {
        $successCount = 0;
        $failedCount = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2;

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Prepare data
                $data = [
                    'user_id' => $row[0] ?? null,
                    'statistical_activity_id' => $row[1] ?? null,
                ];

                // Validate row data
                $rowValidator = Validator::make($data, [
                    'user_id' => 'required|uuid|exists:users,id',
                    'statistical_activity_id' => 'required|uuid|exists:statistical_activities,id',
                ]);

                if ($rowValidator->fails()) {
                    $failedCount++;
                    $errors[] = [
                        'row' => $rowNumber,
                        'user_id' => $data['user_id'] ?? 'N/A',
                        'errors' => $rowValidator->errors()->all(),
                    ];
                    continue;
                }

                try {
                    // Check if allocation already exists
                    if ($this->isUserAllocated($data['user_id'], $data['statistical_activity_id'])) {
                        $failedCount++;
                        $errors[] = [
                            'row' => $rowNumber,
                            'user_id' => $data['user_id'],
                            'errors' => ['Allocation already exists'],
                        ];
                        continue;
                    }

                    // Create allocation
                    $this->create(new CreatePmlAllocationDto(
                        user_id: $data['user_id'],
                        statistical_activity_id: $data['statistical_activity_id']
                    ));

                    $successCount++;
                } catch (\Exception $e) {
                    $failedCount++;
                    $errors[] = [
                        'row' => $rowNumber,
                        'user_id' => $data['user_id'] ?? 'N/A',
                        'errors' => [$e->getMessage()],
                    ];
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return [
            'success_count' => $successCount,
            'failed_count' => $failedCount,
            'errors' => $errors,
        ];
    }

    /**
     * Apply filters to query
     */
    private function applyFilters(Builder $query, PmlAllocationFilterDto $filter): void
    {
        // Global search (user name, email, or activity name)
        if ($filter->search) {
            $query->where(function ($q) use ($filter) {
                $q->whereHas('user', function ($subQ) use ($filter) {
                    $subQ->where('name', 'like', "%{$filter->search}%")
                        ->orWhere('email', 'like', "%{$filter->search}%");
                })
                ->orWhereHas('statisticalActivity', function ($subQ) use ($filter) {
                    $subQ->where('name', 'like', "%{$filter->search}%");
                });
            });
        }

        // Filter by user_id
        if ($filter->user_id) {
            $query->where('user_id', $filter->user_id);
        }

        // Filter by statistical_activity_id
        if ($filter->statistical_activity_id) {
            $query->where('statistical_activity_id', $filter->statistical_activity_id);
        }

        // Filter by user name
        if ($filter->user_name) {
            $query->whereHas('user', function ($q) use ($filter) {
                $q->where('name', 'like', "%{$filter->user_name}%");
            });
        }

        // Filter by user email
        if ($filter->user_email) {
            $query->whereHas('user', function ($q) use ($filter) {
                $q->where('email', 'like', "%{$filter->user_email}%");
            });
        }

        // Filter by activity name
        if ($filter->activity_name) {
            $query->whereHas('statisticalActivity', function ($q) use ($filter) {
                $q->where('name', 'like', "%{$filter->activity_name}%");
            });
        }
    }
}