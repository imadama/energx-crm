<?php

namespace App\Http\Controllers;

use App\Models\Klant;
use Illuminate\Http\Request;

class KlantController extends Controller
{
    public function index()
    {
        $klanten = Klant::withCount('offertes')->latest()->paginate(20);
        return view('klanten.index', compact('klanten'));
    }

    public function create()
    {
        return view('klanten.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'soort'      => 'required|in:bedrijf,particulier',
            'naam'       => 'required_if:soort,bedrijf|nullable|string|max:255',
            'voornaam'   => 'required|string|max:255',
            'achternaam' => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            'telefoon'   => 'nullable|string|max:20',
            'straat'     => 'nullable|string|max:255',
            'huisnummer' => 'nullable|string|max:20',
            'postcode'   => 'nullable|string|max:10',
            'stad'       => 'nullable|string|max:255',
            'notities'   => 'nullable|string',
            'bron'       => 'required|in:website,telefoon,email,doorverwijzing,anders',
        ]);

        if ($data['soort'] === 'particulier') {
            $data['naam'] = $data['voornaam'] . ' ' . $data['achternaam'];
        }

        $klant = Klant::create([
            'soort'      => $data['soort'],
            'naam'       => $data['naam'],
            'straat'     => $data['straat'],
            'huisnummer' => $data['huisnummer'],
            'postcode'   => $data['postcode'],
            'stad'       => $data['stad'],
            'notities'   => $data['notities'],
            'bron'       => $data['bron'],
        ]);

        $klant->contactpersonen()->create([
            'voornaam'   => $data['voornaam'],
            'achternaam' => $data['achternaam'],
            'email'      => $data['email'],
            'telefoon'   => $data['telefoon'],
        ]);

        return redirect()->route('klanten.show', $klant)->with('success', 'Klant en contactpersoon aangemaakt.');
    }

    public function show(Klant $klant)
    {
        $offertes = $klant->offertes()->latest()->get();
        $contactpersonen = $klant->contactpersonen()->get();
        $tickets = $klant->tickets()->latest()->get();
        return view('klanten.show', compact('klant', 'offertes', 'contactpersonen', 'tickets'));
    }

    public function edit(Klant $klant)
    {
        return view('klanten.edit', compact('klant'));
    }

    public function update(Request $request, Klant $klant)
    {
        $data = $request->validate([
            'soort'      => 'required|in:bedrijf,particulier',
            'naam'       => 'nullable|string|max:255',
            'straat'     => 'nullable|string|max:255',
            'huisnummer' => 'nullable|string|max:20',
            'postcode'   => 'nullable|string|max:10',
            'stad'       => 'nullable|string|max:255',
            'notities'   => 'nullable|string',
            'bron'       => 'required|in:website,telefoon,email,doorverwijzing,anders',
        ]);

        $klant->update($data);
        return redirect()->route('klanten.show', $klant)->with('success', 'Klant bijgewerkt.');
    }

    public function destroy(Klant $klant)
    {
        $klant->delete();
        return redirect()->route('klanten.index')->with('success', 'Klant verwijderd.');
    }
}
