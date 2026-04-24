<?php

namespace App\Services\OfferApi;

use App\Models\Offerte;

class OfferApiResult
{
    public function __construct(
        public readonly Offerte $offerte,
        /** @var string[] */
        public readonly array $warnings = [],
    ) {
    }
}

