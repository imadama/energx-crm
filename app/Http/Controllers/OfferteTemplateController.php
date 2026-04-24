<?php

namespace App\Http\Controllers;

use App\Models\OfferteTemplate;
use App\Models\Product;
use App\Services\DocumentRenderer;
use App\Services\TokenResolver;
use Illuminate\Http\Request;

class OfferteTemplateController extends Controller
{
    public function index()
    {
        $templates = OfferteTemplate::latest()->get();
        return view('offerte-templates.index', compact('templates'));
    }

    public function create()
    {
        return view('offerte-templates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'naam'      => 'required|string|max:255',
            'categorie' => 'nullable|string|max:255',
        ]);

        $template = OfferteTemplate::create([
            'naam'        => $request->naam,
            'beschrijving'=> $request->beschrijving,
            'categorie'   => $request->categorie,
            'document'    => DocumentRenderer::leegDocument(),
        ]);

        return redirect()->route('offerte-templates.editor', $template)->with('success', 'Template aangemaakt.');
    }

    public function editor(OfferteTemplate $offerteTemplate)
    {
        $producten = Product::actief()->orderBy('categorie')->orderBy('naam')->get();
        $tokens    = TokenResolver::beschikbareTokens();

        if (!$offerteTemplate->document) {
            $offerteTemplate->update(['document' => DocumentRenderer::leegDocument()]);
        }

        return view('offerte-templates.editor', compact('offerteTemplate', 'producten', 'tokens'));
    }

    public function updateDocument(Request $request, OfferteTemplate $offerteTemplate)
    {
        $request->validate(['document' => 'required|array']);
        $offerteTemplate->update(['document' => $request->document]);
        return response()->json(['ok' => true]);
    }

    public function update(Request $request, OfferteTemplate $offerteTemplate)
    {
        $offerteTemplate->update([
            'naam'        => $request->naam,
            'beschrijving'=> $request->beschrijving,
            'categorie'   => $request->categorie,
        ]);

        return redirect()->route('offerte-templates.index')->with('success', 'Template bijgewerkt.');
    }

    public function destroy(OfferteTemplate $offerteTemplate)
    {
        $offerteTemplate->delete();
        return redirect()->route('offerte-templates.index')->with('success', 'Template verwijderd.');
    }
}
