<?php

namespace App\Http\Requests\Admin;

use App\MediaKind;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWorkMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('caption') === '') {
            $this->merge(['caption' => null]);
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $kind = $this->string('kind')->toString();

        return [
            'kind' => ['required', Rule::enum(MediaKind::class)],
            'label' => ['required', 'string', 'max:160'],
            'caption' => ['nullable', 'string', 'max:500'],
            'file' => array_values(array_filter([
                Rule::excludeIf($kind === MediaKind::Link->value),
                Rule::requiredIf($kind !== MediaKind::Link->value),
                'file',
                $kind === MediaKind::Image->value ? 'image' : null,
                $kind === MediaKind::Image->value ? 'mimes:jpg,jpeg,png,webp' : null,
                $kind === MediaKind::Image->value ? 'max:5120' : null,
                $kind === MediaKind::Pdf->value ? 'mimes:pdf' : null,
                $kind === MediaKind::Pdf->value ? 'mimetypes:application/pdf' : null,
                $kind === MediaKind::Pdf->value ? 'max:10240' : null,
            ])),
            'url' => [
                Rule::excludeIf($kind !== MediaKind::Link->value),
                Rule::requiredIf($kind === MediaKind::Link->value),
                'url:https',
                'max:2000',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'file.image' => 'Gambar harus JPEG, PNG, atau WebP.',
            'file.mimes' => 'Jenis berkas tidak diizinkan.',
            'file.mimetypes' => 'Jenis berkas tidak diizinkan.',
            'file.max' => 'Berkas terlalu besar.',
            'url.url' => 'Tautan harus berupa URL HTTPS.',
            'url.required' => 'URL HTTPS wajib diisi.',
            'label.required' => 'Label atau teks alternatif wajib diisi.',
        ];
    }
}
