<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelService
{
    /**
     * Generate user import template
     */
    public function generateUserTemplate(): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        $headers = ['Name', 'Email', 'Password', 'Role'];
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
                'startColor' => ['rgb' => '4472C4'],
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
        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);

        // Add sample data
        $sampleData = [
            ['John Doe', 'john@example.com', 'password123', 'mitra'],
            ['Jane Smith', 'jane@example.com', 'password123', 'pegawai'],
        ];
        $sheet->fromArray($sampleData, null, 'A2');

        // Add instructions
        $sheet->setCellValue('F2', 'Instructions:');
        $sheet->setCellValue('F3', '1. Fill in user data starting from row 2');
        $sheet->setCellValue('F4', '2. Role options: mitra, pegawai, kepala, admin');
        $sheet->setCellValue('F5', '3. Password minimum 8 characters');
        $sheet->setCellValue('F6', '4. Email must be unique');
        $sheet->setCellValue('F7', '5. Delete sample data before uploading');

        $sheet->getStyle('F2:F7')->getFont()->setItalic(true)->setSize(10);

        // Auto-size columns
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->getColumnDimension('F')->setWidth(40);
        $sheet->getRowDimension(1)->setRowHeight(25);

        return $this->saveSpreadsheet($spreadsheet, 'user_import_template');
    }

    /**
     * Export users to Excel
     */
    public function exportUsers(Collection $users): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = ['ID', 'Name', 'Email', 'Role', 'Created At'];
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
                'startColor' => ['rgb' => '4472C4'],
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

        // Add user data
        $rowNumber = 2;
        foreach ($users as $user) {
            $sheet->fromArray([
                $user->id,
                $user->name,
                $user->email,
                $user->role,
                $user->created_at->format('Y-m-d H:i:s'),
            ], null, "A{$rowNumber}");
            $rowNumber++;
        }

        // Auto-size columns
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $this->saveSpreadsheet($spreadsheet, 'users_export');
    }

    /**
     * Read Excel file and return rows
     */
    public function readExcelFile(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Get specific range (columns A to D)
        $highestRow = $sheet->getHighestRow();
        $rows = $sheet->rangeToArray('A1:D' . $highestRow, null, true, true, false);

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