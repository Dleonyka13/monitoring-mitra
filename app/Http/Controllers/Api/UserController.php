<?php

namespace App\Http\Controllers\Api;

use App\Common\User\UserFilterDto;
use App\Common\User\UserOrderDto;
use App\DTOs\User\CreateUserDto;
use App\DTOs\User\ImportUserDto;
use App\DTOs\User\UpdateUserDto;
use App\Helpers\PaginationHelper;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\ExcelService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
        private readonly ExcelService $excelService
    ) {}

    /**
     * Store a newly created user
     * POST /api/{api_name}/{version}/users
     */
    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(['mitra', 'pegawai', 'kepala', 'admin'])],
        ]);

        if ($validator->fails()) {
            return ResponseHelper::error(
                'Validation failed',
                $validator->errors(),
                422
            );
        }

        $dto = CreateUserDto::fromRequest($request);
        $user = $this->userService->create($dto);

        return ResponseHelper::success(
            new UserResource($user),
            'User created successfully',
            201
        );
    }

    /**
     * Get paginated users list with filters and sorting
     * GET /api/{api_name}/{version}/users
     */
    
    public function findAll(Request $request): JsonResponse
    {
        $params = PaginationHelper::getParams($request);
        $filter = UserFilterDto::fromRequest($request);
        $order = UserOrderDto::fromRequest($request);

        $users = $this->userService->pagination(
            $params['per_page'],
            $filter,
            $order
        );

        $transformedData = PaginationHelper::transform($users);
        $transformedData['data'] = UserResource::collection(collect($transformedData['data']));

        return ResponseHelper::success(
            $transformedData,
            'Users retrieved successfully'
        );
    }

    /**
     * Display the specified user
     * GET /api/{api_name}/{version}/users/{id}
     */
    public function findById(string $id): JsonResponse
    {
        $user = $this->userService->findById($id);

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
     * PUT/PATCH /api/{api_name}/{version}/users/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = $this->userService->findById($id);

        if (!$user) {
            return ResponseHelper::notFound('User not found');
        }

        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return ResponseHelper::error(
                'Validation failed',
                $validator->errors(),
                422
            );
        }

        $dto = UpdateUserDto::fromRequest($request);
        $user = $this->userService->update($user, $dto);

        return ResponseHelper::success(
            new UserResource($user),
            'User updated successfully'
        );
    }

    /**
     * Remove the specified user (Soft Delete)
     * DELETE /api/{api_name}/{version}/users/{id}
     */
    public function delete(string $id): JsonResponse
    {
        $user = $this->userService->findById($id);

        if (!$user) {
            return ResponseHelper::notFound('User not found');
        }

        // Prevent user from deleting themselves
        if ($user->id === auth()->id()) {
            return ResponseHelper::error(
                'You cannot delete your own account',
                null,
                403
            );
        }

        $this->userService->delete($user);

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

    /**
     * Download user import template
     * GET /api/{api_name}/{version}/users/template/download
     */
    public function downloadTemplate(): BinaryFileResponse
    {
        $filePath = $this->excelService->generateUserTemplate();
        $fileName = basename($filePath);

        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * Import users from Excel file
     * POST /api/{api_name}/{version}/users/import
     */
    public function import(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls|max:2048',
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
            $rows = $this->excelService->readExcelFile($file->getRealPath());

            $successCount = 0;
            $failedCount = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2;

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                $dto = ImportUserDto::fromArray($row, $rowNumber);

                // Validate user data
                $validation = $this->userService->validateImportUser($dto);

                if (!$validation['valid']) {
                    $failedCount++;
                    $errors[] = [
                        'row' => $rowNumber,
                        'email' => $dto->email ?: 'N/A',
                        'errors' => $validation['errors'],
                    ];
                    continue;
                }

                try {
                    $this->userService->createFromImport($dto);
                    $successCount++;
                } catch (\Exception $e) {
                    $failedCount++;
                    $errors[] = [
                        'row' => $rowNumber,
                        'email' => $dto->email ?: 'N/A',
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
     * Export users to Excel
     * GET /api/{api_name}/{version}/users/export
     */
    public function export(Request $request): BinaryFileResponse
    {
        $filter = UserFilterDto::fromRequest($request);
        $order = UserOrderDto::fromRequest($request);

        $users = $this->userService->findAll($filter, $order);
        $filePath = $this->excelService->exportUsers($users);
        $fileName = basename($filePath);

        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }
}