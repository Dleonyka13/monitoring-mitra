<?php

namespace App\Http\Controllers\Api;

use App\Common\PmlAllocation\PmlAllocationFilterDto;
use App\Common\PmlAllocation\PmlAllocationOrderDto;
use App\Helpers\ResponseHelper;
use App\Services\PmlAllocationExcelService;
use App\Services\PmlAllocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Additional Export/Import methods for PmlAllocationController
 * Add these methods to your PmlAllocationController class
 */
trait PmlAllocationExportImportMethods
{
    /**
     * Download PML allocation import template
     * GET /api/monitoring-mitra/v1/pml-allocations/template/download
     */
    public function downloadTemplate(): BinaryFileResponse
    {
        /** @var PmlAllocationExcelService $excelService */
        $excelService = app(PmlAllocationExcelService::class);

        $filePath = $excelService->generateImportTemplate();
        $fileName = basename($filePath);

        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * Export PML allocations to Excel
     * GET /api/monitoring-mitra/v1/pml-allocations/export/excel
     */
    public function export(Request $request): BinaryFileResponse
    {
        $filter = PmlAllocationFilterDto::fromRequest($request);
        $order = PmlAllocationOrderDto::fromRequest($request);

        /** @var PmlAllocationService $service */
        $service = app(PmlAllocationService::class);

        /** @var PmlAllocationExcelService $excelService */
        $excelService = app(PmlAllocationExcelService::class);

        $allocations = $service->getAllAllocations($filter, $order);
        $filePath = $excelService->exportAllocations($allocations);
        $fileName = basename($filePath);

        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * Import PML allocations from Excel
     * POST /api/monitoring-mitra/v1/pml-allocations/import/excel
     */
    public function import(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::error(
                'Validation failed',
                $validator->errors(),
                422
            );
        }

        try {
            $excelService = app(PmlAllocationExcelService::class);
            $service = app(PmlAllocationService::class);

            $rows = $excelService->readExcelFile(
                $request->file('file')->getRealPath()
            );

            // 🔥 PENTING: PAKAI SERVICE
            $result = $service->processImport($rows);

            return ResponseHelper::success(
                $result,
                "Import completed. Success: {$result['success_count']}, Failed: {$result['failed_count']}"
            );
        } catch (\Exception $e) {
            return ResponseHelper::error(
                'Failed to import PML allocations',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

}
