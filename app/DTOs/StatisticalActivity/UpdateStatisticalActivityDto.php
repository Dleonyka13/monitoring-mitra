<?php

namespace App\DTOs\StatisticalActivity;
use Illuminate\Http\Request;

/**
 * DTO untuk Update Statistical Activity
 */
class UpdateStatisticalActivityDto
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $start_date = null,
        public readonly ?string $end_date = null,
        public readonly ?int $total_target = null,
        public readonly ?bool $is_done = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->input('name'),
            start_date: $request->input('start_date'),
            end_date: $request->input('end_date'),
            total_target: $request->has('total_target') 
                ? (int) $request->input('total_target') 
                : null,
            is_done: $request->has('is_done') 
                ? (bool) $request->input('is_done') 
                : null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'total_target' => $this->total_target,
            'is_done' => $this->is_done,
        ], fn($value) => !is_null($value));
    }

    public function hasUpdates(): bool
    {
        return !empty($this->toArray());
    }
}