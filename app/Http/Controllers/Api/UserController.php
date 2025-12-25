<?php

namespace App\Http\Controllers\Api;

use App\Helpers\PaginationHelper;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $params = PaginationHelper::getParams($request);
        $search = $request->input('search');
        $role = $request->input('role');

        $query = User::query();

        // Search by name or email
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($role) {
            $query->where('role', $role);
        }

        // Order by latest
        $query->orderBy('created_at', 'desc');

        $users = $query->paginate($params['per_page']);

        // Transform users using UserResource
        $transformedData = PaginationHelper::transform($users);
        $transformedData['data'] = UserResource::collection(collect($transformedData['data']));

        return ResponseHelper::success(
            $transformedData,
            'Users retrieved successfully'
        );
    }

    /**
     * Store a newly created user
     * POST /api/{api_name}/{version}/users
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(['mitra', 'pegawai', 'kepala', 'admin'])],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return ResponseHelper::success(
            new UserResource($user),
            'User created successfully',
            201
        );
    }

    /**
     * Display the specified user
     * GET /api/{api_name}/{version}/users/{id}
     */
    public function show(string $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return ResponseHelper::notFound('User not found');
        }

        return ResponseHelper::success(
            new UserResource($user),
            'User retrieved successfully'
        );
    }

    /**
     * Update the specified user
     * PUT /api/{api_name}/{version}/users/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return ResponseHelper::notFound('User not found');
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'password' => 'sometimes|required|string|min:8',
            'role' => ['sometimes', 'required', Rule::in(['mitra', 'pegawai', 'kepala', 'admin'])],
        ]);

        // Update hanya field yang dikirim
        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->has('role')) {
            $user->role = $request->role;
        }

        $user->save();

        return ResponseHelper::success(
            new UserResource($user),
            'User updated successfully'
        );
    }

    /**
     * Remove the specified user (Soft Delete)
     * DELETE /api/{api_name}/{version}/users/{id}
     */
    public function destroy(string $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return ResponseHelper::notFound('User not found');
        }

        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return ResponseHelper::error(
                'You cannot delete your own account',
                null,
                403
            );
        }

        $user->delete();

        return ResponseHelper::success(
            null,
            'User deleted successfully'
        );
    }

    /**
     * Get authenticated user profile
     * GET /api/{api_name}/{version}/me
     */
    public function me(Request $request): JsonResponse
    {
        return ResponseHelper::success(
            new UserResource($request->user()),
            'User profile retrieved successfully'
        );
    }

    public function downloadTemplate(): \Symfony\Component\HttpFoundation\BinaryFileResponse
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

        // Add notes/instructions
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

        // Set row height
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Generate file
        $writer = new Xlsx($spreadsheet);
        $fileName = 'user_import_template_' . date('Ymd_His') . '.xlsx';
        $tempFile = storage_path('app/temp/' . $fileName);

        // Create temp directory if not exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * Import users from Excel file
     * POST /api/{api_name}/{version}/users/import
     */
    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:2048',
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            
            // --- PERBAIKAN DI SINI ---
            // Ambil range yang spesifik saja (Kolom A sampai D) 
            // agar kolom F dan seterusnya tidak ikut masuk ke array
            $highestRow = $sheet->getHighestRow();
            $rows = $sheet->rangeToArray('A1:D' . $highestRow, null, true, true, false);

            // Hapus header row (Baris 1)
            array_shift($rows);

            $successCount = 0;
            $failedCount = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2;

                // Row sekarang hanya berisi 4 elemen (index 0-3): [Name, Email, Password, Role]
                // Skip jika baris benar-benar kosong
                if (empty(array_filter($row))) {
                    continue;
                }

                // Validasi data baris
                $validator = Validator::make([
                    'name'     => $row[0] ?? null,
                    'email'    => $row[1] ?? null,
                    'password' => $row[2] ?? null,
                    'role'     => $row[3] ?? null,
                ], [
                    'name'     => 'required|string|max:255',
                    'email'    => 'required|string|email|max:255|unique:users',
                    'password' => 'required|string|min:8',
                    'role'     => ['required', Rule::in(['mitra', 'pegawai', 'kepala', 'admin'])],
                ]);

                if ($validator->fails()) {
                    $failedCount++;
                    $errors[] = [
                        'row' => $rowNumber,
                        'email' => $row[1] ?? 'N/A',
                        'errors' => $validator->errors()->all(),
                    ];
                    continue;
                }

                try {
                    User::create([
                        'name'     => $row[0],
                        'email'    => $row[1],
                        'password' => Hash::make($row[2]),
                        'role'     => $row[3],
                    ]);
                    $successCount++;
                } catch (\Exception $e) {
                    $failedCount++;
                    $errors[] = [
                        'row' => $rowNumber,
                        'email' => $row[1] ?? 'N/A',
                        'errors' => [$e->getMessage()],
                    ];
                }
            }

            $message = "Import selesai. Berhasil: {$successCount}, Gagal: {$failedCount}";

            return ResponseHelper::success([
                'success_count' => $successCount,
                'failed_count' => $failedCount,
                'errors' => $errors,
            ], $message);

        } catch (\Exception $e) {
            return ResponseHelper::error(
                'Gagal mengimport user',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Export all users to Excel
     * GET /api/{api_name}/{version}/users/export
     */
    public function export(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $search = $request->input('search');
        $role = $request->input('role');

        $query = User::query();

        // Apply same filters as index method
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role) {
            $query->where('role', $role);
        }

        $users = $query->orderBy('created_at', 'desc')->get();

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

        // Generate file
        $writer = new Xlsx($spreadsheet);
        $fileName = 'users_export_' . date('Ymd_His') . '.xlsx';
        $tempFile = storage_path('app/temp/' . $fileName);

        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}