<?php

namespace App\Http\Controllers\Api;

use App\Common\PmlAllocation\PmlAllocationFilterDto;
use App\Common\PmlAllocation\PmlAllocationOrderDto;
use App\Helpers\PaginationHelper;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\PmlAllocationResource;
use App\Services\PmlAllocationExcelService;
use App\Services\PmlAllocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PmlAllocationController extends Controller
{
    public function __construct(
        private readonly PmlAllocationService $service,
        private readonly PmlAllocationExcelService $excelService
    ) {}

    /**
     * Get all PML allocations
     * GET /api/monitoring-mitra/v1/pml-allocations
     */
    public function index(Request $request): JsonResponse
    {
        $params = PaginationHelper::getParams($request);
        $filter = PmlAllocationFilterDto::fromRequest($request);
        $order = PmlAllocationOrderDto::fromRequest($request);

        $allocations = $this->service->getPaginatedAllocations(
            $params['per_page'],
            $filter,
            $order
        );

        $transformedData = PaginationHelper::transform($allocations);
        $transformedData['data'] = PmlAllocationResource::collection(
            collect($transformedData['data'])
        );

        return ResponseHelper::success(
            $transformedData,
            'PML allocations retrieved successfully'
        );
    }

    /**
     * Create new PML allocation (Admin only)
     * POST /api/monitoring-mitra/v1/pml-allocations
     */
    public function store(Request $request): JsonResponse
    {
        if (!$request->user()->hasRole('admin')) {
            return ResponseHelper::forbidden(
                'You do not have permission to create PML allocations'
            );
        }

        $result = $this->service->validateAndCreate($request->all());

        if (!$result['success']) {
            return ResponseHelper::error(
                'Validation failed',
                $result['errors'] ?? $result['error'] ?? null,
                isset($result['errors']) ? 422 : 400
            );
        }

        return ResponseHelper::success(
            new PmlAllocationResource($result['data']),
            'PML allocation created successfully',
            201
        );
    }

    /**
     * Bulk create PML allocations (Admin only)
     * POST /api/monitoring-mitra/v1/pml-allocations/bulk
     */
    public function bulkStore(Request $request): JsonResponse
    {
        if (!$request->user()->hasRole('admin')) {
            return ResponseHelper::forbidden(
                'You do not have permission to create PML allocations'
            );
        }

        $result = $this->service->validateAndBulkCreate($request->all());

        if (!$result['success']) {
            return ResponseHelper::error(
                isset($result['errors']) ? 'Validation failed' : 'Failed to bulk create',
                $result['errors'] ?? $result['error'] ?? null,
                isset($result['errors']) ? 422 : 500
            );
        }

        $data = $result['data'];
        $message = "Bulk create completed. Success: {$data['success_count']}, Failed: {$data['failed_count']}";

        return ResponseHelper::success([
            'success_count' => $data['success_count'],
            'failed_count' => $data['failed_count'],
            'errors' => $data['errors'],
            'data' => PmlAllocationResource::collection($data['created']),
        ], $message, 201);
    }

    /**
     * Get specific PML allocation by ID
     * GET /api/monitoring-mitra/v1/pml-allocations/{id}
     */
    public function show(string $id): JsonResponse
    {
        $allocation = $this->service->findById($id);

        if (!$allocation) {
            return ResponseHelper::notFound('PML allocation not found');
        }

        return ResponseHelper::success(
            new PmlAllocationResource($allocation),
            'PML allocation retrieved successfully'
        );
    }

    /**
     * Update PML allocation (Admin only)
     * PATCH /api/monitoring-mitra/v1/pml-allocations/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        if (!$request->user()->hasRole('admin')) {
            return ResponseHelper::forbidden(
                'You do not have permission to update PML allocations'
            );
        }

        $allocation = $this->service->findById($id);

        if (!$allocation) {
            return ResponseHelper::notFound('PML allocation not found');
        }

        $result = $this->service->validateAndUpdate($allocation, $request->all());

        if (!$result['success']) {
            return ResponseHelper::error(
                isset($result['errors']) ? 'Validation failed' : $result['error'],
                $result['errors'] ?? null,
                isset($result['errors']) ? 422 : 400
            );
        }

        return ResponseHelper::success(
            new PmlAllocationResource($result['data']),
            'PML allocation updated successfully'
        );
    }

    /**
     * Delete PML allocation (Admin only)
     * DELETE /api/monitoring-mitra/v1/pml-allocations/{id}
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        if (!$request->user()->hasRole('admin')) {
            return ResponseHelper::forbidden(
                'You do not have permission to delete PML allocations'
            );
        }

        $allocation = $this->service->findById($id);

        if (!$allocation) {
            return ResponseHelper::notFound('PML allocation not found');
        }

        $this->service->delete($allocation);

        return ResponseHelper::success(
            null,
            'PML allocation deleted successfully'
        );
    }

    /**
     * Get PML allocations by user
     * GET /api/monitoring-mitra/v1/pml-allocations/user/{userId}
     */
    public function getByUser(string $userId): JsonResponse
    {
        $allocations = $this->service->getAllocationsByUser($userId);

        return ResponseHelper::success(
            PmlAllocationResource::collection($allocations),
            'PML allocations retrieved successfully'
        );
    }

    /**
     * Get PML allocations by activity
     * GET /api/monitoring-mitra/v1/pml-allocations/activity/{activityId}
     */
    public function getByActivity(string $activityId): JsonResponse
    {
        $allocations = $this->service->getAllocationsByActivity($activityId);

        return ResponseHelper::success(
            PmlAllocationResource::collection($allocations),
            'PML allocations retrieved successfully'
        );
    }

    /**
     * Bulk delete by activity (Admin only)
     * DELETE /api/monitoring-mitra/v1/pml-allocations/activity/{activityId}
     */
    public function bulkDeleteByActivity(Request $request, string $activityId): JsonResponse
    {
        if (!$request->user()->hasRole('admin')) {
            return ResponseHelper::forbidden(
                'You do not have permission to delete PML allocations'
            );
        }

        $deletedCount = $this->service->bulkDeleteByActivity($activityId);

        return ResponseHelper::success(
            ['deleted_count' => $deletedCount],
            "Successfully deleted {$deletedCount} PML allocation(s)"
        );
    }

    /**
     * Bulk delete by user (Admin only)
     * DELETE /api/monitoring-mitra/v1/pml-allocations/user/{userId}
     */
    public function bulkDeleteByUser(Request $request, string $userId): JsonResponse
    {
        if (!$request->user()->hasRole('admin')) {
            return ResponseHelper::forbidden(
                'You do not have permission to delete PML allocations'
            );
        }

        $deletedCount = $this->service->bulkDeleteByUser($userId);

        return ResponseHelper::success(
            ['deleted_count' => $deletedCount],
            "Successfully deleted {$deletedCount} PML allocation(s)"
        );
    }

    /**
     * Get statistics summary
     * GET /api/monitoring-mitra/v1/pml-allocations/statistics/summary
     */
    public function summary(): JsonResponse
    {
        $summary = $this->service->getStatisticsSummary();

        return ResponseHelper::success(
            $summary,
            'PML allocation statistics retrieved successfully'
        );
    }

    /**
     * Check if user is allocated to activity
     * GET /api/monitoring-mitra/v1/pml-allocations/check
     */
    public function checkAllocation(Request $request): JsonResponse
    {
        $result = $this->service->validateAndCheckAllocation($request->all());

        if (!$result['success']) {
            return ResponseHelper::error(
                'Validation failed',
                $result['errors'],
                422
            );
        }

        return ResponseHelper::success(
            $result['data'],
            'Allocation check completed'
        );
    }

    /**
     * Download import template
     * GET /api/monitoring-mitra/v1/pml-allocations/template/download
     */
    public function downloadTemplate(): BinaryFileResponse
    {
        $filePath = $this->excelService->generateImportTemplate();
        return response()->download($filePath, basename($filePath))->deleteFileAfterSend(true);
    }

    /**
     * Export to Excel
     * GET /api/monitoring-mitra/v1/pml-allocations/export/excel
     */
    public function export(Request $request): BinaryFileResponse
    {
        $filter = PmlAllocationFilterDto::fromRequest($request);
        $order = PmlAllocationOrderDto::fromRequest($request);

        $allocations = $this->service->getAllAllocations($filter, $order);
        $filePath = $this->excelService->exportAllocations($allocations);

        return response()->download($filePath, basename($filePath))->deleteFileAfterSend(true);
    }

    /**
     * Import from Excel
     * POST /api/monitoring-mitra/v1/pml-allocations/import/excel
     */
    public function import(Request $request): JsonResponse
    {
        if (!$request->user()->hasRole('admin')) {
            return ResponseHelper::forbidden(
                'You do not have permission to import PML allocations'
            );
        }

        if (!$request->hasFile('file')) {
            return ResponseHelper::error('File is required', null, 422);
        }

        try {
            $file = $request->file('file');
            
            // Validate file type
            if (!in_array($file->getClientOriginalExtension(), ['xlsx', 'xls'])) {
                return ResponseHelper::error(
                    'Invalid file type. Only Excel files are allowed',
                    null,
                    422
                );
            }

            $rows = $this->excelService->readExcelFile($file->getRealPath());
            $result = $this->service->processImport($rows);

            $message = "Import completed. Success: {$result['success_count']}, Failed: {$result['failed_count']}";

            return ResponseHelper::success($result, $message);
        } catch (\Exception $e) {
            return ResponseHelper::error(
                'Failed to import PML allocations',
                ['error' => $e->getMessage()],
                500
            );
        }
    }
}