<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StatisticalActivityExcelService
{
    /**
     * Export statistical activities to Excel
     */
    public function exportActivities(Collection $activities): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = [
            'ID',
            'Name',
            'Start Date',
            'End Date',
            'Total Target',
            'Status',
            'Is Done',
            'Duration (Days)',
            'Created At',
            'Updated At'
        ];
        $sheet->fromArray($headers, null, 'A1');

        // Style header
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2563EB'], // Blue-600
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);

        // Add data
        $rowNumber = 2;
        foreach ($activities as $activity) {
            $durationDays = $activity->start_date && $activity->end_date
                ? $activity->start_date->diffInDays($activity->end_date)
                : 0;

            $sheet->fromArray([
                $activity->id,
                $activity->name,
                $activity->start_date?->format('Y-m-d H:i:s'),
                $activity->end_date?->format('Y-m-d H:i:s'),
                $activity->total_target,
                $activity->is_done ? 'Selesai' : 'Berlangsung',
                $activity->is_done ? 'Yes' : 'No',
                $durationDays,
                $activity->created_at->format('Y-m-d H:i:s'),
                $activity->updated_at->format('Y-m-d H:i:s'),
            ], null, "A{$rowNumber}");

            // Apply conditional formatting for status
            $statusCell = "F{$rowNumber}";
            if ($activity->is_done) {
                $sheet->getStyle($statusCell)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D1FAE5'], // Green-100
                    ],
                    'font' => [
                        'color' => ['rgb' => '065F46'], // Green-800
                        'bold' => true,
                    ],
                ]);
            } else {
                $sheet->getStyle($statusCell)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FEF3C7'], // Yellow-100
                    ],
                    'font' => [
                        'color' => ['rgb' => '92400E'], // Yellow-800
                        'bold' => true,
                    ],
                ]);
            }

            $rowNumber++;
        }

        // Auto-size columns
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set row height for header
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Add borders to all data
        $sheet->getStyle('A1:J' . ($rowNumber - 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB'], // Gray-300
                ],
            ],
        ]);

        return $this->saveSpreadsheet($spreadsheet, 'statistical_activities_export');
    }

    /**
     * Generate statistical activities import template
     */
    public function generateImportTemplate(): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = [
            'Name',
            'Start Date (YYYY-MM-DD HH:MM:SS)',
            'End Date (YYYY-MM-DD HH:MM:SS)',
            'Total Target',
            'Is Done (true/false)'
        ];
        $sheet->fromArray($headers, null, 'A1');

        // Style header
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2563EB'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);

        // Add sample data
        $sampleData = [
            [
                'Survei Penduduk 2025',
                '2025-01-01 08:00:00',
                '2025-03-31 17:00:00',
                1000,
                'false'
            ],
            [
                'Sensus Ekonomi 2025',
                '2025-02-01 08:00:00',
                '2025-06-30 17:00:00',
                5000,
                'false'
            ],
        ];
        $sheet->fromArray($sampleData, null, 'A2');

        // Add instructions
        $instructions = [
            ['Instructions:'],
            ['1. Name: Required, max 255 characters'],
            ['2. Start Date: Required, format YYYY-MM-DD HH:MM:SS'],
            ['3. End Date: Required, must be after or equal to Start Date'],
            ['4. Total Target: Required, minimum 1'],
            ['5. Is Done: Optional, use "true" or "false"'],
            ['6. Delete sample data before uploading'],
            ['7. Do not modify column headers'],
        ];

        $instructionRow = 5;
        foreach ($instructions as $instruction) {
            $sheet->fromArray($instruction, null, "G{$instructionRow}");
            $instructionRow++;
        }

        $sheet->getStyle('G5:G12')->getFont()->setItalic(true)->setSize(10);
        $sheet->getStyle('G5')->getFont()->setBold(true)->setSize(11);

        // Auto-size columns
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->getColumnDimension('G')->setWidth(50);

        // Set row height
        $sheet->getRowDimension(1)->setRowHeight(25);

        return $this->saveSpreadsheet($spreadsheet, 'statistical_activity_import_template');
    }

    /**
     * Save spreadsheet to temp directory
     */
    private function saveSpreadsheet(Spreadsheet $spreadsheet, string $prefix): string
    {
        $writer = new Xlsx($spreadsheet);
        $fileName = $prefix . '_' . date('Ymd_His') . '.xlsx';
        $tempDir = storage_path('app/temp');

        // Create temp directory if not exists
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $tempFile = $tempDir . '/' . $fileName;
        $writer->save($tempFile);

        return $tempFile;
    }
}