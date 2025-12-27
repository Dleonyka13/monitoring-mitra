<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PmlAllocationExcelService
{
    /**
     * Export PML allocations to Excel
     */
    public function exportAllocations(Collection $allocations): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = [
            'ID',
            'User ID',
            'User Name',
            'User Email',
            'User Role',
            'Activity ID',
            'Activity Name',
            'Activity Start Date',
            'Activity End Date',
            'Activity Status',
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
                'startColor' => ['rgb' => '7C3AED'], // Purple-600
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
        $sheet->getStyle('A1:L1')->applyFromArray($headerStyle);

        // Add data
        $rowNumber = 2;
        foreach ($allocations as $allocation) {
            $sheet->fromArray([
                $allocation->id,
                $allocation->user->id,
                $allocation->user->name,
                $allocation->user->email,
                $allocation->user->role,
                $allocation->statisticalActivity->id,
                $allocation->statisticalActivity->name,
                $allocation->statisticalActivity->start_date?->format('Y-m-d H:i:s'),
                $allocation->statisticalActivity->end_date?->format('Y-m-d H:i:s'),
                $allocation->statisticalActivity->is_done ? 'Selesai' : 'Berlangsung',
                $allocation->created_at->format('Y-m-d H:i:s'),
                $allocation->updated_at->format('Y-m-d H:i:s'),
            ], null, "A{$rowNumber}");

            // Apply conditional formatting for status
            $statusCell = "J{$rowNumber}";
            if ($allocation->statisticalActivity->is_done) {
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
        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set row height for header
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Add borders to all data
        $sheet->getStyle('A1:L' . ($rowNumber - 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB'], // Gray-300
                ],
            ],
        ]);

        return $this->saveSpreadsheet($spreadsheet, 'pml_allocations_export');
    }

    /**
     * Generate PML allocation import template
     */
    public function generateImportTemplate(): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = [
            'User ID (UUID)',
            'Statistical Activity ID (UUID)',
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
                'startColor' => ['rgb' => '7C3AED'],
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
        $sheet->getStyle('A1:B1')->applyFromArray($headerStyle);

        // Add sample data
        $sampleData = [
            [
                '9a234567-89ab-cdef-0123-456789abcdef',
                '8b123456-78cd-ef01-2345-6789abcdef01'
            ],
            [
                '7c345678-90ab-cdef-0123-456789abcdef',
                '8b123456-78cd-ef01-2345-6789abcdef01'
            ],
        ];
        $sheet->fromArray($sampleData, null, 'A2');

        // Add instructions
        $instructions = [
            ['Instructions:'],
            ['1. User ID: Required, must be valid UUID and exist in users table'],
            ['2. Statistical Activity ID: Required, must be valid UUID and exist'],
            ['3. Each row represents one PML allocation'],
            ['4. Duplicate allocations will be skipped'],
            ['5. Delete sample data before uploading'],
            ['6. Do not modify column headers'],
            ['7. Maximum 1000 rows per import'],
        ];

        $instructionRow = 5;
        foreach ($instructions as $instruction) {
            $sheet->fromArray($instruction, null, "D{$instructionRow}");
            $instructionRow++;
        }

        $sheet->getStyle('D5:D12')->getFont()->setItalic(true)->setSize(10);
        $sheet->getStyle('D5')->getFont()->setBold(true)->setSize(11);

        // Auto-size columns
        foreach (range('A', 'B') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->getColumnDimension('D')->setWidth(60);

        // Set row height
        $sheet->getRowDimension(1)->setRowHeight(25);

        return $this->saveSpreadsheet($spreadsheet, 'pml_allocation_import_template');
    }

    /**
     * Read Excel file for import
     */
    public function readExcelFile(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Get specific range (columns A to B)
        $highestRow = $sheet->getHighestRow();
        $rows = $sheet->rangeToArray('A1:B' . $highestRow, null, true, true, false);

        // Remove header row
        array_shift($rows);

        return $rows;
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