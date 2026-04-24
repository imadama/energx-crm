<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfferteRegel extends Model
{
    protected $table = 'offerte_regels';

    protected $fillable = [
        'offerte_id', 'product_id', 'naam', 'beschrijving',
        'aantal', 'eenheid', 'eenheidsprijs', 'btw_tarief', 'btw_bedrag', 'totaal',
        'volgorde', 'type', 'optioneel',
    ];

    protected $casts = [
        'eenheidsprijs' => 'decimal:2',
        'btw_bedrag'    => 'decimal:2',
        'totaal'        => 'decimal:2',
        'optioneel'     => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (OfferteRegel $regel) {
            if (in_array($regel->type, ['product', 'vrije_regel', 'korting', 'optioneel'])) {
                $regel->totaal     = round($regel->aantal * $regel->eenheidsprijs, 2);
                $regel->btw_bedrag = round($regel->totaal * $regel->btw_tarief / 100, 2);
            }
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
