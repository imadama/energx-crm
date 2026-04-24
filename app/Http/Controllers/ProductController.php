<?php

namespace App\Http\Controllers;

use App\Models\ApiField;
use App\Models\Product;
use App\Services\Admin\ProductAdminService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $producten = Product::query()->orderBy('order')->orderBy('id')->paginate(50);
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
            'order'       => 'nullable|integer|min:0',
        ]);

        $data['actief'] = $request->boolean('actief', true);
        Product::create($data);

        return redirect()->route('producten.index')->with('success', 'Product aangemaakt.');
    }

    public function edit(Product $product)
    {
        $apiFields = ApiField::query()->orderBy('key')->get(['key', 'label', 'type', 'allowed_values']);
        return view('producten.edit', compact('product', 'apiFields'));
    }

    public function update(Request $request, Product $product, ProductAdminService $service)
    {
        $data = $request->validate([
            'naam'        => 'required|string|max:255',
            'beschrijving'=> 'nullable|string',
            'prijs'       => 'required|numeric|min:0',
            'categorie'   => 'required|in:laadpaal,installatie,thuisbatterij,warmtepomp,accessoire,overig',
            'merk'        => 'nullable|string|max:255',
            'actief'      => 'boolean',
            'order'       => 'nullable|integer|min:0',
            'generator_mode' => 'nullable|in:manual,always,conditional',
            'generator_conditions_json' => 'nullable|string',
            'generator_value_rules_json' => 'nullable|string',
        ]);

        $data['actief'] = $request->boolean('actief', true);
        $update = $data;
        unset($update['generator_conditions_json'], $update['generator_value_rules_json']);

        if (isset($data['generator_conditions_json'])) {
            $update['generator_conditions'] = json_decode($data['generator_conditions_json'] ?: 'null', true);
        }
        if (isset($data['generator_value_rules_json'])) {
            $update['generator_value_rules'] = json_decode($data['generator_value_rules_json'] ?: 'null', true);
        }

        $service->updateProduct($product, $update);

        return redirect()->route('producten.index')->with('success', 'Product bijgewerkt.');
    }

    public function updateOrder(Request $request, ProductAdminService $service)
    {
        $data = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:producten,id',
            'items.*.order' => 'required|integer|min:0',
        ]);

        $service->updateOrderBulk($data['items']);
        return redirect()->route('producten.index')->with('success', 'Volgorde opgeslagen.');
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
