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
        // Access control
        if (!$request->user()->hasRole('admin')) {
            return ResponseHelper::forbidden(
                'You do not have permission to import PML allocations'
            );
        }

        // Validate file
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls|max:5120', // Max 5MB
        ]);

        if ($validator->fails()) {
            return ResponseHelper::error(
                'Validation failed',
                $validator->errors(),
                422
            );
        }

        try {
            /** @var PmlAllocationExcelService $excelService */
            $excelService = app(PmlAllocationExcelService::class);

            /** @var PmlAllocationService $service */
            $service = app(PmlAllocationService::class);

            $file = $request->file('file');
            $rows = $excelService->readExcelFile($file->getRealPath());

            $successCount = 0;
            $failedCount = 0;
            $errors = [];

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
                    if ($service->isUserAllocated($data['user_id'], $data['statistical_activity_id'])) {
                        $failedCount++;
                        $errors[] = [
                            'row' => $rowNumber,
                            'user_id' => $data['user_id'],
                            'errors' => ['Allocation already exists'],
                        ];
                        continue;
                    }

                    // Create allocation
                    $service->create(new \App\DTOs\PmlAllocation\CreatePmlAllocationDto(
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

            $message = "Import completed. Success: {$successCount}, Failed: {$failedCount}";

            return ResponseHelper::success([
                'success_count' => $successCount,
                'failed_count' => $failedCount,
                'errors' => $errors,
            ], $message);

        } catch (\Exception $e) {
            return ResponseHelper::error(
                'Failed to import PML allocations',
                ['error' => $e->getMessage()],
                500
            );
        }
    }
}
