<?php

namespace App\Services;

use App\Common\StatisticalActivity\StatisticalActivityFilterDto;
use App\Common\StatisticalActivity\StatisticalActivityOrderDto;
use App\DTOs\StatisticalActivity\CreateStatisticalActivityDto;
use App\DTOs\StatisticalActivity\UpdateStatisticalActivityDto;
use App\Helpers\PaginationHelper;
use App\Http\Resources\StatisticalActivityResource;
use App\Models\StatisticalActivity;
use App\Models\User;
use Carbon\Carbon;
use App\Services\StatisticalActivityExcelService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;

class StatisticalActivityService
{
    public function __construct(
        private readonly StatisticalActivityExcelService $excelService
    ) {}

    /**
     * Create from request with validation and permission check
     */
    public function createFromRequest(Request $request): array
    {
        // Check permission
        if (!$request->user()->hasRole('admin')) {
            return [
                'success' => false,
                'message' => 'You do not have permission to create statistical activities',
                'status_code' => 403
            ];
        }

        // Validate
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'total_target' => 'required|integer|min:1',
            'is_done' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'status_code' => 422
            ];
        }

        $dto = CreateStatisticalActivityDto::fromRequest($request);
        $activity = $this->create($dto);

        return [
            'success' => true,
            'data' => new StatisticalActivityResource($activity),
            'message' => 'Statistical activity created successfully',
            'status_code' => 201
        ];
    }

    /**
     * Find all from request with pagination
     */
    public function findAllFromRequest(Request $request): array
    {
        $user = $request->user();
        $params = PaginationHelper::getParams($request);
        $filter = StatisticalActivityFilterDto::fromRequest($request);
        $order = StatisticalActivityOrderDto::fromRequest($request);

        $activities = $this->pagination(
            $params['per_page'],
            $filter,
            $order,
            $user
        );

        $transformedData = PaginationHelper::transform($activities);
        $transformedData['data'] = StatisticalActivityResource::collection(
            collect($transformedData['data'])
        );

        return [
            'success' => true,
            'data' => $transformedData,
            'message' => 'Statistical activities retrieved successfully'
        ];
    }

    /**
     * Find by ID from request with permission check
     */
    public function findByIdFromRequest(Request $request, string $id): array
    {
        $user = $request->user();
        $activity = $this->findById($id, $user);

        if (!$activity) {
            return [
                'success' => false,
                'message' => 'Statistical activity not found or you do not have access',
                'status_code' => 404
            ];
        }

        return [
            'success' => true,
            'data' => new StatisticalActivityResource($activity),
            'message' => 'Statistical activity retrieved successfully'
        ];
    }

    /**
     * Update from request with validation and permission check
     */
    public function updateFromRequest(Request $request, string $id): array
    {
        // Check permission
        if (!$request->user()->hasRole('admin')) {
            return [
                'success' => false,
                'message' => 'You do not have permission to update statistical activities',
                'status_code' => 403
            ];
        }

        $activity = $this->findByIdForAdmin($id);

        if (!$activity) {
            return [
                'success' => false,
                'message' => 'Statistical activity not found',
                'status_code' => 404
            ];
        }

        // Validate
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after_or_equal:start_date',
            'total_target' => 'sometimes|required|integer|min:1',
            'is_done' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'status_code' => 422
            ];
        }

        $dto = UpdateStatisticalActivityDto::fromRequest($request);

        if (!$dto->hasUpdates()) {
            return [
                'success' => false,
                'message' => 'No fields to update',
                'status_code' => 400
            ];
        }

        $activity = $this->update($activity, $dto);

        return [
            'success' => true,
            'data' => new StatisticalActivityResource($activity),
            'message' => 'Statistical activity updated successfully'
        ];
    }

    /**
     * Delete from request with permission check
     */
    public function deleteFromRequest(Request $request, string $id): array
    {
        // Check permission
        if (!$request->user()->hasRole('admin')) {
            return [
                'success' => false,
                'message' => 'You do not have permission to delete statistical activities',
                'status_code' => 403
            ];
        }

        $activity = $this->findByIdForAdmin($id);

        if (!$activity) {
            return [
                'success' => false,
                'message' => 'Statistical activity not found',
                'status_code' => 404
            ];
        }

        $this->delete($activity);

        return [
            'success' => true,
            'message' => 'Statistical activity deleted successfully'
        ];
    }

    /**
     * Get summary from request
     */
    public function getSummaryFromRequest(Request $request): array
    {
        $user = $request->user();
        $summary = $this->getStatisticsSummary($user);

        return [
            'success' => true,
            'data' => $summary,
            'message' => 'Statistics summary retrieved successfully'
        ];
    }

    /**
     * Generate template
     */
    public function generateTemplate(): array
    {
        $filePath = $this->excelService->generateImportTemplate();
        
        return [
            'file_path' => $filePath,
            'file_name' => basename($filePath)
        ];
    }

    /**
     * Export from request
     */
    public function exportFromRequest(Request $request): array
    {
        $user = $request->user();
        $filter = StatisticalActivityFilterDto::fromRequest($request);
        $order = StatisticalActivityOrderDto::fromRequest($request);

        $activities = $this->getAllActivities($filter, $order, $user);
        $filePath = $this->excelService->exportActivities($activities);

        return [
            'file_path' => $filePath,
            'file_name' => basename($filePath)
        ];
    }

    /**
     * Import from request with validation and permission check
     */
    public function importFromRequest(Request $request): array
    {
        // Check permission
        if (!$request->user()->hasRole('admin')) {
            return [
                'success' => false,
                'message' => 'You do not have permission to import statistical activities',
                'status_code' => 403
            ];
        }

        // Validate file
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'status_code' => 422
            ];
        }

        try {
            $file = $request->file('file');
            $result = $this->processImportFile($file);

            $message = "Import completed. Success: {$result['success_count']}, Failed: {$result['failed_count']}";

            return [
                'success' => true,
                'data' => $result,
                'message' => $message
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to import statistical activities',
                'errors' => ['error' => $e->getMessage()],
                'status_code' => 500
            ];
        }
    }

    /**
     * Process import file
     */
    private function processImportFile($file): array
    {
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();

        $highestRow = $sheet->getHighestRow();
        $rows = $sheet->rangeToArray('A1:E' . $highestRow, null, true, true, false);

        // Remove header
        array_shift($rows);

        return $this->processImportRows($rows);
    }

    /**
     * Process import rows
     */
    private function processImportRows(array $rows): array
    {
        $successCount = 0;
        $failedCount = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            $data = [
                'name' => $row[0] ?? null,
                'start_date' => $row[1] ?? null,
                'end_date' => $row[2] ?? null,
                'total_target' => $row[3] ?? null,
                'is_done' => $row[4] ?? null,
            ];

            // Validate row
            $rowValidator = Validator::make($data, [
                'name' => 'required|string|max:255',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'total_target' => 'required|integer|min:1',
                'is_done' => ['nullable', Rule::in(['true', 'false', '1', '0', 1, 0, true, false])],
            ]);

            if ($rowValidator->fails()) {
                $failedCount++;
                $errors[] = [
                    'row' => $rowNumber,
                    'name' => $data['name'] ?? 'N/A',
                    'errors' => $rowValidator->errors()->all(),
                ];
                continue;
            }

            try {
                // Parse is_done
                $isDone = false;
                if (isset($data['is_done'])) {
                    $isDone = in_array(strtolower((string)$data['is_done']), ['true', '1'], true);
                }

                // Create DTO and save
                $dto = new CreateStatisticalActivityDto(
                    name: $data['name'],
                    start_date: $data['start_date'],
                    end_date: $data['end_date'],
                    total_target: (int) $data['total_target'],
                    is_done: $isDone
                );

                $this->create($dto);
                $successCount++;
            } catch (\Exception $e) {
                $failedCount++;
                $errors[] = [
                    'row' => $rowNumber,
                    'name' => $data['name'] ?? 'N/A',
                    'errors' => [$e->getMessage()],
                ];
            }
        }

        return [
            'success_count' => $successCount,
            'failed_count' => $failedCount,
            'errors' => $errors,
        ];
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
     * Get paginated statistical activities
     */
    public function pagination(
        int $perPage,
        StatisticalActivityFilterDto $filter,
        StatisticalActivityOrderDto $order,
        User $user
    ): LengthAwarePaginator {
        $query = StatisticalActivity::where('is_active', true);

        $this->applyAccessControl($query, $user);
        $this->applyFilters($query, $filter);
        $query->orderBy($order->orderBy, $order->orderDirection);

        return $query->paginate($perPage);
    }

    /**
     * Get all statistical activities (for export)
     */
    public function getAllActivities(
        StatisticalActivityFilterDto $filter,
        StatisticalActivityOrderDto $order,
        User $user
    ): Collection {
        $query = StatisticalActivity::where('is_active', true);

        $this->applyAccessControl($query, $user);
        $this->applyFilters($query, $filter);
        $query->orderBy($order->orderBy, $order->orderDirection);

        return $query->get();
    }

    /**
     * Find by ID with access control
     */
    public function findById(string $id, User $user): ?StatisticalActivity
    {
        $query = StatisticalActivity::where('is_active', true)
            ->where('id', $id);

        $this->applyAccessControl($query, $user);

        return $query->first();
    }

    /**
     * Find by ID for admin (no access control)
     */
    public function findByIdForAdmin(string $id): ?StatisticalActivity
    {
        return StatisticalActivity::where('is_active', true)
            ->where('id', $id)
            ->first();
    }

    /**
     * Soft delete
     */
    public function delete(StatisticalActivity $activity): bool
    {
        $activity->is_active = false;
        return $activity->save();
    }

    /**
     * Check user access to specific activity
     */
    public function userHasAccess(StatisticalActivity $activity, User $user): bool
    {
        // Admin and kepala have access to all activities
        if (in_array($user->role, ['admin', 'kepala'])) {
            return true;
        }

        // Mitra and pegawai only have access if they're allocated as PML or PCL
        if (in_array($user->role, ['mitra', 'pegawai'])) {
            return $activity->pmlAllocations()->where('user_id', $user->id)->exists()
                || $activity->pclAllocations()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Get statistics summary
     */
    public function getStatisticsSummary(User $user): array
    {
        $query = StatisticalActivity::where('is_active', true);

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
     * - admin: can see all activities
     * - kepala: can see all activities
     * - mitra: can only see activities where they are allocated as PML or PCL
     * - pegawai: can only see activities where they are allocated as PML or PCL
     */
    private function applyAccessControl(Builder $query, User $user): void
    {
        // Admin and kepala can see all activities - no filter needed
        if (in_array($user->role, ['admin', 'kepala'])) {
            return;
        }

        // Mitra and pegawai can only see activities they're allocated to
        if (in_array($user->role, ['mitra', 'pegawai'])) {
            $query->where(function ($q) use ($user) {
                // Check if user is allocated as PML
                $q->whereHas('pmlAllocations', function ($subQ) use ($user) {
                    $subQ->where('user_id', $user->id);
                })
                // OR check if user is allocated as PCL
                ->orWhereHas('pclAllocations', function ($subQ) use ($user) {
                    $subQ->where('user_id', $user->id);
                });
            });
        }
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