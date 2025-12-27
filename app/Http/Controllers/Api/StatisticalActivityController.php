<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\ResponseHelper;
use App\Services\StatisticalActivityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
        $result = $this->statisticalActivityService->createFromRequest($request);
        
        if (!$result['success']) {
            return ResponseHelper::error(
                $result['message'],
                $result['errors'] ?? null,
                $result['status_code']
            );
        }
        
        return ResponseHelper::success(
            $result['data'],
            $result['message'],
            $result['status_code']
        );
    }

    /**
     * Get all statistical activities with filters and sorting
     * GET /api/monitoring-mitra/v1/kegiatan-statistik
     */
    public function findAll(Request $request): JsonResponse
    {
        $result = $this->statisticalActivityService->findAllFromRequest($request);
        
        return ResponseHelper::success(
            $result['data'],
            $result['message']
        );
    }

    /**
     * Get specific statistical activity by ID
     * GET /api/monitoring-mitra/v1/kegiatan-statistik/{id}
     */
    public function findById(Request $request, string $id): JsonResponse
    {
        $result = $this->statisticalActivityService->findByIdFromRequest($request, $id);
        
        if (!$result['success']) {
            return ResponseHelper::error(
                $result['message'],
                null,
                $result['status_code']
            );
        }
        
        return ResponseHelper::success(
            $result['data'],
            $result['message']
        );
    }

    /**
     * Update statistical activity (Admin only)
     * PATCH /api/monitoring-mitra/v1/kegiatan-statistik/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $result = $this->statisticalActivityService->updateFromRequest($request, $id);
        
        if (!$result['success']) {
            return ResponseHelper::error(
                $result['message'],
                $result['errors'] ?? null,
                $result['status_code']
            );
        }
        
        return ResponseHelper::success(
            $result['data'],
            $result['message']
        );
    }

    /**
     * Soft delete statistical activity (Admin only)
     * DELETE /api/monitoring-mitra/v1/kegiatan-statistik/{id}
     */
    public function delete(Request $request, string $id): JsonResponse
    {
        $result = $this->statisticalActivityService->deleteFromRequest($request, $id);
        
        if (!$result['success']) {
            return ResponseHelper::error(
                $result['message'],
                null,
                $result['status_code']
            );
        }
        
        return ResponseHelper::success(
            null,
            $result['message']
        );
    }

    /**
     * Get statistics summary
     * GET /api/monitoring-mitra/v1/kegiatan-statistik/statistics/summary
     */
    public function summary(Request $request): JsonResponse
    {
        $result = $this->statisticalActivityService->getSummaryFromRequest($request);
        
        return ResponseHelper::success(
            $result['data'],
            $result['message']
        );
    }

    /**
     * Download statistical activity import template
     * GET /api/monitoring-mitra/v1/kegiatan-statistik/template/download
     */
    public function downloadTemplate(): BinaryFileResponse
    {
        $result = $this->statisticalActivityService->generateTemplate();
        
        return response()
            ->download($result['file_path'], $result['file_name'])
            ->deleteFileAfterSend(true);
    }

    /**
     * Export statistical activities to Excel
     * GET /api/monitoring-mitra/v1/kegiatan-statistik/export/excel
     */
    public function export(Request $request): BinaryFileResponse
    {
        $result = $this->statisticalActivityService->exportFromRequest($request);
        
        return response()
            ->download($result['file_path'], $result['file_name'])
            ->deleteFileAfterSend(true);
    }

    /**
     * Import statistical activities from Excel
     * POST /api/monitoring-mitra/v1/kegiatan-statistik/import/excel
     */
    public function import(Request $request): JsonResponse
    {
        $result = $this->statisticalActivityService->importFromRequest($request);
        
        if (!$result['success']) {
            return ResponseHelper::error(
                $result['message'],
                $result['errors'] ?? null,
                $result['status_code']
            );
        }
        
        return ResponseHelper::success(
            $result['data'],
            $result['message']
        );
    }
}