<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfferteTemplateRegel extends Model
{
    protected $table = 'offerte_template_regels';

    protected $fillable = ['template_id', 'product_id', 'naam', 'beschrijving', 'aantal', 'eenheidsprijs', 'volgorde'];

    protected $casts = ['eenheidsprijs' => 'decimal:2'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
