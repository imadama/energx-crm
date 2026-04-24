<?php

namespace Tests\Feature;

use App\Models\ApiField;
use App\Models\Klant;
use App\Models\Offerte;
use App\Models\OfferteTemplate;
use App\Models\OfferteTemplateSectie;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfferApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_offer_for_existing_customer_and_adds_matching_products(): void
    {
        ApiField::create(['key' => 'meters_cable', 'label' => 'Kabel (m)', 'type' => 'integer']);
        ApiField::create(['key' => 'situation', 'label' => 'Situatie', 'type' => 'list', 'allowed_values' => ['garage', 'oprit']]);

        $template = OfferteTemplate::create([
            'naam' => 'Test template',
            'identifier' => 'test_template',
        ]);
        OfferteTemplateSectie::create([
            'template_id' => $template->id,
            'type' => 'voorblad',
            'titel' => 'Voorblad',
            'inhoud' => [],
            'volgorde' => 0,
        ]);

        $always = Product::create([
            'naam' => 'Altijd product',
            'prijs' => 100,
            'categorie' => 'installatie',
            'actief' => true,
            'order' => 1,
            'generator_mode' => 'always',
        ]);

        $conditional = Product::create([
            'naam' => 'Kabel product',
            'prijs' => 10,
            'categorie' => 'accessoire',
            'actief' => true,
            'order' => 2,
            'generator_mode' => 'conditional',
            'generator_conditions' => [
                ['and' => [
                    ['field' => 'meters_cable', 'op' => 'gt', 'value' => 5],
                    ['field' => 'situation', 'op' => 'eq', 'value' => 'garage'],
                ]],
            ],
            'generator_value_rules' => [
                'aantal' => ['enabled' => true, 'field' => 'meters_cable', 'op' => '', 'delta' => 0],
                'prijs'  => ['enabled' => false, 'field' => null, 'op' => '', 'delta' => 0],
            ],
        ]);

        $klant = Klant::create([
            'naam' => 'Existing Customer',
            'email' => 'existing@example.com',
        ]);

        $res = $this->postJson('/api/v1/offer', [
            'customer' => [
                'name' => 'Existing Customer',
                'email' => 'existing@example.com',
                'phone' => '0612345678',
                'street' => 'Teststraat',
                'housenumber' => '1',
                'postalcode' => '1234AB',
                'city' => 'Utrecht',
                'country' => 'NL',
            ],
            'communicationPreference' => 'email',
            'offerTemplateId' => 'test_template',
            'details' => [
                'meters_cable' => 10,
                'situation' => 'garage',
            ],
        ]);

        $res->assertStatus(201);
        $offerteId = $res->json('offerteId');
        $this->assertNotNull($offerteId);

        $offerte = Offerte::query()->with('regels')->findOrFail($offerteId);
        $this->assertSame($klant->id, $offerte->klant_id);
        $this->assertSame('concept', $offerte->status);

        $this->assertCount(2, $offerte->regels);
        $this->assertSame($always->id, $offerte->regels[0]->product_id);
        $this->assertSame($conditional->id, $offerte->regels[1]->product_id);
        $this->assertSame(10, $offerte->regels[1]->aantal);
    }

    public function test_rejects_unknown_template_identifier(): void
    {
        ApiField::create(['key' => 'meters_cable', 'type' => 'integer']);

        $res = $this->postJson('/api/v1/offer', [
            'customer' => [
                'name' => 'X',
                'email' => 'x@example.com',
                'country' => 'NL',
            ],
            'communicationPreference' => 'email',
            'offerTemplateId' => 'does_not_exist',
            'details' => [
                'meters_cable' => 1,
            ],
        ]);

        $res->assertStatus(422);
        $res->assertJsonPath('errors.offerTemplateId.0', 'Onbekende offerte template identifier.');
    }

    public function test_rejects_unknown_details_keys(): void
    {
        OfferteTemplate::create(['naam' => 'T', 'identifier' => 't']);

        $res = $this->postJson('/api/v1/offer', [
            'customer' => [
                'name' => 'X',
                'email' => 'x@example.com',
                'country' => 'NL',
            ],
            'communicationPreference' => 'email',
            'offerTemplateId' => 't',
            'details' => [
                'unknown_key' => 'x',
            ],
        ]);

        $res->assertStatus(422);
        $this->assertStringContainsString('Onbekende detailvelden', (string)($res->json('errors.details.0') ?? ''));
    }
}

