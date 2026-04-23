<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $producten = Product::latest()->paginate(20);
        return view('producten.index', compact('producten'));
    }

    public function create()
    {
        return view('producten.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'naam'        => 'required|string|max:255',
            'beschrijving'=> 'nullable|string',
            'prijs'       => 'required|numeric|min:0',
            'categorie'   => 'required|in:laadpaal,installatie,thuisbatterij,warmtepomp,accessoire,overig',
            'merk'        => 'nullable|string|max:255',
            'actief'      => 'boolean',
        ]);

        $data['actief'] = $request->boolean('actief', true);
        Product::create($data);

        return redirect()->route('producten.index')->with('success', 'Product aangemaakt.');
    }

    public function edit(Product $product)
    {
        return view('producten.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'naam'        => 'required|string|max:255',
            'beschrijving'=> 'nullable|string',
            'prijs'       => 'required|numeric|min:0',
            'categorie'   => 'required|in:laadpaal,installatie,thuisbatterij,warmtepomp,accessoire,overig',
            'merk'        => 'nullable|string|max:255',
            'actief'      => 'boolean',
        ]);

        $data['actief'] = $request->boolean('actief', true);
        $product->update($data);

        return redirect()->route('producten.index')->with('success', 'Product bijgewerkt.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('producten.index')->with('success', 'Product verwijderd.');
    }

    public function show(Product $product)
    {
        return redirect()->route('producten.edit', $product);
    }
}
