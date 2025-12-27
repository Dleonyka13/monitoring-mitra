<?php

namespace App\Common\StatisticalActivity;

use Illuminate\Http\Request;

/**
 * DTO untuk Filter Statistical Activity
 */
class StatisticalActivityFilterDto
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?string $name = null,
        public readonly ?bool $is_done = null,
        public readonly ?string $start_date_from = null,
        public readonly ?string $start_date_to = null,
        public readonly ?string $end_date_from = null,
        public readonly ?string $end_date_to = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            search: $request->input('search'),
            name: $request->input('name'),
            is_done: $request->has('is_done') 
                ? filter_var($request->input('is_done'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
                : null,
            start_date_from: $request->input('start_date_from'),
            start_date_to: $request->input('start_date_to'),
            end_date_from: $request->input('end_date_from'),
            end_date_to: $request->input('end_date_to'),
        );
    }

    public function hasFilters(): bool
    {
        return !is_null($this->search) 
            || !is_null($this->name) 
            || !is_null($this->is_done)
            || !is_null($this->start_date_from)
            || !is_null($this->start_date_to)
            || !is_null($this->end_date_from)
            || !is_null($this->end_date_to);
    }

    public function toArray(): array
    {
        return array_filter([
            'search' => $this->search,
            'name' => $this->name,
            'is_done' => $this->is_done,
            'start_date_from' => $this->start_date_from,
            'start_date_to' => $this->start_date_to,
            'end_date_from' => $this->end_date_from,
            'end_date_to' => $this->end_date_to,
        ], fn($value) => !is_null($value));
    }
}