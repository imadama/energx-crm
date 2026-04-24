<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfferteTemplate extends Model
{
    protected $table = 'offerte_templates';

    protected $fillable = ['naam', 'beschrijving', 'categorie', 'document'];

    protected $casts = ['document' => 'array'];
}
