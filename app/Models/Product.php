<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    protected $table = 'producten';

    protected $fillable = [
        'naam', 'beschrijving', 'prijs', 'categorie', 'merk', 'actief',
    ];

    protected $casts = [
        'prijs'  => 'decimal:2',
        'actief' => 'boolean',
    ];

    public function scopeActief(Builder $query): Builder
    {
        return $query->where('actief', true);
    }
}
