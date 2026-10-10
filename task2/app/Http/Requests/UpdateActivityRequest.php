<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

/**
 * Aturannya sama dengan create, kecuali kode unik harus mengabaikan record yang sedang diedit
 * (BR-02: update tanpa mengganti kode tidak dianggap duplikat terhadap dirinya sendiri).
 */
class UpdateActivityRequest extends StoreActivityRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['code'] = [
            'required',
            'string',
            'max:30',
            Rule::unique('activities', 'code')->ignore($this->route('activity')),
        ];

        return $rules;
    }
}
