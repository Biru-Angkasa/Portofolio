<?php

namespace App\Http\Requests\Admin;

use App\WorkStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWorkItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        foreach (['period', 'caption'] as $field) {
            if ($this->input($field) === '') {
                $this->merge([$field => null]);
            }
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:160'],
            'category' => ['required', 'string', 'max:80'],
            'summary' => ['required', 'string', 'max:2000'],
            'role' => ['required', 'string', 'max:500'],
            'period' => ['nullable', 'string', 'max:80'],
            'caption' => ['nullable', 'string', 'max:500'],
            'status' => ['required', Rule::enum(WorkStatus::class)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul wajib diisi.',
            'category.required' => 'Kategori wajib diisi.',
            'summary.required' => 'Ringkasan wajib diisi.',
            'role.required' => 'Peran atau tindakan wajib diisi.',
            'status.required' => 'Pilih status draf atau terbit.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function itemAttributes(): array
    {
        return $this->safe()->only([
            'title',
            'category',
            'summary',
            'role',
            'period',
            'caption',
            'status',
        ]);
    }
}
