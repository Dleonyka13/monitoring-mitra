<?php

namespace App\Helpers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PaginationHelper
{
    /**
     * Transform pagination data into a standardized format
     *
     * @param LengthAwarePaginator $paginator
     * @return array
     */
    public static function transform(LengthAwarePaginator $paginator): array
    {
        return [
            'data' => $paginator->items(),
            'meta' => [
                'total' => $paginator->total(),
                'count' => $paginator->count(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'total_pages' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ];
    }

    public static function getParams($request, int $defaultPerPage = 10): array
    {
        return [
            'per_page' => $request->input('per_page', $defaultPerPage),
            'page' => $request->input('page', 1),
        ];
    }
}