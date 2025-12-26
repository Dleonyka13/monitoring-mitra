<?php

namespace App\Http\Controllers\Api;

use App\Common\StatisticalActivity\StatisticalActivityFilterDto;
use App\Common\StatisticalActivity\StatisticalActivityOrderDto;
use App\DTOs\StatisticalActivity\CreateStatisticalActivityDto;
use App\Helpers\ResponseHelper;
use App\Services\StatisticalActivityExcelService;
use App\Services\StatisticalActivityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Additional methods for StatisticalActivityController
 * Add these methods to your existing StatisticalActivityController class
 */
trait StatisticalActivityExportImportMethods
{
    /**
     * Download statistical activity import template
     * GET /api/monitoring-mitra/v1/kegiatan-statistik/template/download
     */
    public function downloadTemplate(): BinaryFileResponse
    {
        /** @var StatisticalActivityExcelService $excelService */
        $excelService = app(StatisticalActivityExcelService::class);
        
        $filePath = $excelService->generateImportTemplate();
        $fileName = basename($filePath);

        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * Export statistical activities to Excel
     * GET /api/monitoring-mitra/v1/kegiatan-statistik/export/excel
     */
    public function export(Request $request): BinaryFileResponse
    {
        $user = $request->user();
        $filter = StatisticalActivityFilterDto::fromRequest($request);
        $order = StatisticalActivityOrderDto::fromRequest($request);

        /** @var StatisticalActivityService $service */
        $service = app(StatisticalActivityService::class);
        
        /** @var StatisticalActivityExcelService $excelService */
        $excelService = app(StatisticalActivityExcelService::class);

        $activities = $service->getAllActivities($filter, $order, $user);
        $filePath = $excelService->exportActivities($activities);
        $fileName = basename($filePath);

        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * Import statistical activities from Excel
     * POST /api/monitoring-mitra/v1/kegiatan-statistik/import/excel
     */
    public function import(Request $request): JsonResponse
    {
        // Access control
        if (!$request->user()->hasRole('admin')) {
            return ResponseHelper::forbidden(
                'You do not have permission to import statistical activities'
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
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();

            // Get specific range (columns A to E)
            $highestRow = $sheet->getHighestRow();
            $rows = $sheet->rangeToArray('A1:E' . $highestRow, null, true, true, false);

            // Remove header row
            array_shift($rows);

            $successCount = 0;
            $failedCount = 0;
            $errors = [];

            /** @var StatisticalActivityService $service */
            $service = app(StatisticalActivityService::class);

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2;

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Prepare data
                $data = [
                    'name' => $row[0] ?? null,
                    'start_date' => $row[1] ?? null,
                    'end_date' => $row[2] ?? null,
                    'total_target' => $row[3] ?? null,
                    'is_done' => $row[4] ?? null,
                ];

                // Validate row data
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
                    // Parse is_done value
                    $isDone = false;
                    if (isset($data['is_done'])) {
                        $isDone = in_array(strtolower($data['is_done']), ['true', '1', 1], true);
                    }

                    // Create DTO
                    $dto = new CreateStatisticalActivityDto(
                        name: $data['name'],
                        start_date: $data['start_date'],
                        end_date: $data['end_date'],
                        total_target: (int) $data['total_target'],
                        is_done: $isDone
                    );

                    $service->create($dto);
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

            $message = "Import completed. Success: {$successCount}, Failed: {$failedCount}";

            return ResponseHelper::success([
                'success_count' => $successCount,
                'failed_count' => $failedCount,
                'errors' => $errors,
            ], $message);

        } catch (\Exception $e) {
            return ResponseHelper::error(
                'Failed to import statistical activities',
                ['error' => $e->getMessage()],
                500
            );
        }
    }
}