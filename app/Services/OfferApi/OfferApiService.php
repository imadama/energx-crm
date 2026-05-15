<?php

namespace App\Services\OfferApi;

use App\Models\ApiField;
use App\Models\ApiSubmission;
use App\Models\Contactpersoon;
use App\Models\Klant;
use App\Models\Offerte;
use App\Models\OfferteTemplate;
use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OfferApiService
{
    /**
     * @param array<string,mixed> $payload
     */
    public function createOfferFromApi(array $payload): OfferApiResult
    {
        $customer = (array)($payload['customer'] ?? []);
        $details  = (array)($payload['details'] ?? []);
        $templateIdentifier = (string)($payload['offerTemplateId'] ?? '');
        $communicationPreference = $payload['communicationPreference'] ?? null;

        $warnings = [];

        $normalizedDetails = $this->validateAndNormalizeDetails($details);

        /** @var OfferApiResult $result */
        $result = DB::transaction(function () use ($payload, $customer, $templateIdentifier, $communicationPreference, $normalizedDetails, &$warnings) {
            $submission = ApiSubmission::create([
                'template_identifier' => $templateIdentifier,
                'communication_preference' => is_string($communicationPreference) ? $communicationPreference : null,
                'customer_email' => is_string($customer['email'] ?? null) ? $customer['email'] : null,
                'payload' => $payload,
                'details' => $normalizedDetails,
            ]);

            $klant = $this->findOrCreateKlant($customer);

            $template = OfferteTemplate::query()
                ->where('identifier', $templateIdentifier)
                ->with('secties', 'regels')
                ->first();

            if (!$template) {
                throw ValidationException::withMessages([
                    'offerTemplateId' => ['Onbekende offerte template identifier.'],
                ]);
            }

            $offerte = Offerte::create([
                'klant_id' => $klant->id,
                'template_id' => $template->id,
                'status' => 'concept',
            ]);

            foreach ($template->secties as $sectie) {
                $offerte->secties()->create([
                    'type' => $sectie->type,
                    'titel' => $sectie->titel,
                    'inhoud' => $sectie->inhoud ?? [],
                    'volgorde' => $sectie->volgorde,
                ]);
            }

            $this->generateRegelsFromProducts($offerte, $normalizedDetails, $warnings);

            $submission->update(['offerte_id' => $offerte->id]);

            return new OfferApiResult($offerte->fresh(['klant']), $warnings);
        });

        return $result;
    }

    /**
     * @param array<string,mixed> $details
     * @return array<string,mixed>
     */
    private function validateAndNormalizeDetails(array $details): array
    {
        $fields = ApiField::query()->get()->keyBy('key');

        $unknown = array_diff(array_keys($details), $fields->keys()->all());
        if (!empty($unknown)) {
            throw ValidationException::withMessages([
                'details' => ['Onbekende detailvelden: ' . implode(', ', $unknown)],
            ]);
        }

        $normalized = [];

        foreach ($details as $key => $value) {
            $field = $fields->get($key);
            if (!$field) {
                continue;
            }

            if ($value === null) {
                $normalized[$key] = null;
                continue;
            }

            switch ($field->type) {
                case 'text':
                    if (!is_string($value) && !is_numeric($value) && !is_bool($value)) {
                        throw ValidationException::withMessages(['details.' . $key => ['Moet tekst zijn.']]);
                    }
                    $normalized[$key] = (string)$value;
                    break;
                case 'integer':
                    if (is_string($value) && preg_match('/^-?\d+$/', trim($value))) {
                        $normalized[$key] = (int)trim($value);
                        break;
                    }
                    if (is_int($value)) {
                        $normalized[$key] = $value;
                        break;
                    }
                    throw ValidationException::withMessages(['details.' . $key => ['Moet een geheel getal zijn.']]);
                case 'decimal':
                    if (is_numeric($value)) {
                        $normalized[$key] = (float)$value;
                        break;
                    }
                    throw ValidationException::withMessages(['details.' . $key => ['Moet een getal zijn.']]);
                case 'list':
                    $allowed = $field->allowed_values ?? [];
                    $v = is_string($value) || is_numeric($value) ? (string)$value : null;
                    if ($v === null || !in_array($v, $allowed, true)) {
                        throw ValidationException::withMessages(['details.' . $key => ['Ongeldige waarde.']]);
                    }
                    $normalized[$key] = $v;
                    break;
                case 'list_multiple':
                    $allowed = $field->allowed_values ?? [];
                    $values = is_array($value) ? $value : [$value];
                    foreach ($values as $v) {
                        $v = is_string($v) || is_numeric($v) ? (string)$v : null;
                        if ($v === null || !in_array($v, $allowed, true)) {
                            throw ValidationException::withMessages(['details.' . $key => ['Ongeldige waarde: ' . $v]]);
                        }
                    }
                    $normalized[$key] = array_values(array_map('strval', $values));
                    break;
            }
        }

        return $normalized;
    }

    /**
     * @param array<string,mixed> $customer
     */
    private function findOrCreateKlant(array $customer): Klant
    {
        $email = trim((string)($customer['email'] ?? ''));
        if ($email === '') {
            throw ValidationException::withMessages(['customer.email' => ['E-mailadres is verplicht.']]);
        }

        // Zoek bestaande klant via contactpersoon e-mail
        $contactpersoon = Contactpersoon::where('email', $email)->first();
        if ($contactpersoon) {
            return $contactpersoon->klant;
        }

        // Naam splitsen in voornaam / achternaam
        $volledigeNaam = trim((string)($customer['name'] ?? ''));
        $spacePos  = strpos($volledigeNaam, ' ');
        $voornaam  = $spacePos !== false ? substr($volledigeNaam, 0, $spacePos) : $volledigeNaam;
        $achternaam = $spacePos !== false ? substr($volledigeNaam, $spacePos + 1) : '';

        $klant = Klant::create([
            'naam'       => $volledigeNaam,
            'straat'     => Arr::get($customer, 'street'),
            'huisnummer' => Arr::get($customer, 'housenumber'),
            'postcode'   => Arr::get($customer, 'postalcode'),
            'stad'       => Arr::get($customer, 'city'),
            'bron'       => 'website',
        ]);

        $klant->contactpersonen()->create([
            'voornaam'   => $voornaam,
            'achternaam' => $achternaam,
            'email'      => $email,
            'telefoon'   => Arr::get($customer, 'phone'),
        ]);

        return $klant;
    }

    /**
     * @param array<string,mixed> $details
     * @param string[] $warnings
     */
    private function generateRegelsFromProducts(Offerte $offerte, array $details, array &$warnings): void
    {
        $producten = Product::query()
            ->where('actief', true)
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        $volgorde = 0;

        foreach ($producten as $product) {
            /** @var Product $product */
            $mode = $product->generator_mode ?? 'manual';
            if ($mode === 'manual') {
                continue;
            }

            $shouldAdd = false;

            if ($mode === 'always') {
                $shouldAdd = true;
            } elseif ($mode === 'conditional') {
                $shouldAdd = $this->matchesConditions($product->generator_conditions ?? [], $details);
            }

            if (!$shouldAdd) {
                continue;
            }

            [$aantal, $prijs] = $this->resolveAantalEnPrijs($product, $details, $warnings);

            $offerte->regels()->create([
                'product_id' => $product->id,
                'naam' => $product->naam,
                'beschrijving' => $product->beschrijving,
                'aantal' => $aantal,
                'eenheidsprijs' => $prijs,
                'volgorde' => $volgorde++,
            ]);
        }
    }

    /**
     * @param array<int,mixed> $conditions
     * @param array<string,mixed> $details
     */
    private function matchesConditions(array $conditions, array $details): bool
    {
        // conditions = OR blocks, each block = AND rules
        if (empty($conditions)) {
            return false;
        }

        foreach ($conditions as $block) {
            $rules = is_array($block) ? ($block['and'] ?? $block) : [];
            if (!is_array($rules) || empty($rules)) {
                continue;
            }

            $blockOk = true;
            foreach ($rules as $rule) {
                if (!$this->matchesRule(is_array($rule) ? $rule : [], $details)) {
                    $blockOk = false;
                    break;
                }
            }
            if ($blockOk) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<string,mixed> $rule
     * @param array<string,mixed> $details
     */
    private function matchesRule(array $rule, array $details): bool
    {
        $field = (string)($rule['field'] ?? '');
        $op = (string)($rule['op'] ?? '');

        $exists = array_key_exists($field, $details) && $details[$field] !== null;

        if ($op === 'present') {
            return $exists;
        }

        $value = $details[$field] ?? null;
        $cmp = $rule['value'] ?? null;

        if ($op === 'eq') return $exists && (string)$value === (string)$cmp;
        if ($op === 'neq') return $exists && (string)$value !== (string)$cmp;

        if ($op === 'gt') return $exists && is_numeric($value) && is_numeric($cmp) && (float)$value > (float)$cmp;
        if ($op === 'lt') return $exists && is_numeric($value) && is_numeric($cmp) && (float)$value < (float)$cmp;

        return false;
    }

    /**
     * @param string[] $warnings
     * @return array{0:int,1:float}
     */
    private function resolveAantalEnPrijs(Product $product, array $details, array &$warnings): array
    {
        $aantal = 1;
        $prijs = (float)$product->prijs;

        $rules = $product->generator_value_rules ?? [];
        if (!is_array($rules)) {
            return [$aantal, $prijs];
        }

        foreach (['aantal', 'prijs'] as $target) {
            $enabled = (bool)($rules[$target]['enabled'] ?? false);
            if (!$enabled) continue;

            $field = (string)($rules[$target]['field'] ?? '');
            $op = (string)($rules[$target]['op'] ?? '');
            $delta = $rules[$target]['delta'] ?? null;

            $base = $details[$field] ?? null;
            if (!is_numeric($base)) {
                $warnings[] = "Waarde voor '{$field}' ontbreekt of is niet numeriek (voor {$target}).";
                continue;
            }
            $num = (float)$base;
            if ($op === '+') $num += (float)($delta ?? 0);
            if ($op === '-') $num -= (float)($delta ?? 0);

            if ($target === 'aantal') {
                $aantal = max(1, (int)round($num));
            } else {
                $prijs = max(0.0, round($num, 2));
            }
        }

        return [$aantal, $prijs];
    }
}

