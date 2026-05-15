<?php

namespace App\Models;

use App\Scopes\TeamScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfferteTemplate extends Model
{
    protected $table = 'offerte_templates';

    protected $fillable = ['team_id', 'naam', 'beschrijving', 'categorie', 'identifier'];

    protected static function booted(): void
    {
        static::addGlobalScope(new TeamScope());
        static::creating(function (self $model) {
            if (empty($model->team_id) && auth()->check()) {
                $model->team_id = auth()->user()->team_id;
            }
        });
    }

    public function secties(): HasMany
    {
        return $this->hasMany(OfferteTemplateSectie::class, 'template_id')->orderBy('volgorde');
    }

    public function regels(): HasMany
    {
        return $this->hasMany(OfferteTemplateRegel::class, 'template_id')->orderBy('volgorde');
    }
}
