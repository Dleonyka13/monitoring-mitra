<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatisticalActivity extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'total_target',
        'is_done',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_done' => 'boolean',
        'is_active' => 'boolean',
        'total_target' => 'integer',
    ];

    /**
     * Relationship: Statistical Activity has many PCL Allocations
     */
    public function pclAllocations(): HasMany
    {
        return $this->hasMany(PclAllocation::class, 'statistical_activity_id');
    }

    /**
     * Relationship: Statistical Activity has many PML Allocations
     */
    public function pmlAllocations(): HasMany
    {
        return $this->hasMany(PmlAllocation::class, 'statistical_activity_id');
    }

    /**
     * Scope untuk filter hanya yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk filter yang sudah selesai
     */
    public function scopeDone($query)
    {
        return $query->where('is_done', true);
    }

    /**
     * Scope untuk filter yang belum selesai
     */
    public function scopeNotDone($query)
    {
        return $query->where('is_done', false);
    }

    /**
     * Scope untuk filter by user
     */
    public function scopeByUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope untuk filter by activity
     */
    public function scopeByActivity($query, string $activityId)
    {
        return $query->where('statistical_activity_id', $activityId);
    }

    /**
     * Check if allocation already exists
     */
    public static function allocationExists(string $userId, string $activityId): bool
    {
        return self::where('user_id', $userId)
            ->where('statistical_activity_id', $activityId)
            ->exists();
    }
}