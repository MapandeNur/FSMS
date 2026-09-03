<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'registration_number',
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'date_of_birth',
        'email',
        'phone',
        'institution_name',
        'programme_of_study',
        'year_of_study',
        'start_date',
        'end_date',
        'status',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}