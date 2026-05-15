<?php

namespace App\Models;

use App\Scopes\TeamScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiSubmission extends Model
{
    protected $table = 'api_submissions';

    protected $fillable = [
        'team_id',
        'template_identifier',
        'communication_preference',
        'customer_email',
        'payload',
        'details',
        'offerte_id',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new TeamScope());
    }

    protected $casts = [
        'payload' => 'array',
        'details' => 'array',
    ];

    public function offerte(): BelongsTo
    {
        return $this->belongsTo(Offerte::class);
    }
}

