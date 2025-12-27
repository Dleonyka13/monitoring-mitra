<?php

namespace App\DTOs\PmlAllocation;

use Illuminate\Http\Request;

/**
 * DTO untuk Update PML Allocation
 */
class UpdatePmlAllocationDto
{
    public function __construct(
        public readonly ?string $user_id = null,
        public readonly ?string $statistical_activity_id = null,
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
        return array_filter([
            'user_id' => $this->user_id,
            'statistical_activity_id' => $this->statistical_activity_id,
        ], fn($value) => !is_null($value));
    }

    public function hasUpdates(): bool
    {
        return !empty($this->toArray());
    }
}