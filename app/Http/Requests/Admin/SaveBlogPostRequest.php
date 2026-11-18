<?php

namespace App\Http\Requests\Admin;

use App\WorkStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveBlogPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:160'],
            'category' => ['required', 'string', 'max:80'],
            'excerpt' => ['required', 'string', 'max:2000'],
            'body' => ['required', 'string', 'max:100000'],
            'status' => ['required', Rule::enum(WorkStatus::class)],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_cover' => ['sometimes', 'boolean'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul artikel wajib diisi.',
            'body.required' => 'Isi artikel wajib diisi.',
            'cover.image' => 'Sampul harus berupa gambar JPEG, PNG, atau WebP.',
        ];
    }
}
