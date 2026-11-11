<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonevSubmission extends Model
{
    public $timestamps = false;

    protected $casts = [
        'answers' => 'array',
        'identity_snapshot' => 'array',
        'accreditation_snapshot' => 'array',
        'submitted_at' => 'datetime',
        'is_late' => 'boolean',
    ];
}
