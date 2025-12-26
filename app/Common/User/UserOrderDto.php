<?php

namespace App\Common\User;

use Illuminate\Http\Request;

/**
 * DTO untuk Order User
 */

class UserOrderDto
{
    public const ALLOWED_FIELDS = ['name', 'email', 'role', 'created_at', 'updated_at'];
    public const ALLOWED_DIRECTIONS = ['asc', 'desc'];

    public function __construct(
        public readonly string $orderBy = 'created_at',
        public readonly string $orderDirection = 'desc',
    ) {}

    public static function fromRequest(Request $request): self
    {
        $orderBy = $request->input('order_by', 'created_at');
        $orderDirection = $request->input('order_direction', 'desc');

        // Validate order_by field
        if (!in_array($orderBy, self::ALLOWED_FIELDS)) {
            $orderBy = 'created_at';
        }

        // Validate order_direction
        if (!in_array(strtolower($orderDirection), self::ALLOWED_DIRECTIONS)) {
            $orderDirection = 'desc';
        }

        return new self(
            orderBy: $orderBy,
            orderDirection: strtolower($orderDirection),
        );
    }

    public function toArray(): array
    {
        return [
            'order_by' => $this->orderBy,
            'order_direction' => $this->orderDirection,
        ];
    }
}