<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class DailyProgression extends Model
{
    use HasUuid;

    protected $fillable = [
        'pcl_allocation_id',
        'respondent_name',
        'address',
        'long',
        'lat',
        'status'
    ];

    public function pclAllocation()
    {
        return $this->belongsTo(PclAllocation::class, 'pcl_allocation_id');
    }
}
