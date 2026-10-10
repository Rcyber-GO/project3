<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Hanya validasi INPUT. Aturan yang bergantung pada state (transisi status,
     * kelengkapan saat publish) ada di ActivityService.
     * Perhatikan: tidak ada field "status" di sini (AC-05: status tidak boleh diubah dari form umum).
     */
    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'code' => ['required', 'string', 'max:30', 'unique:activities,code'],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'location' => ['required', 'string', 'max:150'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after_or_equal:start_at'],
            'capacity' => ['required', 'integer', 'min:1', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak tersedia.',
            'code.unique' => 'Kode kegiatan sudah dipakai.',
            'end_at.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
            'capacity.min' => 'Kapasitas minimal 1 peserta.',
            'capacity.max' => 'Kapasitas maksimal 500 peserta.',
        ];
    }
}
