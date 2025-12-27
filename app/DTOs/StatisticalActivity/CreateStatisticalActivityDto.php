<?php

namespace App\DTOs\StatisticalActivity;

use Illuminate\Http\Request;

/**
 * DTO untuk Create Statistical Activity
 */
class CreateStatisticalActivityDto
{
    public function __construct(
        public readonly string $name,
        public readonly string $start_date,
        public readonly string $end_date,
        public readonly int $total_target,
        public readonly bool $is_done = false,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->input('name'),
            start_date: $request->input('start_date'),
            end_date: $request->input('end_date'),
            total_target: (int) $request->input('total_target'),
            is_done: (bool) $request->input('is_done', false),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'total_target' => $this->total_target,
            'is_done' => $this->is_done,
            'is_active' => true,
        ];
    }
}