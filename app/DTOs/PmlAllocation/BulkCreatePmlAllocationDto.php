<?php

namespace App\DTOs\PmlAllocation;

use Illuminate\Http\Request;

/**
 * DTO untuk Bulk Create PML Allocations
 */
class BulkCreatePmlAllocationDto
{
    public function __construct(
        public readonly string $statistical_activity_id,
        public readonly array $user_ids,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            statistical_activity_id: $request->input('statistical_activity_id'),
            user_ids: $request->input('user_ids', []),
        );
    }

    public function toArray(): array
    {
        return [
            'statistical_activity_id' => $this->statistical_activity_id,
            'user_ids' => $this->user_ids,
        ];
    }
}