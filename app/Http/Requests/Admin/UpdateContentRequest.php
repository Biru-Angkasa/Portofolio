<?php

namespace App\Http\Requests\Admin;

use App\Models\SiteProfile;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function sectionDefinition(): array
    {
        $definition = config('portfolio-editor.'.$this->route('section'));
        abort_unless(is_array($definition), 404);

        return $definition;
    }

    protected function prepareForValidation(): void
    {
        $content = $this->input('content', []);

        if (is_array($content)) {
            foreach ($this->sectionDefinition()['collections'] as $key => $collection) {
                $content[$key] ??= [];
            }

            if ($this->route('section') === 'kontak' && is_array($content['socials'] ?? null)) {
                foreach ($content['socials'] as &$social) {
                    if (! is_array($social) || ! is_string($social['url'] ?? null)) {
                        continue;
                    }

                    if (in_array($social['platform'] ?? 'auto', ['auto', 'whatsapp'], true)) {
                        $number = $this->whatsAppNumber($social['url']);

                        if ($number !== null) {
                            $social['platform'] = 'whatsapp';
                            $social['url'] = 'https://wa.me/'.$number;
                        }
                    }
                }
                unset($social);
            }

            $this->merge(['content' => $content]);
        }
    }

    private function whatsAppNumber(string $value): ?string
    {
        $value = trim($value);

        if (! preg_match('/^\+?[0-9\s().-]+$/', $value)) {
            return null;
        }

        $number = preg_replace('/[\s().-]/', '', ltrim($value, '+'));

        if (str_starts_with($number, '08')) {
            $number = '62'.substr($number, 1);
        }

        return preg_match('/^[1-9][0-9]{7,14}$/', $number) ? $number : null;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        $definition = $this->sectionDefinition();
        $keys = [...array_keys($definition['fields']), ...array_keys($definition['collections'])];
        $rules = ['content' => ['required', 'array:'.implode(',', $keys)]];

        foreach ($definition['fields'] as $key => $field) {
            $rules['content.'.$key] = $this->fieldRules($field);
        }

        foreach ($definition['collections'] as $key => $collection) {
            $rules['content.'.$key] = ['present', 'array', 'max:30'];
            $rules['content.'.$key.'.*'] = ['array:'.implode(',', array_keys($collection['fields']))];

            foreach ($collection['fields'] as $fieldKey => $field) {
                $rules['content.'.$key.'.*.'.$fieldKey] = $this->fieldRules([...$field, 'required' => $field['required'] ?? true]);
            }
        }

        if ($this->route('section') === 'pendidikan') {
            $rules['content.education.*.logo_path'][] = Rule::in(array_column(SiteProfile::current()->portfolio()['education'], 'logo_path'));
        }

        return $rules;
    }

    /** @param array<string, mixed> $field
     * @return array<int, string>
     */
    private function fieldRules(array $field): array
    {
        if ($field['type'] === 'image') {
            return ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:'.$field['max']];
        }

        $rules = [
            ($field['required'] ?? false) ? 'required' : 'nullable',
            match ($field['type']) {
                'email' => 'email',
                'url' => 'url:http,https',
                default => 'string',
            },
            'max:'.$field['max'],
        ];

        if ($field['type'] === 'select') {
            $rules[] = 'in:'.implode(',', array_keys($field['options']));
        }

        return $rules;
    }

    /** @return array<string, mixed> */
    public function contentAttributes(): array
    {
        $content = $this->validated('content');

        foreach ($this->sectionDefinition()['fields'] as $key => $field) {
            if (array_key_exists($key, $content)) {
                $content[$key] ??= '';
            }
        }

        foreach ($this->sectionDefinition()['collections'] as $key => $collection) {
            $content[$key] = array_values($content[$key]);

            foreach ($content[$key] as &$row) {
                foreach ($collection['fields'] as $fieldKey => $field) {
                    if ($field['type'] === 'lines') {
                        $row[$fieldKey] = array_values(array_filter(array_map('trim', preg_split('/\R/u', $row[$fieldKey])), fn (string $line): bool => $line !== ''));
                    }
                }
            }
            unset($row);
        }

        return $content;
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        $messages = [];

        $socials = $this->input('content.socials', []);

        foreach (is_array($socials) ? $socials : [] as $index => $social) {
            if (is_array($social) && ($social['platform'] ?? null) === 'whatsapp') {
                $messages['content.socials.'.$index.'.url.url'] = 'Masukkan nomor WhatsApp yang valid, misalnya 081234567890 atau +6281234567890.';
            }
        }

        return [
            ...$messages,
            'content.array' => 'Ada kolom yang tidak sesuai dengan menu ini.',
            'content.*.required' => 'Kolom ini wajib diisi.',
            'content.*.*.*.required' => 'Lengkapi semua kolom pada item, atau hapus item yang kosong.',
            'content.socials.*.url.url' => 'Gunakan tautan lengkap dengan http:// atau https://.',
            'content.education.*.url.url' => 'Gunakan link tujuan lengkap dengan http:// atau https://.',
            'content.education.*.logo.image' => 'Logo harus berupa gambar JPEG, PNG, atau WebP.',
            'content.education.*.logo.mimes' => 'Logo harus berupa gambar JPEG, PNG, atau WebP.',
            'content.education.*.logo.max' => 'Ukuran logo maksimal 2 MB.',
            'content.education.*.logo_path.in' => 'Logo tersimpan tidak valid. Unggah logo melalui kolom yang tersedia.',
        ];
    }
}
