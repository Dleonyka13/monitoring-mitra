<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class PmlAllocation extends Model
{
    use HasUuid;

    protected $fillable = [
        'user_id', 'statistical_activity_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function statisticalActivity()
    {
        return $this->belongsTo(StatisticalActivity::class);
    }

    public function pclAllocations()
    {
        return $this->hasMany(PclAllocation::class);
    }
}
