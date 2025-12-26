<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatisticalActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'start_date' => $this->start_date?->format('Y-m-d H:i:s'),
            'end_date' => $this->end_date?->format('Y-m-d H:i:s'),
            'total_target' => $this->total_target,
            'is_done' => $this->is_done,
            'is_active' => $this->is_active,
            
            // Additional info
            'duration_days' => $this->start_date && $this->end_date 
                ? $this->start_date->diffInDays($this->end_date) 
                : null,
            
            'status' => $this->is_done ? 'Selesai' : 'Berlangsung',
            
            // // Counts (optional, jika ingin menampilkan jumlah alokasi)
            // 'pcl_count' => $this->whenLoaded('pclAllocations', function () {
            //     return $this->pclAllocations->count();
            // }),
            // 'pml_count' => $this->whenLoaded('pmlAllocations', function () {
            //     return $this->pmlAllocations->count();
            // }),
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}