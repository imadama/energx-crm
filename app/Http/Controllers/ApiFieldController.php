<?php

namespace App\Http\Controllers;

use App\Models\ApiField;
use App\Services\Admin\ApiFieldService;
use Illuminate\Http\Request;

class ApiFieldController extends Controller
{
    public function index(ApiFieldService $service)
    {
        $fields = $service->list();
        return view('api-fields.index', compact('fields'));
    }

    public function create()
    {
        return view('api-fields.create');
    }

    public function store(Request $request, ApiFieldService $service)
    {
        $data = $request->validate([
            'key' => 'required|string|max:255|regex:/^[a-zA-Z0-9_]+$/|unique:api_fields,key',
            'label' => 'nullable|string|max:255',
            'type' => 'required|in:text,integer,decimal,list',
            'allowed_values_text' => 'nullable|string',
        ], [
            'key.regex' => 'Veldnaam mag alleen letters, cijfers en underscores bevatten.',
        ]);

        $allowed = null;
        if ($data['type'] === 'list') {
            $allowed = collect(preg_split('/\r\n|\r|\n/', (string)($data['allowed_values_text'] ?? '')))
                ->map(fn ($v) => trim((string)$v))
                ->filter()
                ->values()
                ->all();
        }

        $service->create([
            'key' => $data['key'],
            'label' => $data['label'] ?: null,
            'type' => $data['type'],
            'allowed_values' => $allowed,
        ]);

        return redirect()->route('api-fields.index')->with('success', 'API veld aangemaakt.');
    }

    public function edit(ApiField $apiField)
    {
        return view('api-fields.edit', compact('apiField'));
    }

    public function update(Request $request, ApiField $apiField, ApiFieldService $service)
    {
        $data = $request->validate([
            'key' => 'required|string|max:255|regex:/^[a-zA-Z0-9_]+$/|unique:api_fields,key,' . $apiField->id,
            'label' => 'nullable|string|max:255',
            'type' => 'required|in:text,integer,decimal,list',
            'allowed_values_text' => 'nullable|string',
        ], [
            'key.regex' => 'Veldnaam mag alleen letters, cijfers en underscores bevatten.',
        ]);

        $allowed = null;
        if ($data['type'] === 'list') {
            $allowed = collect(preg_split('/\r\n|\r|\n/', (string)($data['allowed_values_text'] ?? '')))
                ->map(fn ($v) => trim((string)$v))
                ->filter()
                ->values()
                ->all();
        }

        $service->update($apiField, [
            'key' => $data['key'],
            'label' => $data['label'] ?: null,
            'type' => $data['type'],
            'allowed_values' => $allowed,
        ]);

        return redirect()->route('api-fields.index')->with('success', 'API veld bijgewerkt.');
    }

    public function destroy(ApiField $apiField, ApiFieldService $service)
    {
        $service->delete($apiField);
        return redirect()->route('api-fields.index')->with('success', 'API veld verwijderd.');
    }
}

