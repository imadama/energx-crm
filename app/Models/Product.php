<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    protected $table = 'producten';

    protected $fillable = [
        'naam', 'beschrijving', 'prijs', 'categorie', 'merk', 'actief',
        'order',
        'generator_mode',
        'generator_conditions',
        'generator_value_rules',
    ];

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
