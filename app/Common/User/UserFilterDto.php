<?php

namespace App\Common\User;

use Illuminate\Http\Request;

/**
 * DTO untuk Filter User
 */
class UserFilterDto
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?string $role = null,
        public readonly ?string $name = null,
        public readonly ?string $email = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            search: $request->input('search'),
            role: $request->input('role'),
            name: $request->input('name'),
            email: $request->input('email'),
        );
    }

    public function hasFilters(): bool
    {
        return !is_null($this->search) 
            || !is_null($this->role) 
            || !is_null($this->name) 
            || !is_null($this->email);
    }

    public function toArray(): array
    {
        return array_filter([
            'search' => $this->search,
            'role' => $this->role,
            'name' => $this->name,
            'email' => $this->email,
        ], fn($value) => !is_null($value));
    }
}
