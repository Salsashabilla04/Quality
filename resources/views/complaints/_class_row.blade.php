{{-- 1 baris = 1 item: [Ketidaksesuaian + Detail] + [Penyebab + Detail] --}}
@php
    $ketOpts = $ketOptions ?? [];
    $penOpts = $penOptions ?? [];
    $ketVal  = $ketVal ?? '';
    $penVal  = $penVal ?? '';
    $ketDet  = $ketDet ?? '';
    $penDet  = $penDet ?? '';
    if (! empty($ketVal) && ! in_array($ketVal, $ketOpts, true)) $ketOpts[] = $ketVal;
    if (! empty($penVal) && ! in_array($penVal, $penOpts, true)) $penOpts[] = $penVal;
@endphp
<div class="item-row grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-slate-50 rounded-lg border border-slate-200 relative">
    {{-- Kolom Ketidaksesuaian --}}
    <div class="space-y-2">
        <div class="text-[11px] font-semibold text-indigo-600 uppercase tracking-wide">Ketidaksesuaian</div>
        <select name="complaint_items[{{ $i }}][ket_val]" class="cr-select cr-ket-select w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-sky-500 focus:border-sky-500">
            <option value="">— pilih —</option>
            @foreach ($ketOpts as $o)
                <option value="{{ $o }}" @selected($ketVal === $o)>{{ $o }}</option>
            @endforeach
            <option value="__new__">➕ Tambah baru…</option>
        </select>
        <input type="text" name="complaint_items[{{ $i }}][ket_new]" autocomplete="off"
               class="cr-new cr-ket-new hidden w-full rounded-lg border border-sky-300 bg-sky-50 px-3 py-2 text-sm focus:ring-sky-500 focus:border-sky-500"
               placeholder="Ketik jenis baru…">
        <input type="text" name="complaint_items[{{ $i }}][ket_det]" value="{{ $ketDet }}"
               class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-600 focus:ring-sky-500 focus:border-sky-500"
               placeholder="Detail ketidaksesuaian (mis. L(-))">
    </div>

    {{-- Kolom Penyebab --}}
    <div class="space-y-2">
        <div class="text-[11px] font-semibold text-amber-600 uppercase tracking-wide">Penyebab</div>
        <select name="complaint_items[{{ $i }}][pen_val]" class="cr-select cr-pen-select w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-sky-500 focus:border-sky-500">
            <option value="">— pilih —</option>
            @foreach ($penOpts as $o)
                <option value="{{ $o }}" @selected($penVal === $o)>{{ $o }}</option>
            @endforeach
            <option value="__new__">➕ Tambah baru…</option>
        </select>
        <input type="text" name="complaint_items[{{ $i }}][pen_new]" autocomplete="off"
               class="cr-new cr-pen-new hidden w-full rounded-lg border border-amber-300 bg-amber-50 px-3 py-2 text-sm focus:ring-sky-500 focus:border-sky-500"
               placeholder="Ketik penyebab baru…">
        <input type="text" name="complaint_items[{{ $i }}][pen_det]" value="{{ $penDet }}"
               class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-600 focus:ring-sky-500 focus:border-sky-500"
               placeholder="Detail penyebab (mis. Nilon aus)">
    </div>

    {{-- Tombol hapus baris --}}
    <button type="button" class="item-remove absolute top-2 right-2 text-slate-300 hover:text-rose-500 text-lg leading-none" title="Hapus baris">&times;</button>
</div>
