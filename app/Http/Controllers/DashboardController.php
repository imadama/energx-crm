<?php

namespace App\Http\Controllers;

use App\Models\Klant;
use App\Models\Offerte;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'klanten'      => Klant::count(),
            'producten'    => Product::actief()->count(),
            'open'         => Offerte::whereIn('status', ['concept', 'verstuurd', 'bekeken'])->count(),
            'geaccepteerd' => Offerte::where('status', 'geaccepteerd')->count(),
        ];

        $recenteOffertes = Offerte::with('klant')
            ->latest()
            ->take(8)
            ->get();

        return view('dashboard.index', compact('stats', 'recenteOffertes'));
    }
}
