<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfferteRegel extends Model
{
    protected $fillable = [
        'offerte_id', 'product_id', 'naam', 'beschrijving',
        'aantal', 'eenheidsprijs', 'totaal', 'volgorde',
    ];

    protected $casts = [
        'eenheidsprijs' => 'decimal:2',
        'totaal'        => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (OfferteRegel $regel) {
            $regel->totaal = round($regel->aantal * $regel->eenheidsprijs, 2);
        });

        static::saved(function (OfferteRegel $regel) {
            $regel->offerte->berekenTotalen();
        });

        static::deleted(function (OfferteRegel $regel) {
            $regel->offerte->berekenTotalen();
        });
    }

    public function offerte(): BelongsTo
    {
        return $this->belongsTo(Offerte::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
