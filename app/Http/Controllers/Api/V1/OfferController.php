<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\OfferApi\OfferApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OfferController extends Controller
{
    public function store(Request $request, OfferApiService $service): JsonResponse
    {
        $data = $request->validate([
            'customer' => 'required|array',
            'customer.name' => 'required|string|max:255',
            'customer.email' => 'required|email|max:255',
            'customer.phone' => 'nullable|string|max:255',
            'customer.street' => 'nullable|string|max:255',
            'customer.housenumber' => 'nullable|string|max:255',
            'customer.postalcode' => 'nullable|string|max:255',
            'customer.city' => 'nullable|string|max:255',
            'customer.country' => 'required|in:NL,BE',
            'communicationPreference' => 'required|in:email,whatsapp,bellen',
            'offerTemplateId' => 'required|string|max:255',
            'details' => 'required|array',
        ]);

        try {
            $result = $service->createOfferFromApi($data);
        } catch (ValidationException $e) {
            throw $e;
        }

        return response()->json([
            'offerteId' => $result->offerte->id,
            'offerteNummer' => $result->offerte->nummer,
            'viewerUrl' => $result->offerte->viewer_url,
            'warnings' => $result->warnings,
        ], 201);
    }
}

