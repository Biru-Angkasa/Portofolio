<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProfilePhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'alt' => ['required', 'string', 'max:160'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'photo.required' => 'Pilih gambar terlebih dahulu.',
            'photo.image' => 'Unggah gambar JPEG, PNG, atau WebP.',
            'photo.mimes' => 'Unggah gambar JPEG, PNG, atau WebP.',
            'photo.max' => 'Ukuran gambar maksimal 5 MB.',
            'alt.required' => 'Teks alternatif wajib diisi.',
        ];
    }
}
