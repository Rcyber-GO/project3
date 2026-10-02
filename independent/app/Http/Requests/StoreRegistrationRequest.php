<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Hanya validasi format input. Rule yang bergantung pada kondisi kegiatan
     * (published, belum mulai, kapasitas, email unik per kegiatan) ada di RegistrationService.
     */
    public function rules(): array
    {
        return [
            'participant_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
        ];
    }
}
