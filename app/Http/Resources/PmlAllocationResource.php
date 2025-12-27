<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PmlAllocationResource extends JsonResource
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
            'user_id' => $this->user_id,
            'statistical_activity_id' => $this->statistical_activity_id,

            // User details
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'role' => $this->user->role,
            ],

            // Statistical Activity details
            'statistical_activity' => [
                'id' => $this->statisticalActivity->id,
                'name' => $this->statisticalActivity->name,
                'start_date' => $this->statisticalActivity->start_date?->format('Y-m-d H:i:s'),
                'end_date' => $this->statisticalActivity->end_date?->format('Y-m-d H:i:s'),
                'total_target' => $this->statisticalActivity->total_target,
                'is_done' => $this->statisticalActivity->is_done,
                'status' => $this->statisticalActivity->is_done ? 'Selesai' : 'Berlangsung',
            ],

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}