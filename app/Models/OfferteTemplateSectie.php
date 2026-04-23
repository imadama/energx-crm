<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfferteTemplateSectie extends Model
{
    protected $table = 'offerte_template_secties';

    protected $fillable = ['template_id', 'type', 'titel', 'inhoud', 'volgorde'];

    protected $casts = ['inhoud' => 'array'];

    public function template(): BelongsTo
    {
        return $this->belongsTo(OfferteTemplate::class, 'template_id');
    }
}
