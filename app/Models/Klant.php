<?php

namespace App\Models;

use App\Scopes\TeamScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Klant extends Model
{
    protected $table = 'klanten';

    protected $fillable = [
        'team_id', 'soort', 'naam', 'straat', 'huisnummer',
        'postcode', 'stad', 'notities', 'bron',
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

    public function contactpersonen(): HasMany
    {
        return $this->hasMany(Contactpersoon::class);
    }

    public function offertes(): HasMany
    {
        return $this->hasMany(Offerte::class);
    }

    public function tickets(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(Ticket::class, Contactpersoon::class);
    }

    public function getAdresAttribute(): string
    {
        return trim("{$this->straat} {$this->huisnummer}, {$this->postcode} {$this->stad}");
    }
}
