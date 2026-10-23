@extends('layouts.app')
@section('title', 'Kamus QC')
@section('subtitle', 'Kamus jenis & detail ketidaksesuaian dan penyebab untuk konsistensi pelaporan')
@section('hide_create_btn', true)

@php
    // Kamus referensi: urutkan jenis & detail secara alfabetis, tanpa angka frekuensi
    $prepare = function (array $rows) {
        usort($rows, fn ($a, $b) => strcasecmp($a['jenis_label'], $b['jenis_label']));
        foreach ($rows as &$r) {
            $names = array_column($r['details'], 'name');
            natcasesort($names);
            $r['detail_names'] = array_values($names);
        }
        unset($r);
        return $rows;
    };

    $tabs = [
        'ketidaksesuaian' => [
            'label'      => 'Ketidaksesuaian (Cacat)',
            'icon'       => 'fa-tags',
            'color'      => '#4e73df',
            'rows'       => $prepare($ketidaksesuaianList),
            'col_jenis'  => 'Jenis Ketidaksesuaian',
            'col_detail' => 'Pilihan Detail Ketidaksesuaian',
            'empty'      => 'Belum ada data ketidaksesuaian.',
        ],
        'penyebab' => [
            'label'      => 'Penyebab (Akar Masalah)',
            'icon'       => 'fa-wrench',
            'color'      => '#f6c23e',
            'rows'       => $prepare($penyebabList),
            'col_jenis'  => 'Jenis Penyebab',
            'col_detail' => 'Pilihan Detail Penyebab',
            'empty'      => 'Belum ada data penyebab.',
        ],
    ];
@endphp

@section('content')
<div x-data="{
        activeTab: 'ketidaksesuaian',
        searchQuery: @js(strtolower($q)),
        match(text) {
            const q = this.searchQuery.trim().toLowerCase();
            return !q || text.includes(q);
        }
     }" class="max-w-7xl mx-auto">

    {{-- Page Heading (SB Admin 2) --}}
    <div class="mb-4">
        <h2 class="font-bold text-[#4e73df] text-base mb-0.5">
            <i class="fas fa-book mr-2"></i> Kamus Ketidaksesuaian dan Penyebab
        </h2>
    </div>

    {{-- Card --}}
    <div class="bg-white rounded-md shadow-[0_0.15rem_1.75rem_0_rgba(58,59,69,0.15)] border border-[#e3e6f0] mb-6">

        {{-- Card Header: Nav Tabs + Search --}}
        <div class="bg-[#f8f9fc] border-b border-[#e3e6f0] rounded-t-md px-4 pt-3 flex flex-col md:flex-row md:items-end md:justify-between gap-3">
            <ul class="flex -mb-px text-sm" role="tablist">
                @foreach($tabs as $key => $tab)
                    <li>
                        <button type="button" id="tab-{{ $key }}" role="tab"
                                @click="activeTab = '{{ $key }}'"
                                :class="activeTab === '{{ $key }}'
                                    ? 'bg-white border-[#e3e6f0] border-b-white text-[#4e73df] font-bold'
                                    : 'border-transparent text-[#858796] hover:text-[#4e73df] hover:border-[#eaecf4]'"
                                class="px-4 py-2.5 border rounded-t-md transition-colors flex items-center gap-2 cursor-pointer">
                            <i class="fas {{ $tab['icon'] }}" style="color: {{ $tab['color'] }}"></i>
                            {{ $tab['label'] }}
                        </button>
                    </li>
                @endforeach
            </ul>

            <div class="relative w-full md:w-72 pb-3">
                <input type="text" id="kamus-search" x-model="searchQuery"
                       placeholder="Cari jenis atau detail..."
                       class="w-full h-9 pl-3 pr-9 text-sm text-[#6e707e] bg-white border border-[#d1d3e2] rounded focus:outline-none focus:border-[#bac8f3] focus:ring-4 focus:ring-[#4e73df]/25 transition">
                <span class="absolute right-0 top-0 h-9 w-9 flex items-center justify-center bg-[#4e73df] text-white rounded-r">
                    <i class="fas fa-search text-xs"></i>
                </span>
            </div>
        </div>

        {{-- Card Body: Tables --}}
        <div class="p-4">
            @foreach($tabs as $key => $tab)
                <div x-show="activeTab === '{{ $key }}'" x-cloak role="tabpanel" class="overflow-x-auto">
                    <table class="w-full text-sm text-[#858796] border border-[#e3e6f0] border-collapse">
                        <thead>
                            <tr class="text-left text-[#5a5c69]">
                                <th class="border border-[#e3e6f0] px-3 py-2.5 w-12 text-center">No</th>
                                <th class="border border-[#e3e6f0] px-3 py-2.5 w-1/4">{{ $tab['col_jenis'] }}</th>
                                <th class="border border-[#e3e6f0] px-3 py-2.5">{{ $tab['col_detail'] }}</th>
                                <th class="border border-[#e3e6f0] px-3 py-2.5 w-28 text-center">Total Kasus</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tab['rows'] as $i => $row)
                                @php
                                    $searchText = strtolower($row['jenis'].' '.$row['jenis_label'].' '.implode(' ', $row['detail_names']));
                                @endphp
                                <tr data-search="{{ $searchText }}"
                                    x-show="match($el.dataset.search)"
                                    class="odd:bg-[#f8f9fc]/60 hover:bg-[#eaecf4]/60 transition-colors">
                                    <td class="border border-[#e3e6f0] px-3 py-2.5 text-center align-top">{{ $i + 1 }}</td>
                                    <td class="border border-[#e3e6f0] px-3 py-2.5 align-top">
                                        <div class="font-bold text-[#5a5c69]">{{ $row['jenis_label'] }}</div>
                                    </td>
                                    <td class="border border-[#e3e6f0] px-3 py-2.5 align-top text-[#5a5c69]">
                                        @if(count($row['detail_names']))
                                            {{ implode(', ', $row['detail_names']) }}
                                        @else
                                            <span class="italic text-[#858796]">-</span>
                                        @endif
                                    </td>
                                    <td class="border border-[#e3e6f0] px-3 py-2.5 align-top text-center font-bold text-[#5a5c69]">{{ $row['total'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="border border-[#e3e6f0] px-3 py-8 text-center">
                                        <i class="fas fa-inbox text-2xl text-[#d1d3e2] mb-2 block"></i>
                                        {{ $tab['empty'] }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
