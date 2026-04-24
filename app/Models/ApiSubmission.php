<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiSubmission extends Model
{
    protected $table = 'api_submissions';

    protected $fillable = [
        'template_identifier',
        'communication_preference',
        'customer_email',
        'payload',
        'details',
        'offerte_id',
    ];

    protected $casts = [
        'payload' => 'array',
        'details' => 'array',
    ];

    public function offerte(): BelongsTo
    {
        return $this->belongsTo(Offerte::class);
    }
}

