<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasUuid;

class StatisticalActivity extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'statistical_activities';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'total_target',
        'is_done',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date'   => 'datetime',
        'is_done'     => 'boolean',
    ];
}
