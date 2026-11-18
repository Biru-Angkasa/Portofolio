<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCvRequest extends FormRequest
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
            'cv' => ['required', 'file', 'mimes:pdf', 'max:5120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'cv.required' => 'Pilih berkas PDF CV terlebih dahulu.',
            'cv.file' => 'Berkas CV tidak valid.',
            'cv.mimes' => 'Berkas CV harus berformat PDF.',
            'cv.max' => 'Ukuran berkas CV maksimal 5 MB.',
        ];
    }
}
