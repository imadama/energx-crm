<?php

namespace App\Services\Admin;

use App\Models\ApiField;

class ApiFieldService
{
    /**
     * @return \Illuminate\Support\Collection<int,ApiField>
     */
    public function list(): \Illuminate\Support\Collection
    {
        return ApiField::query()->orderBy('key')->get();
    }

    /**
     * @param array<string,mixed> $data
     */
    public function create(array $data): ApiField
    {
        return ApiField::create($data);
    }

    /**
     * @param array<string,mixed> $data
     */
    public function update(ApiField $field, array $data): ApiField
    {
        $field->update($data);
        return $field;
    }

    public function delete(ApiField $field): void
    {
        $field->delete();
    }
}

