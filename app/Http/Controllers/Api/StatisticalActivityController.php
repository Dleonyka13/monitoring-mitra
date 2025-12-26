<?php

namespace App\Http\Controllers\Api;

use App\Common\StatisticalActivity\StatisticalActivityFilterDto;
use App\Common\StatisticalActivity\StatisticalActivityOrderDto;
use App\DTOs\StatisticalActivity\CreateStatisticalActivityDto;
use App\DTOs\StatisticalActivity\UpdateStatisticalActivityDto;
use App\Helpers\PaginationHelper;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\StatisticalActivityResource;
use App\Services\StatisticalActivityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StatisticalActivityController extends Controller
{
    public function __construct(
        private readonly StatisticalActivityService $statisticalActivityService
    ) {}

    /**
     * Create new statistical activity (Admin only)
     * POST /api/monitoring-mitra/v1/kegiatan-statistik
     */
    public function create(Request $request): JsonResponse
    {
        // Access control
        if (!$request->user()->hasRole('admin')) {
            return ResponseHelper::forbidden(
                'You do not have permission to create statistical activities'
            );
        }

        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'total_target' => 'required|integer|min:1',
            'is_done' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::error(
                'Validation failed',
                $validator->errors(),
                422
            );
        }

        $dto = CreateStatisticalActivityDto::fromRequest($request);
        $activity = $this->statisticalActivityService->create($dto);

        return ResponseHelper::success(
            new StatisticalActivityResource($activity),
            'Statistical activity created successfully',
            201
        );
    }


    /**
     * Get all statistical activities with filters and sorting
     * GET /api/monitoring-mitra/v1/kegiatan-statistik
     */
    public function findAll(Request $request): JsonResponse
    {
        $user = $request->user();
        $params = PaginationHelper::getParams($request);
        $filter = StatisticalActivityFilterDto::fromRequest($request);
        $order = StatisticalActivityOrderDto::fromRequest($request);

        $activities = $this->statisticalActivityService->pagination(
            $params['per_page'],
            $filter,
            $order,
            $user
        );

        $transformedData = PaginationHelper::transform($activities);
        $transformedData['data'] = StatisticalActivityResource::collection(
            collect($transformedData['data'])
        );

        return ResponseHelper::success(
            $transformedData,
            'Statistical activities retrieved successfully'
        );
    }

    /**
     * Get specific statistical activity by ID
     * GET /api/monitoring-mitra/v1/kegiatan-statistik/{id}
     */
    public function findById(Request $request, string $id): JsonResponse
    {
        $user = $request->user();

        // Access control check
        if (!in_array($user->role, ['admin', 'mitra', 'pegawai', 'kepala'])) {
            return ResponseHelper::forbidden(
                'You do not have permission to view statistical activities'
            );
        }

        $activity = $this->statisticalActivityService->findById($id, $user);

        if (!$activity) {
            return ResponseHelper::notFound(
                'Statistical activity not found or you do not have access'
            );
        }

        return ResponseHelper::success(
            new StatisticalActivityResource($activity),
            'Statistical activity retrieved successfully'
        );
    }

    /**
     * Update statistical activity (Admin only)
     * PATCH /api/monitoring-mitra/v1/kegiatan-statistik/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        // Access control
        if (!$request->user()->hasRole('admin')) {
            return ResponseHelper::forbidden(
                'You do not have permission to update statistical activities'
            );
        }

        $activity = $this->statisticalActivityService->findByIdForAdmin($id);

        if (!$activity) {
            return ResponseHelper::notFound('Statistical activity not found');
        }

        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after_or_equal:start_date',
            'total_target' => 'sometimes|required|integer|min:1',
            'is_done' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::error(
                'Validation failed',
                $validator->errors(),
                422
            );
        }

        $dto = UpdateStatisticalActivityDto::fromRequest($request);
        
        if (!$dto->hasUpdates()) {
            return ResponseHelper::error(
                'No fields to update',
                null,
                400
            );
        }

        $activity = $this->statisticalActivityService->update($activity, $dto);

        return ResponseHelper::success(
            new StatisticalActivityResource($activity),
            'Statistical activity updated successfully'
        );
    }

    /**
     * Soft delete statistical activity (Admin only)
     * DELETE /api/monitoring-mitra/v1/kegiatan-statistik/{id}
     */
    public function delete(Request $request, string $id): JsonResponse
    {
        // Access control
        if (!$request->user()->hasRole('admin')) {
            return ResponseHelper::forbidden(
                'You do not have permission to delete statistical activities'
            );
        }

        $activity = $this->statisticalActivityService->findByIdForAdmin($id);

        if (!$activity) {
            return ResponseHelper::notFound('Statistical activity not found');
        }

        $this->statisticalActivityService->delete($activity);

        return ResponseHelper::success(
            null,
            'Statistical activity deleted successfully'
        );
    }

    /**
     * Get statistics summary
     * GET /api/monitoring-mitra/v1/kegiatan-statistik/statistics/summary
     */
    public function summary(Request $request): JsonResponse
    {
        $user = $request->user();
        $summary = $this->statisticalActivityService->getStatisticsSummary($user);

        return ResponseHelper::success(
            $summary,
            'Statistics summary retrieved successfully'
        );
    }
}