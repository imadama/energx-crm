<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiField extends Model
{
    protected $table = 'api_fields';

    protected $fillable = [
        'key',
        'label',
        'type',
        'allowed_values',
    ];

    protected $casts = [
        'allowed_values' => 'array',
    ];
}

