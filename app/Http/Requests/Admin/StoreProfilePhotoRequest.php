<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'photo' => ['nullable', Rule::requiredIf(! $this->boolean('use_existing_photo')), 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120', 'dimensions:max_width=4096,max_height=4096'],
            'alt' => ['required', 'string', 'max:160'],
            'use_existing_photo' => ['nullable', 'boolean'],
            'crop' => [Rule::requiredIf($this->boolean('use_existing_photo')), 'array:x,y,zoom'],
            'crop.x' => ['required_with:crop', 'numeric', 'between:0,1'],
            'crop.y' => ['required_with:crop', 'numeric', 'between:0,1'],
            'crop.zoom' => ['required_with:crop', 'numeric', 'between:1,3'],
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
            'photo.dimensions' => 'Ukuran foto maksimal 4096 × 4096 piksel.',
            'alt.required' => 'Teks alternatif wajib diisi.',
            'crop.required' => 'Atur posisi foto sebelum menyimpan.',
            'crop.*.required_with' => 'Pengaturan crop belum lengkap. Pilih foto dan atur kembali posisinya.',
            'crop.*.numeric' => 'Pengaturan crop tidak valid.',
            'crop.x.between' => 'Posisi foto harus berada di dalam area crop.',
            'crop.y.between' => 'Posisi foto harus berada di dalam area crop.',
            'crop.zoom.between' => 'Zoom foto harus antara 1× dan 3×.',
        ];
    }
}
