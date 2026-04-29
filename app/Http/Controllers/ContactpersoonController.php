<?php

namespace App\Http\Controllers;

use App\Models\Contactpersoon;
use App\Models\Klant;
use Illuminate\Http\Request;

class ContactpersoonController extends Controller
{
    public function create(Klant $klant)
    {
        return view('contactpersonen.create', compact('klant'));
    }

    public function store(Request $request, Klant $klant)
    {
        $data = $request->validate([
            'voornaam'   => 'required|string|max:255',
            'achternaam' => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            'telefoon'   => 'nullable|string|max:20',
        ]);

        $klant->contactpersonen()->create($data);

        return redirect()->route('klanten.show', $klant)->with('success', 'Contactpersoon toegevoegd.');
    }

    public function edit(Contactpersoon $contactpersoon)
    {
        return view('contactpersonen.edit', compact('contactpersoon'));
    }

    public function update(Request $request, Contactpersoon $contactpersoon)
    {
        $data = $request->validate([
            'voornaam'   => 'required|string|max:255',
            'achternaam' => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            'telefoon'   => 'nullable|string|max:20',
        ]);

        $contactpersoon->update($data);

        return redirect()->route('klanten.show', $contactpersoon->klant_id)->with('success', 'Contactpersoon bijgewerkt.');
    }

    public function destroy(Contactpersoon $contactpersoon)
    {
        $klant = $contactpersoon->klant;

        if ($klant->contactpersonen()->count() <= 1) {
            return back()->with('error', 'Een klant moet minimaal één contactpersoon hebben.');
        }

        $contactpersoon->delete();

        return redirect()->route('klanten.show', $klant)->with('success', 'Contactpersoon verwijderd.');
    }
}
