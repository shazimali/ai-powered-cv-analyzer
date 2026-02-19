<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CvAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'status',
        'report',
        'user_details',
        'job_details',
        'cv_path',
    ];

    protected $casts = [
        'user_details' => 'array',
        'job_details' => 'array',
    ];
}
