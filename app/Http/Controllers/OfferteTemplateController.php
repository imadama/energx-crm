<?php

namespace App\Http\Controllers;

use App\Models\OfferteTemplate;
use App\Models\Product;
use Illuminate\Http\Request;

class OfferteTemplateController extends Controller
{
    public function index()
    {
        $templates = OfferteTemplate::withCount('secties')->latest()->get();
        return view('offerte-templates.index', compact('templates'));
    }

    public function create()
    {
        $producten = Product::actief()->orderBy('categorie')->orderBy('naam')->get();
        return view('offerte-templates.create', compact('producten'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'naam'      => 'required|string|max:255',
            'categorie' => 'nullable|string|max:255',
            'secties'   => 'array',
            'regels'    => 'array',
        ]);

        $template = OfferteTemplate::create([
            'naam'        => $request->naam,
            'beschrijving'=> $request->beschrijving,
            'categorie'   => $request->categorie,
        ]);

        foreach (($request->secties ?? []) as $i => $sectie) {
            $inhoud = $this->parseSectieInhoud($sectie);
            $template->secties()->create([
                'type'    => $sectie['type'],
                'titel'   => $sectie['titel'],
                'inhoud'  => $inhoud,
                'volgorde'=> $i,
            ]);
        }

        foreach (($request->regels ?? []) as $i => $regel) {
            $template->regels()->create([
                'product_id'   => $regel['product_id'] ?: null,
                'naam'         => $regel['naam'],
                'beschrijving' => $regel['beschrijving'] ?? null,
                'aantal'       => $regel['aantal'] ?? 1,
                'eenheidsprijs'=> $regel['eenheidsprijs'] ?? 0,
                'volgorde'     => $i,
            ]);
        }

        return redirect()->route('offerte-templates.index')->with('success', 'Template aangemaakt.');
    }

    public function edit(OfferteTemplate $offerteTemplate)
    {
        $offerteTemplate->load('secties', 'regels.product');
        $producten = Product::actief()->orderBy('categorie')->orderBy('naam')->get();
        return view('offerte-templates.edit', compact('offerteTemplate', 'producten'));
    }

    public function update(Request $request, OfferteTemplate $offerteTemplate)
    {
        $offerteTemplate->update([
            'naam'        => $request->naam,
            'beschrijving'=> $request->beschrijving,
            'categorie'   => $request->categorie,
        ]);

        $offerteTemplate->secties()->delete();
        foreach (($request->secties ?? []) as $i => $sectie) {
            $offerteTemplate->secties()->create([
                'type'    => $sectie['type'],
                'titel'   => $sectie['titel'],
                'inhoud'  => $this->parseSectieInhoud($sectie),
                'volgorde'=> $i,
            ]);
        }

        $offerteTemplate->regels()->delete();
        foreach (($request->regels ?? []) as $i => $regel) {
            $offerteTemplate->regels()->create([
                'product_id'   => $regel['product_id'] ?: null,
                'naam'         => $regel['naam'],
                'beschrijving' => $regel['beschrijving'] ?? null,
                'aantal'       => $regel['aantal'] ?? 1,
                'eenheidsprijs'=> $regel['eenheidsprijs'] ?? 0,
                'volgorde'     => $i,
            ]);
        }

        return redirect()->route('offerte-templates.index')->with('success', 'Template bijgewerkt.');
    }

    public function destroy(OfferteTemplate $offerteTemplate)
    {
        $offerteTemplate->delete();
        return redirect()->route('offerte-templates.index')->with('success', 'Template verwijderd.');
    }

    private function parseSectieInhoud(array $sectie): array
    {
        return match($sectie['type']) {
            'introductie' => ['tekst' => $sectie['inhoud']['tekst'] ?? ''],
            'product'     => [
                'beschrijving' => $sectie['inhoud']['beschrijving'] ?? '',
                'specs'        => $sectie['inhoud']['specs'] ?? [],
            ],
            'werkwijze'   => ['stappen' => $sectie['inhoud']['stappen'] ?? []],
            'acceptatie'  => ['tekst' => $sectie['inhoud']['tekst'] ?? 'Ben je akkoord met deze offerte? Klik op de knop hieronder om digitaal te ondertekenen.'],
            'tekst'       => ['tekst' => $sectie['inhoud']['tekst'] ?? ''],
            default       => [],
        };
    }
}
