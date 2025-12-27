<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Color;

class StatisticalActivityExcelService
{
    /**
     * Export statistical activities to Excel with enhanced styling
     */
    public function exportActivities(Collection $activities): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Statistical Activities');

        // Set column widths
        $columnWidths = [
            'A' => 25,  // ID
            'B' => 40,  // Name
            'C' => 20,  // Start Date
            'D' => 20,  // End Date
            'E' => 15,  // Total Target
            'F' => 15,  // Status
            'G' => 12,  // Is Done
            'H' => 18,  // Duration (Days)
            'I' => 20,  // Created At
            'J' => 20,  // Updated At
        ];

        foreach ($columnWidths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

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
                'name' => 'Calibri',
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2563EB'], // Blue-600
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '1E40AF'], // Blue-800
                ],
            ],
        ];
        $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(35);

        // Add data with alternating row colors
        $rowNumber = 2;
        foreach ($activities as $activity) {
            // Calculate duration in days
            $durationDays = 0;
            if ($activity->start_date && $activity->end_date) {
                $durationDays = $activity->start_date->diffInDays($activity->end_date);
            }

            // Prepare row data
            $rowData = [
                $activity->id,
                $activity->name,
                $activity->start_date ? $activity->start_date->format('Y-m-d H:i:s') : '',
                $activity->end_date ? $activity->end_date->format('Y-m-d H:i:s') : '',
                $activity->total_target,
                $activity->is_done ? 'Selesai' : 'Berlangsung',
                $activity->is_done ? 'Yes' : 'No',
                $durationDays,
                $activity->created_at->format('Y-m-d H:i:s'),
                $activity->updated_at->format('Y-m-d H:i:s'),
            ];

            $sheet->fromArray($rowData, null, "A{$rowNumber}");

            // Apply alternating row color
            $rowColor = ($rowNumber % 2 == 0) ? 'F9FAFB' : 'FFFFFF'; // Gray-50 : White
            $sheet->getStyle("A{$rowNumber}:J{$rowNumber}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $rowColor],
                ],
            ]);

            // Apply conditional formatting for Status column (F)
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
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
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
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
            }

            // Center align specific columns
            $sheet->getStyle("E{$rowNumber}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Total Target
            $sheet->getStyle("G{$rowNumber}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Is Done
            $sheet->getStyle("H{$rowNumber}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Duration

            $rowNumber++;
        }

        // Add borders to all data
        $lastRow = $rowNumber - 1;
        $sheet->getStyle("A1:J{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB'], // Gray-300
                ],
            ],
        ]);

        // Add summary at the bottom
        $summaryRow = $lastRow + 2;
        $sheet->mergeCells("A{$summaryRow}:D{$summaryRow}");
        $sheet->setCellValue("A{$summaryRow}", 'SUMMARY');
        $sheet->getStyle("A{$summaryRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => '1F2937'], // Gray-800
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E5E7EB'], // Gray-200
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Summary data
        $totalActivities = $activities->count();
        $completedActivities = $activities->where('is_done', true)->count();
        $ongoingActivities = $activities->where('is_done', false)->count();
        $completionRate = $totalActivities > 0 ? round(($completedActivities / $totalActivities) * 100, 2) : 0;

        $summaryDataRow = $summaryRow + 1;
        $sheet->setCellValue("A{$summaryDataRow}", 'Total Activities:');
        $sheet->setCellValue("B{$summaryDataRow}", $totalActivities);
        $sheet->setCellValue("C{$summaryDataRow}", 'Completed:');
        $sheet->setCellValue("D{$summaryDataRow}", $completedActivities);
        
        $summaryDataRow++;
        $sheet->setCellValue("A{$summaryDataRow}", 'Ongoing:');
        $sheet->setCellValue("B{$summaryDataRow}", $ongoingActivities);
        $sheet->setCellValue("C{$summaryDataRow}", 'Completion Rate:');
        $sheet->setCellValue("D{$summaryDataRow}", $completionRate . '%');

        // Style summary data
        $sheet->getStyle("A" . ($summaryRow + 1) . ":D" . $summaryDataRow)->applyFromArray([
            'font' => [
                'size' => 11,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB'],
                ],
            ],
        ]);

        // Bold labels
        $sheet->getStyle("A" . ($summaryRow + 1) . ":A{$summaryDataRow}")->getFont()->setBold(true);
        $sheet->getStyle("C" . ($summaryRow + 1) . ":C{$summaryDataRow}")->getFont()->setBold(true);

        // Freeze first row
        $sheet->freezePane('A2');

        return $this->saveSpreadsheet($spreadsheet, 'statistical_activities_export');
    }

    /**
     * Generate statistical activities import template with detailed instructions
     */
    public function generateImportTemplate(): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Import Template');

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(60); // Instructions column

        // Set headers
        $headers = [
            'Name *',
            'Start Date (YYYY-MM-DD HH:MM:SS) *',
            'End Date (YYYY-MM-DD HH:MM:SS) *',
            'Total Target *',
            'Is Done (true/false)'
        ];
        $sheet->fromArray($headers, null, 'A1');

        // Style header
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
                'name' => 'Calibri',
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2563EB'], // Blue-600
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '1E40AF'],
                ],
            ],
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(40);

        // Add sample data with styling
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
                'true'
            ],
            [
                'Survei Konsumsi Rumah Tangga',
                '2025-03-15 08:00:00',
                '2025-05-15 17:00:00',
                2500,
                'false'
            ],
        ];
        
        $sampleStartRow = 2;
        foreach ($sampleData as $index => $data) {
            $rowNum = $sampleStartRow + $index;
            $sheet->fromArray($data, null, "A{$rowNum}");
            
            // Alternating colors for sample data
            $bgColor = ($index % 2 == 0) ? 'FEF3C7' : 'FDE68A'; // Yellow shades
            $sheet->getStyle("A{$rowNum}:E{$rowNum}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $bgColor],
                ],
                'font' => [
                    'italic' => true,
                    'color' => ['rgb' => '78350F'], // Yellow-900
                ],
            ]);
        }

        // Add borders to sample data
        $lastSampleRow = $sampleStartRow + count($sampleData) - 1;
        $sheet->getStyle("A{$sampleStartRow}:E{$lastSampleRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB'],
                ],
            ],
        ]);

        // Add instructions section
        $instructionsStartRow = 2;
        
        // Main instruction header
        $sheet->setCellValue("G{$instructionsStartRow}", 'IMPORT INSTRUCTIONS');
        $sheet->getStyle("G{$instructionsStartRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F2937'], // Gray-800
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension($instructionsStartRow)->setRowHeight(25);

        // Detailed instructions
        $instructions = [
            '',
            'REQUIRED FIELDS (marked with *):',
            '',
            '1. Name:',
            '   - Required field',
            '   - Maximum 255 characters',
            '   - Example: "Survei Penduduk 2025"',
            '',
            '2. Start Date:',
            '   - Required field',
            '   - Format: YYYY-MM-DD HH:MM:SS',
            '   - Example: "2025-01-01 08:00:00"',
            '',
            '3. End Date:',
            '   - Required field',
            '   - Format: YYYY-MM-DD HH:MM:SS',
            '   - Must be after or equal to Start Date',
            '   - Example: "2025-03-31 17:00:00"',
            '',
            '4. Total Target:',
            '   - Required field',
            '   - Must be a positive integer (minimum 1)',
            '   - Example: 1000',
            '',
            '5. Is Done:',
            '   - Optional field',
            '   - Accepted values: "true", "false", "1", "0"',
            '   - Leave empty for default (false)',
            '   - Example: "true" or "false"',
            '',
            'IMPORTANT NOTES:',
            '',
            '• Delete all sample data (rows 2-4) before importing',
            '• Do NOT modify the column headers (row 1)',
            '• Ensure all required fields are filled',
            '• Check date format carefully',
            '• Save file as .xlsx or .xls format',
            '• Maximum file size: 5MB',
            '',
            'COMMON ERRORS TO AVOID:',
            '',
            '• Empty required fields',
            '• Incorrect date format',
            '• End date before start date',
            '• Non-numeric values in Total Target',
            '• Invalid values in Is Done field',
        ];

        $instructionRow = $instructionsStartRow + 1;
        foreach ($instructions as $instruction) {
            $sheet->setCellValue("G{$instructionRow}", $instruction);
            
            // Style section headers
            if (strpos($instruction, ':') !== false && strlen($instruction) < 20) {
                $sheet->getStyle("G{$instructionRow}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => '1F2937'],
                    ],
                ]);
            }
            
            // Style main section headers
            if (in_array($instruction, ['REQUIRED FIELDS (marked with *):', 'IMPORTANT NOTES:', 'COMMON ERRORS TO AVOID:'])) {
                $sheet->getStyle("G{$instructionRow}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '3B82F6'], // Blue-500
                    ],
                ]);
                $sheet->getRowDimension($instructionRow)->setRowHeight(20);
            }
            
            $instructionRow++;
        }

        // Style instructions text
        $sheet->getStyle("G" . ($instructionsStartRow + 1) . ":G{$instructionRow}")->applyFromArray([
            'font' => [
                'size' => 10,
                'name' => 'Calibri',
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
                'wrapText' => true,
            ],
        ]);

        // Add border around instructions
        $sheet->getStyle("G{$instructionsStartRow}:G{$instructionRow}")->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '1F2937'],
                ],
            ],
        ]);

        // Freeze first row
        $sheet->freezePane('A2');

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