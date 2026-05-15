<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApiKeyController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        if ($user->is_superadmin) {
            $teams = Team::with(['apiKeys'])->orderBy('naam')->get();
        } else {
            $teams = Team::with(['apiKeys'])
                ->where('id', $user->team_id)
                ->get();
        }

        return view('api-keys.index', compact('teams'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['naam' => 'required|string|max:100', 'team_id' => 'required|integer']);

        $user = auth()->user();
        $teamId = (int) $request->team_id;

        if (!$user->is_superadmin && $teamId !== $user->team_id) {
            abort(403);
        }

        ApiKey::generate($teamId, $request->naam);

        return back()->with('success', 'API-sleutel aangemaakt.');
    }

    public function destroy(ApiKey $apiKey): RedirectResponse
    {
        $user = auth()->user();

        if (!$user->is_superadmin && $apiKey->team_id !== $user->team_id) {
            abort(403);
        }

        $apiKey->delete();

        return back()->with('success', 'API-sleutel verwijderd.');
    }
}
