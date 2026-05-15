<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->header('X-Api-Key');

        if (!$key) {
            return response()->json(['message' => 'API-sleutel ontbreekt.'], 401);
        }

        $apiKey = ApiKey::where('key', $key)->where('actief', true)->first();

        if (!$apiKey) {
            return response()->json(['message' => 'Ongeldige of inactieve API-sleutel.'], 401);
        }

        $request->attributes->set('api_team_id', $apiKey->team_id);
        $request->attributes->set('api_key_id', $apiKey->id);

        return $next($request);
    }
}
