<?php

namespace App\Services;

use App\Common\StatisticalActivity\StatisticalActivityFilterDto;
use App\Common\StatisticalActivity\StatisticalActivityOrderDto;
use App\DTOs\StatisticalActivity\CreateStatisticalActivityDto;
use App\DTOs\StatisticalActivity\UpdateStatisticalActivityDto;
use App\Models\StatisticalActivity;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class StatisticalActivityService
{
    /**
     * Get paginated statistical activities with filters, sorting, and access control
     */
    public function pagination(
        int $perPage,
        StatisticalActivityFilterDto $filter,
        StatisticalActivityOrderDto $order,
        User $user
    ): LengthAwarePaginator {
        $query = StatisticalActivity::where('is_active', true);

        // Apply access control
        $this->applyAccessControl($query, $user);

        // Apply filters
        $this->applyFilters($query, $filter);

        // Apply sorting
        $query->orderBy($order->orderBy, $order->orderDirection);

        return $query->paginate($perPage);
    }

    /**
     * Get all statistical activities (for export)
     */
    public function findAll(
        StatisticalActivityFilterDto $filter,
        StatisticalActivityOrderDto $order,
        User $user
    ): Collection {
        $query = StatisticalActivity::where('is_active', true);

        // Apply access control
        $this->applyAccessControl($query, $user);

        // Apply filters
        $this->applyFilters($query, $filter);

        // Apply sorting
        $query->orderBy($order->orderBy, $order->orderDirection);

        return $query->get();
    }

    /**
     * Find statistical activity by ID with access control
     */
    public function findById(string $id, User $user): ?StatisticalActivity
    {
        $query = StatisticalActivity::where('is_active', true)
            ->where('id', $id);

        // Apply access control
        $this->applyAccessControl($query, $user);

        return $query->first();
    }

    /**
     * Find statistical activity by ID (admin only, no access control)
     */
    public function findByIdForAdmin(string $id): ?StatisticalActivity
    {
        return StatisticalActivity::where('is_active', true)
            ->where('id', $id)
            ->first();
    }

    /**
     * Create new statistical activity
     */
    public function create(CreateStatisticalActivityDto $dto): StatisticalActivity
    {
        return StatisticalActivity::create($dto->toArray());
    }

    /**
     * Update existing statistical activity
     */
    public function update(
        StatisticalActivity $activity, 
        UpdateStatisticalActivityDto $dto
    ): StatisticalActivity {
        if ($dto->name !== null) {
            $activity->name = $dto->name;
        }

        if ($dto->start_date !== null) {
            $activity->start_date = $dto->start_date;
        }

        if ($dto->end_date !== null) {
            $activity->end_date = $dto->end_date;
        }

        if ($dto->total_target !== null) {
            $activity->total_target = $dto->total_target;
        }

        if ($dto->is_done !== null) {
            $activity->is_done = $dto->is_done;
        }

        $activity->save();

        return $activity;
    }

    /**
     * Soft delete statistical activity
     */
    public function delete(StatisticalActivity $activity): bool
    {
        $activity->is_active = false;
        return $activity->save();
    }

    /**
     * Check if user has access to activity
     */
    public function userHasAccess(StatisticalActivity $activity, User $user): bool
    {
        // Admin and kepala have access to all activities
        if (in_array($user->role, ['admin', 'kepala'])) {
            return true;
        }

        // Mitra and pegawai only have access if they're allocated
        if (in_array($user->role, ['mitra', 'pegawai'])) {
            return $activity->pclAllocations()->where('user_id', $user->id)->exists()
                || $activity->pmlAllocations()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Get statistics summary
     */
    public function getStatisticsSummary(User $user): array
    {
        $query = StatisticalActivity::where('is_active', true);
        
        // Apply access control
        $this->applyAccessControl($query, $user);

        $total = (clone $query)->count();
        $completed = (clone $query)->where('is_done', true)->count();
        $ongoing = (clone $query)->where('is_done', false)->count();

        return [
            'total' => $total,
            'completed' => $completed,
            'ongoing' => $ongoing,
            'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Apply access control based on user role
     */
    private function applyAccessControl(Builder $query, User $user): void
    {
        // Mitra and pegawai can only see activities they're allocated to
        if (in_array($user->role, ['mitra', 'pegawai'])) {
            $query->where(function ($q) use ($user) {
                $q->whereHas('pclAllocations', function ($subQ) use ($user) {
                    $subQ->where('user_id', $user->id);
                })
                ->orWhereHas('pmlAllocations', function ($subQ) use ($user) {
                    $subQ->where('user_id', $user->id);
                });
            });
        }
        // Admin and kepala can see all activities (no additional filter needed)
    }

    /**
     * Apply filters to query
     */
    private function applyFilters(Builder $query, StatisticalActivityFilterDto $filter): void
    {
        // Global search (name)
        if ($filter->search) {
            $query->where('name', 'like', "%{$filter->search}%");
        }

        // Specific name filter
        if ($filter->name) {
            $query->where('name', 'like', "%{$filter->name}%");
        }

        // Is done filter
        if ($filter->is_done !== null) {
            $query->where('is_done', $filter->is_done);
        }

        // Start date range filter
        if ($filter->start_date_from) {
            $query->whereDate('start_date', '>=', $filter->start_date_from);
        }

        if ($filter->start_date_to) {
            $query->whereDate('start_date', '<=', $filter->start_date_to);
        }

        // End date range filter
        if ($filter->end_date_from) {
            $query->whereDate('end_date', '>=', $filter->end_date_from);
        }

        if ($filter->end_date_to) {
            $query->whereDate('end_date', '<=', $filter->end_date_to);
        }
    }
}