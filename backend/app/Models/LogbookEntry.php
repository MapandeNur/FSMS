<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogbookEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'placement_id',
        'entry_date',
        'activities',
        'skills_learned',
        'challenges',
        'hours_worked',
        'status',
        'supervisor_comment',
        'reviewed_by',
        'reviewed_at',
        'submitted_at',
    ];

    protected $casts = [
        'entry_date' => 'date:Y-m-d',
        'reviewed_at' => 'datetime',
        'submitted_at' => 'datetime',
        'hours_worked' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}