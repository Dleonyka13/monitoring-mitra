<?php

namespace App\DTOs\User;

/**
 * DTO untuk Import User dari file Excel/CSV
 */
class ImportUserDto
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly string $role,
        public readonly int $rowNumber,
    ) {}

    public static function fromArray(array $row, int $rowNumber): self
    {
        return new self(
            name: $row[0] ?? '',
            email: $row[1] ?? '',
            password: $row[2] ?? '',
            role: $row[3] ?? '',
            rowNumber: $rowNumber,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'role' => $this->role,
        ];
    }
}