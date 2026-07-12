{{-- Dropdown multi-pilih (checkbox di dalam panel) + tambah baru.
     Param: $name, $options (array), $selected (array), $placeholder, $newPlaceholder --}}
<div class="ms-dropdown relative" data-name="{{ $name }}">
    <button type="button"
            class="ms-toggle w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-left flex items-center justify-between gap-2 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
        <span class="ms-summary truncate {{ count($selected) ? 'text-slate-700' : 'text-slate-400' }}" data-placeholder="{{ $placeholder }}">
            {{ count($selected) ? implode(', ', $selected) : $placeholder }}
        </span>
        <svg class="ms-caret w-4 h-4 text-slate-400 shrink-0 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div class="ms-panel hidden absolute z-30 mt-1 w-full bg-white border border-slate-200 rounded-lg shadow-lg">
        <div class="ms-options max-h-52 overflow-auto p-1.5 space-y-0.5">
            @foreach ($options as $opt)
                <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-slate-50 cursor-pointer text-sm">
                    <input type="checkbox" name="{{ $name }}[]" value="{{ $opt }}"
                           class="ms-cb rounded border-slate-300 text-sky-600 focus:ring-sky-500"
                           @checked(in_array($opt, $selected, true))>
                    <span class="text-slate-700">{{ $opt }}</span>
                </label>
            @endforeach
        </div>
        <div class="border-t border-slate-100 p-2 flex gap-2 bg-slate-50">
            <input type="text" class="ms-new flex-1 rounded border border-slate-300 px-2 py-1.5 text-sm focus:ring-sky-500 focus:border-sky-500"
                   placeholder="{{ $newPlaceholder }}">
            <button type="button" class="ms-add shrink-0 text-xs bg-sky-600 hover:bg-sky-700 text-white px-3 rounded">Tambah</button>
        </div>
    </div>
</div>
<p class="text-[11px] text-slate-400 mt-1">Bisa pilih lebih dari satu. Ketik di kolom bawah lalu "Tambah" untuk kategori baru. Ejaan dirapikan otomatis.</p>
