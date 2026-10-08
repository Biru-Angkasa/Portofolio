@csrf
@php($item = $work ?? null)
<label class="flex flex-col gap-2 text-sm">
    Judul
    <input type="text" name="title" value="{{ old('title', $item?->title ?? '') }}" required maxlength="160" class="border border-line bg-paper px-3 py-2 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent">
</label>
<label class="flex flex-col gap-2 text-sm">
    Kategori
    <input type="text" name="category" value="{{ old('category', $item?->category ?? '') }}" required maxlength="80" class="border border-line bg-paper px-3 py-2 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent">
</label>
<label class="flex flex-col gap-2 text-sm">
    Ringkasan
    <textarea name="summary" required maxlength="2000" rows="4" class="border border-line bg-paper px-3 py-2 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent">{{ old('summary', $item?->summary ?? '') }}</textarea>
</label>
<label class="flex flex-col gap-2 text-sm">
    Peran atau tindakan
    <input type="text" name="role" value="{{ old('role', $item?->role ?? '') }}" required maxlength="500" class="border border-line bg-paper px-3 py-2 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent">
</label>
<label class="flex flex-col gap-2 text-sm">
    Periode, jika ada
    <input type="text" name="period" value="{{ old('period', $item?->period ?? '') }}" maxlength="80" class="border border-line bg-paper px-3 py-2 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent">
</label>
<label class="flex flex-col gap-2 text-sm">
    Caption
    <textarea name="caption" maxlength="500" rows="3" class="border border-line bg-paper px-3 py-2 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent">{{ old('caption', $item?->caption ?? '') }}</textarea>
</label>
<label class="flex flex-col gap-2 text-sm">
    Status
    <select name="status" class="border border-line bg-paper px-3 py-2 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent">
        <option value="draft" @selected(old('status', $item?->status?->value ?? 'draft') === 'draft')>Draf</option>
        <option value="published" @selected(old('status', $item?->status?->value ?? 'draft') === 'published')>Terbit</option>
    </select>
</label>
@if ($errors->any())
    <ul class="flex flex-col gap-1 text-sm text-accent">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif
<button type="submit" class="w-fit bg-accent px-4 py-2 text-sm font-semibold text-paper focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">Simpan</button>
