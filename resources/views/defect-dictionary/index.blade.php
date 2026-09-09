@extends('layouts.app')
@section('title', 'Kamus QC')
@section('hide_create_btn', true)

@section('content')
<div x-data="{ 
    activeTab: 'ketidaksesuaian',
    viewMode: 'cards',
    searchQuery: '{{ strtolower($q) }}',
    matchesSearch(row) {
        if (!this.searchQuery.trim()) return true;
        const q = this.searchQuery.toLowerCase();
        if (row.jenis.toLowerCase().includes(q) || row.jenis_label.toLowerCase().includes(q)) return true;
        if (row.details && row.details.some(d => d.name.toLowerCase().includes(q))) return true;
        return false;
    }
}" class="space-y-6 max-w-7xl mx-auto">

    {{-- Page Header & Search Bar --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h1 class="text-lg font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <span class="text-2xl">📖</span> Kamus Pengelompokan Mutu QC
            </h1>
            <p class="text-xs text-slate-500 mt-1">Pemetaan jenis &amp; rincian detail murni dari riwayat data input komplain real.</p>
        </div>

        {{-- Live Search Input --}}
        <div class="relative w-full md:w-80">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input type="text" x-model="searchQuery" 
                   placeholder="Cari jenis atau rincian detail..."
                   class="w-full pl-9 pr-8 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500 focus:bg-white focus:border-sky-500 transition font-medium text-slate-800 placeholder-slate-400 shadow-2xs">
            <button x-show="searchQuery" @click="searchQuery = ''" class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    {{-- Control Bar: Tab Switcher & View Mode Switcher --}}
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-white rounded-2xl border border-slate-200/80 p-3 shadow-xs">
        {{-- Left: Tab Switcher (Ketidaksesuaian vs Penyebab) --}}
        <div class="flex items-center bg-slate-100 p-1 rounded-xl text-xs font-bold w-full sm:w-auto">
            <button type="button" @click="activeTab = 'ketidaksesuaian'"
                    :class="activeTab === 'ketidaksesuaian' ? 'bg-white shadow-xs text-sky-700 font-extrabold' : 'text-slate-600 hover:text-slate-900'"
                    class="flex-1 sm:flex-none px-4 py-2 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer">
                <span>🏷️</span> Kamus Ketidaksesuaian ({{ count($ketidaksesuaianList) }})
            </button>
            <button type="button" @click="activeTab = 'penyebab'"
                    :class="activeTab === 'penyebab' ? 'bg-white shadow-xs text-amber-700 font-extrabold' : 'text-slate-600 hover:text-slate-900'"
                    class="flex-1 sm:flex-none px-4 py-2 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer">
                <span>🛠️</span> Kamus Penyebab ({{ count($penyebabList) }})
            </button>
        </div>

        {{-- Right: View Mode Switcher (Kartu vs Tabel Data) --}}
        <div class="flex items-center bg-slate-100 p-1 rounded-xl text-xs font-semibold shrink-0">
            <button type="button" @click="viewMode = 'cards'"
                    :class="viewMode === 'cards' ? 'bg-white shadow-xs text-slate-900 font-bold' : 'text-slate-500 hover:text-slate-800'"
                    class="px-3.5 py-1.5 rounded-lg transition-all duration-200 flex items-center gap-1.5 cursor-pointer">
                <span>📱</span> Tampilan Kartu
            </button>
            <button type="button" @click="viewMode = 'table'"
                    :class="viewMode === 'table' ? 'bg-white shadow-xs text-slate-900 font-bold' : 'text-slate-500 hover:text-slate-800'"
                    class="px-3.5 py-1.5 rounded-lg transition-all duration-200 flex items-center gap-1.5 cursor-pointer">
                <span>📊</span> Tabel Data
            </button>
        </div>
    </div>

    {{-- ================================================================= --}}
    {{-- TAB 1: KAMUS KETIDAKSESUAIAN (CACAT)                             --}}
    {{-- ================================================================= --}}
    <div x-show="activeTab === 'ketidaksesuaian'" x-cloak>
        {{-- Mode Kartu Grid --}}
        <div x-show="viewMode === 'cards'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($ketidaksesuaianList as $row)
                <div x-data="{ showAllDetails: false }" x-show="matchesSearch({{ json_encode($row) }})" 
                     class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs hover:shadow-md hover:border-sky-300 transition-all duration-200 flex flex-col justify-between">
                    <div>
                        {{-- Card Header --}}
                        <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-3 mb-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-700 flex items-center justify-center font-bold text-lg shrink-0 border border-sky-200/80">
                                    {{ $row['style']['icon'] }}
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-slate-900 text-sm leading-tight">{{ $row['jenis_label'] }}</h3>
                                    <span class="font-mono text-[10px] text-slate-500 bg-slate-100 px-2 py-0.5 rounded border border-slate-200 inline-block mt-0.5">
                                        {{ $row['jenis'] }}
                                    </span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold font-mono bg-emerald-50 text-emerald-800 border border-emerald-200 shrink-0">
                                {{ $row['total'] }} Kasus
                            </span>
                        </div>

                        {{-- Rincian Detail Ketidaksesuaian --}}
                        <div class="space-y-2 mt-3">
                            <span class="text-[10px] font-mono font-bold text-slate-500 uppercase tracking-wider block">
                                RINCIAN DETAIL KETIDAKSESUAIAN (REAL INPUT):
                            </span>
                            <div class="flex flex-wrap gap-1.5">
                                @php
                                    $visibleDets = array_slice($row['details'], 0, 5);
                                    $hiddenDets = array_slice($row['details'], 5);
                                @endphp
                                @forelse($visibleDets as $det)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-semibold bg-sky-50 text-sky-900 border border-sky-200/80 hover:bg-sky-100 transition">
                                        <span>• {{ $det['name'] }}</span>
                                        <span class="font-bold text-[10px] font-mono bg-sky-100 text-sky-950 px-1.5 py-0.2 rounded-full border border-sky-300/70 ml-0.5">
                                            {{ $det['count'] }}x
                                        </span>
                                    </span>
                                @empty
                                    <span class="text-slate-400 italic text-xs">Belum ada rincian detail terinput</span>
                                @endforelse

                                @if(count($hiddenDets) > 0)
                                    <div x-show="showAllDetails" x-cloak class="contents">
                                        @foreach($hiddenDets as $det)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-semibold bg-sky-50 text-sky-900 border border-sky-200/80 hover:bg-sky-100 transition">
                                                <span>• {{ $det['name'] }}</span>
                                                <span class="font-bold text-[10px] font-mono bg-sky-100 text-sky-950 px-1.5 py-0.2 rounded-full border border-sky-300/70 ml-0.5">
                                                    {{ $det['count'] }}x
                                                </span>
                                            </span>
                                        @endforeach
                                    </div>
                                    <button type="button" @click="showAllDetails = !showAllDetails"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 cursor-pointer transition">
                                        <span x-text="showAllDetails ? 'Ringkas (-)' : '+ {{ count($hiddenDets) }} detail lagi'">+ {{ count($hiddenDets) }} detail lagi</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-2xl p-8 text-center text-slate-400 border border-slate-200">
                    <span class="text-3xl block mb-2">📭</span>
                    <p class="text-sm font-medium text-slate-600">Belum ada data ketidaksesuaian yang sesuai.</p>
                </div>
            @endforelse
        </div>

        {{-- Mode Tabel Data --}}
        <div x-show="viewMode === 'table'" class="bg-white rounded-2xl shadow-xs border border-slate-200/90 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-900 text-white uppercase tracking-wider font-bold text-[11px]">
                        <tr>
                            <th class="px-4 py-3.5 w-12 text-center">No</th>
                            <th class="px-5 py-3.5 w-1/3">Jenis Ketidaksesuaian (Kategori Utama)</th>
                            <th class="px-5 py-3.5 w-2/3">Rincian Detail Ketidaksesuaian (Real Data Input)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php $no = 1; @endphp
                        @forelse($ketidaksesuaianList as $row)
                        <tr x-show="matchesSearch({{ json_encode($row) }})" class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3.5 font-mono font-bold text-slate-400 text-center align-top text-xs">{{ $no++ }}</td>
                            <td class="px-5 py-3.5 align-top">
                                <div class="flex items-center gap-2">
                                    <span class="text-base">{{ $row['style']['icon'] }}</span>
                                    <div>
                                        <span class="font-extrabold text-slate-900 text-xs sm:text-sm">{{ $row['jenis_label'] }}</span>
                                        <span class="ml-1.5 font-mono text-[10px] text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200 inline-block">
                                            {{ $row['jenis'] }}
                                        </span>
                                    </div>
                                </div>
                                <span class="text-[11px] text-slate-500 mt-1 block">Total frekuensi: <b>{{ $row['total'] }} kasus</b></span>
                            </td>
                            <td class="px-5 py-3.5 align-top">
                                <div class="flex flex-wrap gap-1.5">
                                    @forelse($row['details'] as $det)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-sky-50 text-sky-800 border border-sky-200/80">
                                            <span>• {{ $det['name'] }}</span>
                                            <span class="font-bold text-[10px] bg-sky-100 text-sky-900 px-1.5 py-0.2 rounded-full border border-sky-300/60 ml-0.5">
                                                {{ $det['count'] }}x
                                            </span>
                                        </span>
                                    @empty
                                        <span class="text-slate-400 italic text-xs">Belum ada rincian detail terinput</span>
                                    @endforelse
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-5 py-10 text-center text-slate-400 space-y-1">
                                <div class="text-2xl">📭</div>
                                <div class="text-xs font-medium text-slate-600">Tidak ada data ketidaksesuaian yang sesuai.</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ================================================================= --}}
    {{-- TAB 2: KAMUS PENYEBAB (AKAR MASALAH)                              --}}
    {{-- ================================================================= --}}
    <div x-show="activeTab === 'penyebab'" x-cloak>
        {{-- Mode Kartu Grid --}}
        <div x-show="viewMode === 'cards'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($penyebabList as $row)
                <div x-data="{ showAllDetails: false }" x-show="matchesSearch({{ json_encode($row) }})" 
                     class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs hover:shadow-md hover:border-amber-300 transition-all duration-200 flex flex-col justify-between">
                    <div>
                        {{-- Card Header --}}
                        <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-3 mb-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center font-bold text-lg shrink-0 border border-amber-200/80">
                                    {{ $row['style']['icon'] }}
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-slate-900 text-sm leading-tight">{{ $row['jenis_label'] }}</h3>
                                    <span class="font-mono text-[10px] text-slate-500 bg-slate-100 px-2 py-0.5 rounded border border-slate-200 inline-block mt-0.5">
                                        {{ $row['jenis'] }}
                                    </span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold font-mono bg-amber-50 text-amber-800 border border-amber-200 shrink-0">
                                {{ $row['total'] }} Kasus
                            </span>
                        </div>

                        {{-- Rincian Detail Penyebab --}}
                        <div class="space-y-2 mt-3">
                            <span class="text-[10px] font-mono font-bold text-slate-500 uppercase tracking-wider block">
                                RINCIAN DETAIL PENYEBAB (REAL INPUT):
                            </span>
                            <div class="flex flex-wrap gap-1.5">
                                @php
                                    $visibleDets = array_slice($row['details'], 0, 5);
                                    $hiddenDets = array_slice($row['details'], 5);
                                @endphp
                                @forelse($visibleDets as $det)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-semibold bg-amber-50 text-amber-900 border border-amber-200/80 hover:bg-amber-100 transition">
                                        <span>• {{ $det['name'] }}</span>
                                        <span class="font-bold text-[10px] font-mono bg-amber-100 text-amber-950 px-1.5 py-0.2 rounded-full border border-amber-300/70 ml-0.5">
                                            {{ $det['count'] }}x
                                        </span>
                                    </span>
                                @empty
                                    <span class="text-slate-400 italic text-xs">Belum ada rincian detail penyebab terinput</span>
                                @endforelse

                                @if(count($hiddenDets) > 0)
                                    <div x-show="showAllDetails" x-cloak class="contents">
                                        @foreach($hiddenDets as $det)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-semibold bg-amber-50 text-amber-900 border border-amber-200/80 hover:bg-amber-100 transition">
                                                <span>• {{ $det['name'] }}</span>
                                                <span class="font-bold text-[10px] font-mono bg-amber-100 text-amber-950 px-1.5 py-0.2 rounded-full border border-amber-300/70 ml-0.5">
                                                    {{ $det['count'] }}x
                                                </span>
                                            </span>
                                        @endforeach
                                    </div>
                                    <button type="button" @click="showAllDetails = !showAllDetails"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 cursor-pointer transition">
                                        <span x-text="showAllDetails ? 'Ringkas (-)' : '+ {{ count($hiddenDets) }} detail lagi'">+ {{ count($hiddenDets) }} detail lagi</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-2xl p-8 text-center text-slate-400 border border-slate-200">
                    <span class="text-3xl block mb-2">📭</span>
                    <p class="text-sm font-medium text-slate-600">Belum ada data penyebab yang sesuai.</p>
                </div>
            @endforelse
        </div>

        {{-- Mode Tabel Data --}}
        <div x-show="viewMode === 'table'" class="bg-white rounded-2xl shadow-xs border border-slate-200/90 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-900 text-white uppercase tracking-wider font-bold text-[11px]">
                        <tr>
                            <th class="px-4 py-3.5 w-12 text-center">No</th>
                            <th class="px-5 py-3.5 w-1/3">Jenis Penyebab (Akar Masalah Utama)</th>
                            <th class="px-5 py-3.5 w-2/3">Rincian Detail Penyebab (Real Data Input)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php $no = 1; @endphp
                        @forelse($penyebabList as $row)
                        <tr x-show="matchesSearch({{ json_encode($row) }})" class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3.5 font-mono font-bold text-slate-400 text-center align-top text-xs">{{ $no++ }}</td>
                            <td class="px-5 py-3.5 align-top">
                                <div class="flex items-center gap-2">
                                    <span class="text-base">{{ $row['style']['icon'] }}</span>
                                    <div>
                                        <span class="font-extrabold text-slate-900 text-xs sm:text-sm">{{ $row['jenis_label'] }}</span>
                                        <span class="ml-1.5 font-mono text-[10px] text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200 inline-block">
                                            {{ $row['jenis'] }}
                                        </span>
                                    </div>
                                </div>
                                <span class="text-[11px] text-slate-500 mt-1 block">Total frekuensi: <b>{{ $row['total'] }} kasus</b></span>
                            </td>
                            <td class="px-5 py-3.5 align-top">
                                <div class="flex flex-wrap gap-1.5">
                                    @forelse($row['details'] as $det)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200/80">
                                            <span>• {{ $det['name'] }}</span>
                                            <span class="font-bold text-[10px] bg-amber-100 text-amber-900 px-1.5 py-0.2 rounded-full border border-amber-300/60 ml-0.5">
                                                {{ $det['count'] }}x
                                            </span>
                                        </span>
                                    @empty
                                        <span class="text-slate-400 italic text-xs">Belum ada rincian detail penyebab terinput</span>
                                    @endforelse
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-5 py-10 text-center text-slate-400 space-y-1">
                                <div class="text-2xl">📭</div>
                                <div class="text-xs font-medium text-slate-600">Tidak ada data penyebab yang sesuai.</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
