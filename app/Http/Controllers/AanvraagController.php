<?php

namespace App\Http\Controllers;

use App\Models\ApiSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AanvraagController extends Controller
{
    public function index(): View
    {
        $kolommen = [
            'nieuw'          => ['label' => 'Nieuw',           'color' => '#f59e0b', 'bg' => '#fffbeb'],
            'in_behandeling' => ['label' => 'In behandeling',  'color' => '#3b82f6', 'bg' => '#eff6ff'],
            'offerte_gemaakt'=> ['label' => 'Offerte gemaakt', 'color' => '#8b5cf6', 'bg' => '#f5f3ff'],
            'afgerond'       => ['label' => 'Afgerond',        'color' => '#10b981', 'bg' => '#ecfdf5'],
            'afgewezen'      => ['label' => 'Afgewezen',       'color' => '#ef4444', 'bg' => '#fef2f2'],
        ];

        $aanvragen = ApiSubmission::with('offerte.klant')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('status');

        return view('aanvragen.index', compact('aanvragen', 'kolommen'));
    }

    public function updateStatus(Request $request, ApiSubmission $aanvraag): RedirectResponse
    {
        $request->validate([
            'status'  => 'required|in:nieuw,in_behandeling,offerte_gemaakt,afgerond,afgewezen',
            'notitie' => 'nullable|string|max:1000',
        ]);

        $aanvraag->update([
            'status'  => $request->status,
            'notitie' => $request->notitie ?? $aanvraag->notitie,
        ]);

        return back()->with('success', 'Status bijgewerkt.');
    }
}
