<?php

namespace App\Models;

use App\Scopes\TeamScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    protected $table = 'producten';

    protected $fillable = [
        'team_id', 'naam', 'beschrijving', 'prijs', 'categorie', 'merk', 'actief',
        'order',
        'generator_mode',
        'generator_conditions',
        'generator_value_rules',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new TeamScope());
        static::creating(function (self $model) {
            if (empty($model->team_id) && auth()->check()) {
                $model->team_id = auth()->user()->team_id;
            }
        });
    }

    protected $casts = [
        'prijs'  => 'decimal:2',
        'actief' => 'boolean',
        'order' => 'integer',
        'generator_conditions' => 'array',
        'generator_value_rules' => 'array',
    ];

    public function scopeActief(Builder $query): Builder
    {
        return $query->where('actief', true);
    }
}
