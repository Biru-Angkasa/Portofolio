<div data-row data-index="{{ $index }}" class="flex flex-col gap-5 rounded-xl border border-line bg-wash p-5">
    <div class="flex flex-wrap items-center justify-between gap-3"><p class="text-sm font-semibold">{{ $collection['label'] }}</p><div class="flex gap-3 text-xs"><button type="button" data-move-row="up" aria-label="Pindahkan item ke atas" class="text-accent">Naik ↑</button><button type="button" data-move-row="down" aria-label="Pindahkan item ke bawah" class="text-accent">Turun ↓</button><button type="button" data-remove-row class="text-red-700">Hapus item</button></div></div>
    <div class="grid gap-5 sm:grid-cols-2">
        @foreach ($collection['fields'] as $fieldKey => $field)
            @php
                $fieldValue = $row[$fieldKey] ?? $field['default'] ?? '';
                if ($collectionKey === 'socials' && $fieldKey === 'url') {
                    $selectedPlatform = old("content.{$collectionKey}.{$index}.platform", $row['platform'] ?? 'auto');
                    $isWhatsApp = $selectedPlatform === 'whatsapp';
                    $field = [...$field,
                        'type' => $isWhatsApp ? 'tel' : ($selectedPlatform === 'auto' ? 'text' : 'url'),
                        'label' => $isWhatsApp ? 'Nomor WhatsApp' : 'Tautan akun / nomor WhatsApp',
                        'placeholder' => $isWhatsApp ? '081234567890 atau +6281234567890' : 'https://…',
                        'social_contact' => true,
                        'help' => $isWhatsApp ? 'Cukup isi nomor. Awalan 08 otomatis diubah menjadi +62.' : 'Isi tautan profil lengkap. Pilih WhatsApp untuk memasukkan nomor telepon.',
                    ];
                    $fieldValue = old("content.{$collectionKey}.{$index}.{$fieldKey}", $fieldValue);
                    if ($isWhatsApp && is_string($fieldValue) && preg_match('~^https?://wa\.me/([0-9]+)$~', $fieldValue, $numberMatch)) {
                        $fieldValue = $numberMatch[1];
                    }
                }
            @endphp
            <div @class(['hidden' => $field['type'] === 'hidden', 'sm:col-span-2' => in_array($field['type'], ['textarea', 'lines'], true)])>
                @include('admin.content.field', ['inputName' => "content[{$collectionKey}][{$index}][{$fieldKey}]", 'errorKey' => "content.{$collectionKey}.{$index}.{$fieldKey}", 'value' => $fieldValue, 'field' => [...$field, 'required' => $field['required'] ?? true], 'useOldValue' => !($field['social_contact'] ?? false)])
            </div>
        @endforeach
    </div>
    @if ($collectionKey === 'education')
        <div class="flex items-center gap-4">
            @if (is_string($row['logo_path'] ?? null) && in_array($row['logo_path'], array_column($portfolio['education'], 'logo_path'), true))
                <img data-logo-preview src="{{ url('/'.ltrim($row['logo_path'], '/')) }}" alt="Logo {{ $row['institution'] ?? '' }}" class="size-16 rounded-xl border border-line bg-paper p-2 object-contain">
            @endif
            <button type="button" data-clear-logo class="text-xs text-red-700">Hapus logo</button>
        </div>
    @endif
</div>
