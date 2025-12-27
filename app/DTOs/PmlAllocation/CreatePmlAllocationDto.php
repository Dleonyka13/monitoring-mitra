<?php

namespace App\DTOs\PmlAllocation;

use Illuminate\Http\Request;

/**
 * DTO untuk Create PML Allocation
 */
class CreatePmlAllocationDto
{
    public function __construct(
        public readonly string $user_id,
        public readonly string $statistical_activity_id,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            user_id: $request->input('user_id'),
            statistical_activity_id: $request->input('statistical_activity_id'),
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->user_id,
            'statistical_activity_id' => $this->statistical_activity_id,
        ];
    }
} 