<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class PclAllocation extends Model
{
    use HasUuid;

    protected $fillable = [
        'user_id', 'pml_allocation_id', 'statistical_activity_id', 'target'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pmlAllocation()
    {
        return $this->belongsTo(PmlAllocation::class);
    }

    public function statisticalActivity()
    {
        return $this->belongsTo(StatisticalActivity::class);
    }

    public function dailyProgressions()
    {
        return $this->hasMany(DailyProgression::class, 'pcl_allocation_id');
    }
}
