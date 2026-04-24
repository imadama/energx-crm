<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfferteTemplate extends Model
{
    protected $table = 'offerte_templates';

    protected $fillable = ['naam', 'beschrijving', 'categorie', 'identifier'];

    public function secties(): HasMany
    {
        return $this->hasMany(OfferteTemplateSectie::class, 'template_id')->orderBy('volgorde');
    }

    public function regels(): HasMany
    {
        return $this->hasMany(OfferteTemplateRegel::class, 'template_id')->orderBy('volgorde');
    }
}
