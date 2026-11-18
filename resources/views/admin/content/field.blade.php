@php
    $inputValue = ($useOldValue ?? true) ? old($errorKey, $value ?? $field['default'] ?? '') : ($value ?? '');
    if (is_array($inputValue)) {
        $inputValue = implode("\n", $inputValue);
    }
@endphp
@if ($field['type'] === 'hidden')
    <input type="hidden" name="{{ $inputName }}" value="{{ $inputValue }}">
@else
<label class="flex min-w-0 flex-col gap-2 text-sm" @if ($field['social_contact'] ?? false) data-social-contact @endif>
    <span class="font-medium" @if ($field['social_contact'] ?? false) data-social-contact-label @endif>{{ $field['label'] }}</span>
    @if ($field['type'] === 'image')
        <input type="file" name="{{ $inputName }}" accept="image/jpeg,image/png,image/webp" class="editor-input">
    @elseif (in_array($field['type'], ['textarea', 'lines'], true))
        <textarea name="{{ $inputName }}" rows="{{ $field['type'] === 'lines' ? 5 : 4 }}" maxlength="{{ $field['max'] }}" @required($field['required'] ?? false) class="editor-input">{{ $inputValue }}</textarea>
    @elseif ($field['type'] === 'select')
        <select name="{{ $inputName }}" @required($field['required'] ?? false) class="editor-input">
            @foreach ($field['options'] as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" @selected($inputValue === $optionValue)>{{ $optionLabel }}</option>
            @endforeach
        </select>
    @else
        <input type="{{ $field['type'] }}" name="{{ $inputName }}" value="{{ $inputValue }}" maxlength="{{ $field['max'] }}" @isset($field['placeholder']) placeholder="{{ $field['placeholder'] }}" @endisset @if ($field['type'] === 'tel') inputmode="tel" @endif @required($field['required'] ?? false) class="editor-input">
    @endif
    @isset($field['help'])<span class="text-xs leading-relaxed text-soft" data-social-contact-help>{{ $field['help'] }}</span>@endisset
    @error($errorKey)<span class="text-xs text-red-700">{{ $message }}</span>@enderror
</label>
@endif
