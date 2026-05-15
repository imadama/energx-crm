<?php

namespace App\Http\Controllers;

use App\Models\ApiSubmission;
use Illuminate\View\View;

class AanvraagController extends Controller
{
    public function index(): View
    {
        $aanvragen = ApiSubmission::query()
            ->with('offerte.klant')
            ->orderByDesc('created_at')
            ->paginate(25);

        return view('aanvragen.index', compact('aanvragen'));
    }
}
