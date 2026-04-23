<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfferteSectie extends Model
{
    protected $table = 'offerte_secties';

    protected $fillable = ['offerte_id', 'type', 'titel', 'inhoud', 'volgorde'];

    protected $casts = ['inhoud' => 'array'];

    public function offerte(): BelongsTo
    {
        return $this->belongsTo(Offerte::class);
    }
}
