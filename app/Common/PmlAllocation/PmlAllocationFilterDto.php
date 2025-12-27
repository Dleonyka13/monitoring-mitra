<?php

namespace App\Common\PmlAllocation;

use Illuminate\Http\Request;

/**
 * DTO untuk Filter PML Allocation
 */
class PmlAllocationFilterDto
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?string $user_id = null,
        public readonly ?string $statistical_activity_id = null,
        public readonly ?string $user_name = null,
        public readonly ?string $user_email = null,
        public readonly ?string $activity_name = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            search: $request->input('search'),
            user_id: $request->input('user_id'),
            statistical_activity_id: $request->input('statistical_activity_id'),
            user_name: $request->input('user_name'),
            user_email: $request->input('user_email'),
            activity_name: $request->input('activity_name'),
        );
    }

    public function hasFilters(): bool
    {
        return !is_null($this->search)
            || !is_null($this->user_id)
            || !is_null($this->statistical_activity_id)
            || !is_null($this->user_name)
            || !is_null($this->user_email)
            || !is_null($this->activity_name);
    }

    public function toArray(): array
    {
        return array_filter([
            'search' => $this->search,
            'user_id' => $this->user_id,
            'statistical_activity_id' => $this->statistical_activity_id,
            'user_name' => $this->user_name,
            'user_email' => $this->user_email,
            'activity_name' => $this->activity_name,
        ], fn($value) => !is_null($value));
    }
}
