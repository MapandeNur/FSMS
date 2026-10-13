<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'head_user_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Department head.
     */
    public function headUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_user_id');
    }

    /**
     * Placements belonging to this department.
     */
    public function placements(): HasMany
    {
        return $this->hasMany(Placement::class, 'department_id');
    }

    /**
     * Active placements only.
     */
    public function activePlacements(): HasMany
    {
        return $this->hasMany(Placement::class, 'department_id')
            ->where('status', 'active');
    }
}