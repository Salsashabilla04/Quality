@extends('layouts.app')
@section('title', 'Seven Tools Quality Control')
@section('subtitle', 'Analisis pengendalian kualitas terpadu dengan 7 alat bantu statistik mutu (QC 7 Tools)')
@section('hide_create_btn', true)

@section('content')
@php
    function tool_header($no, $title, $desc) {
        return "<div class='flex items-start gap-3 mb-4'>
            <span class='shrink-0 w-8 h-8 rounded bg-[#4e73df] text-white flex items-center justify-center font-bold text-sm shadow-sm'>{$no}</span>
            <div><h2 class='font-bold text-[#4e73df] text-base mb-0.5'>{$title}</h2><p class='text-xs text-gray-500'>{$desc}</p></div>
        </div>";
    }
@endphp

<div class="space-y-8">

    <!-- 1. CHECK SHEET (SB Admin 2 Style) -->
    <div class="card-sb border border-[#e3e6f0] rounded shadow-sm overflow-hidden mb-8">
        <div class="card-sb-header py-3 px-4 bg-[#f8f9fc] border-b border-[#e3e6f0] flex flex-col md:flex-row md:items-center justify-between gap-2">
            <div class="flex items-center gap-2.5">
                <span class="w-6 h-6 rounded bg-[#4e73df] text-white flex items-center justify-center font-bold text-xs shrink-0">1</span>
                <div>
                    <h6 class="m-0 font-bold text-[#4e73df] text-sm leading-tight">Check Sheet (Lembar Pemeriksaan Mutu)</h6>
                    <small class="text-xs text-[#858796] block mt-0.5">Tabulasi frekuensi kemunculan ketidaksesuaian per bulan untuk rekapitulasi data lapangan</small>
                </div>
            </div>
        </div>

        {{-- Mode CheckSheet Filter Bar --}}
        <div class="px-4 py-2.5 border-b border-[#e3e6f0] bg-[#f8f9fc] flex flex-wrap items-center justify-between gap-3 text-xs">
            <form method="GET" action="{{ url()->current() }}" class="flex flex-wrap items-center gap-2">
                @foreach (['pareto_year','pareto_month','pareto_detail_year','pareto_detail_month','pareto_cust_year','pareto_cust_month','pareto_cause_year','pareto_cause_month'] as $p)
                    @if (request($p))<input type="hidden" name="{{ $p }}" value="{{ request($p) }}">@endif
                @endforeach

                <label class="text-gray-600 font-bold uppercase tracking-wide text-[11px] m-0">Tabulasi Berdasarkan:</label>
                <select name="cs_mode" onchange="this.form.submit()"
                        class="rounded-md border border-gray-300 px-3 py-1 text-xs font-semibold focus:ring-[#4e73df] focus:border-[#4e73df] bg-white h-8 text-gray-700 cursor-pointer shadow-2xs">
                    <option value="jenis_ketidaksesuaian" @selected($csMode === 'jenis_ketidaksesuaian')>Jenis Ketidaksesuaian</option>
                    <option value="detail_ketidaksesuaian" @selected($csMode === 'detail_ketidaksesuaian')>Detail Ketidaksesuaian</option>
                </select>
            </form>

            <div class="text-xs text-gray-500">
                Total Komplain: <b class="text-[#4e73df] font-bold">{{ $checkSheet['grand_total'] }}</b> Kasus
            </div>
        </div>

        <div class="p-4 overflow-x-auto">
            <table class="w-full text-xs border border-[#e3e6f0] border-collapse">
                <thead>
                    <tr class="bg-[#f8f9fc] text-[#4e73df] border-b-2 border-gray-200 text-xs font-bold uppercase tracking-wider">
                        <th class="border border-[#e3e6f0] px-3.5 py-2.5 text-left sticky left-0 bg-[#f8f9fc] z-10">
                            {{ $csMode === 'detail_ketidaksesuaian' ? 'Detail Ketidaksesuaian' : 'Jenis Ketidaksesuaian' }}
                        </th>
                        @foreach ($checkSheet['bulan'] as $b)
                            <th class="border border-[#e3e6f0] px-2.5 py-2.5 whitespace-nowrap text-center text-gray-700 font-bold">{{ $b }}</th>
                        @endforeach
                        <th class="border border-[#e3e6f0] px-3.5 py-2.5 bg-[#4e73df]/10 text-[#4e73df] text-center font-extrabold">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e3e6f0]">
                    @foreach ($checkSheet['rows'] as $row)
                        <tr class="hover:bg-[#f8f9fc] transition-colors">
                            <td class="border border-[#e3e6f0] px-3.5 py-2 font-semibold text-gray-700 sticky left-0 bg-white z-5">{{ $row['kategori'] }}</td>
                            @foreach ($row['per_bulan'] as $v)
                                <td class="border border-[#e3e6f0] px-2 py-2 text-center {{ $v ? 'text-gray-800 font-bold' : 'text-gray-300' }}">{{ $v ?: '·' }}</td>
                            @endforeach
                            <td class="border border-[#e3e6f0] px-3.5 py-2 text-center font-bold bg-[#f8f9fc] text-[#4e73df]">{{ $row['total'] }}</td>
                        </tr>
                    @endforeach
                    <tr class="bg-[#eaecf4] font-bold text-gray-800 border-t-2 border-gray-300">
                        <td class="border border-[#e3e6f0] px-3.5 py-2.5 sticky left-0 bg-[#eaecf4] uppercase tracking-wider text-gray-800 z-5">TOTAL KESELURUHAN</td>
                        @foreach ($checkSheet['total_per_bulan'] as $v)
                            <td class="border border-[#e3e6f0] px-2.5 py-2.5 text-center font-mono font-bold text-gray-800 text-xs">{{ $v }}</td>
                        @endforeach
                        <td class="border border-[#e3e6f0] px-3.5 py-2.5 text-center bg-[#4e73df] text-white font-mono font-bold text-sm shadow-xs">{{ $checkSheet['grand_total'] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 2. DIAGRAM PARETO (4 VARIAN - 1-BY-1 SB ADMIN 2 THEME) -->
    <section id="pareto-section" class="mb-8">
        <div class="card-sb border border-[#e3e6f0] rounded shadow-sm overflow-hidden">
            <div class="card-sb-header py-3 px-4 bg-[#f8f9fc] border-b border-[#e3e6f0] flex flex-col md:flex-row md:items-center justify-between gap-2">
                <div class="flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded bg-[#4e73df] text-white flex items-center justify-center font-bold text-xs shrink-0">2</span>
                    <div>
                        <h6 class="m-0 font-bold text-[#4e73df] text-sm leading-tight">Diagram Pareto (Prinsip 80/20 Prioritas Masalah)</h6>
                        <small class="text-xs text-[#858796] block mt-0.5">Visualisasi frekuensi dan persentase kumulatif untuk menentukan 20% faktor vital pengendali 80% masalah</small>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-[#f8f9fc] space-y-6">
            <!-- 2a. PARETO JENIS -->
            <section id="paretoCard" class="card shadow-sm border border-[#e3e6f0] rounded overflow-hidden scroll-mt-6">
                <div class="card-header py-3 px-4 bg-[#f8f9fc] border-b border-[#e3e6f0] flex flex-wrap items-center justify-between gap-2">
                    <h6 class="m-0 font-bold text-[#4e73df] text-sm flex items-center gap-2">
                        <i class="fas fa-chart-bar text-[#4e73df]"></i>
                        <span>Diagram Pareto — Jenis Ketidaksesuaian</span>
                    </h6>
                    <span class="badge badge-primary font-bold text-xs px-2.5 py-1">Prioritas Kategori Masalah</span>
                </div>
                <div class="card-body p-4 bg-white">
                    @include('partials._pareto_filter')
                    <div class="relative h-80 md:h-96 w-full mt-2">
                        @if (empty($pareto['labels']))
                            <div class="absolute inset-0 flex items-center justify-center text-sm text-[#858796] italic">
                                <i class="fas fa-info-circle mr-1.5"></i> Tidak ada data pada periode ini.
                            </div>
                        @else
                            <canvas id="paretoChart"></canvas>
                        @endif
                    </div>
                </div>
            </section>

            <!-- 2b. PARETO DETAIL -->
            <section id="paretoDetailCard" class="card shadow-sm border border-[#e3e6f0] rounded overflow-hidden scroll-mt-6">
                <div class="card-header py-3 px-4 bg-[#f8f9fc] border-b border-[#e3e6f0] flex flex-wrap items-center justify-between gap-2">
                    <h6 class="m-0 font-bold text-[#4e73df] text-sm flex items-center gap-2">
                        <i class="fas fa-chart-bar text-[#f6c23e]"></i>
                        <span>Diagram Pareto — Detail Ketidaksesuaian</span>
                    </h6>
                    <span class="badge badge-warning text-dark font-bold text-xs px-2.5 py-1">Prioritas Cacat Spesifik</span>
                </div>
                <div class="card-body p-4 bg-white">
                    @include('partials._pareto_detail_filter')
                    <div class="relative h-80 md:h-96 w-full mt-2">
                        @if (empty($paretoDetail['labels']))
                            <div class="absolute inset-0 flex items-center justify-center text-sm text-[#858796] italic">
                                <i class="fas fa-info-circle mr-1.5"></i> Tidak ada data pada periode ini.
                            </div>
                        @else
                            <canvas id="paretoDetailChart"></canvas>
                        @endif
                    </div>
                </div>
            </section>

            <!-- 2c. PARETO CUSTOMER -->
            <section id="paretoCustCard" class="card shadow-sm border border-[#e3e6f0] rounded overflow-hidden scroll-mt-6">
                <div class="card-header py-3 px-4 bg-[#f8f9fc] border-b border-[#e3e6f0] flex flex-wrap items-center justify-between gap-2">
                    <h6 class="m-0 font-bold text-[#4e73df] text-sm flex items-center gap-2">
                        <i class="fas fa-users text-[#36b9cc]"></i>
                        <span>Diagram Pareto — Frekuensi Customer</span>
                    </h6>
                    <span class="badge badge-info font-bold text-xs px-2.5 py-1">Prioritas Akun Customer</span>
                </div>
                <div class="card-body p-4 bg-white">
                    @include('partials._pareto_cust_filter')
                    <div class="relative h-80 md:h-96 w-full mt-2">
                        @if (empty($paretoCust['labels']))
                            <div class="absolute inset-0 flex items-center justify-center text-sm text-[#858796] italic">
                                <i class="fas fa-info-circle mr-1.5"></i> Tidak ada data pada periode ini.
                            </div>
                        @else
                            <canvas id="paretoCustChart"></canvas>
                        @endif
                    </div>
                </div>
            </section>

            <!-- 2d. PARETO PENYEBAB -->
            <section id="paretoCauseCard" class="card shadow-sm border border-[#e3e6f0] rounded overflow-hidden scroll-mt-6">
                <div class="card-header py-3 px-4 bg-[#f8f9fc] border-b border-[#e3e6f0] flex flex-wrap items-center justify-between gap-2">
                    <h6 class="m-0 font-bold text-[#4e73df] text-sm flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle text-[#1cc88a]"></i>
                        <span>Diagram Pareto — Akar Penyebab Dominan</span>
                    </h6>
                    <span class="badge badge-success font-bold text-xs px-2.5 py-1">Prioritas Akar Penyebab (6M)</span>
                </div>
                <div class="card-body p-4 bg-white">
                    @include('partials._pareto_cause_filter')
                    <div class="relative h-80 md:h-96 w-full mt-2">
                        @if (empty($paretoCause['labels']))
                            <div class="absolute inset-0 flex items-center justify-center text-sm text-[#858796] italic">
                                <i class="fas fa-info-circle mr-1.5"></i> Tidak ada data pada periode ini.
                            </div>
                        @else
                            <canvas id="paretoCauseChart"></canvas>
                        @endif
                    </div>
                </div>
            </section>
        </div>
    </div>
</section>

    <!-- 3. FISHBONE / ISHIKAWA (6M) -->
    <section id="fishbone-section" class="mb-6">
        <div class="card-sb border border-[#e3e6f0] rounded shadow-sm overflow-hidden">
            <!-- Header Rapi & Kompak: Judul + Filter Inline (Tanpa Box Bertumpuk) -->
            <div class="card-sb-header py-3 px-4 bg-[#f8f9fc] border-b border-[#e3e6f0] flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded bg-[#4e73df] text-white flex items-center justify-center font-bold text-xs shrink-0">3</span>
                    <div>
                        <h6 class="m-0 font-bold text-[#4e73df] text-sm leading-tight">Diagram Sebab-Akibat Ishikawa (6M)</h6>
                        <small class="text-xs text-[#858796] block mt-0.5">Pilih salah satu penyebab di bawah untuk menyorot tindakan perbaikan (CAPA)</small>
                    </div>
                </div>

                <!-- Form Filter Ringkas Inline (Hanya 1 baris di header) -->
                <form method="GET" action="{{ route('seven-tools') }}#fishbone-section" class="flex flex-wrap items-center gap-2 m-0">
                    @if(request('cs_mode')) <input type="hidden" name="cs_mode" value="{{ request('cs_mode') }}"> @endif
                    @if(request('pareto_year')) <input type="hidden" name="pareto_year" value="{{ request('pareto_year') }}"> @endif
                    @if(request('pareto_month')) <input type="hidden" name="pareto_month" value="{{ request('pareto_month') }}"> @endif
                    @if(request('pareto_detail_year')) <input type="hidden" name="pareto_detail_year" value="{{ request('pareto_detail_year') }}"> @endif
                    @if(request('pareto_detail_month')) <input type="hidden" name="pareto_detail_month" value="{{ request('pareto_detail_month') }}"> @endif
                    @if(request('pareto_cust_year')) <input type="hidden" name="pareto_cust_year" value="{{ request('pareto_cust_year') }}"> @endif
                    @if(request('pareto_cust_month')) <input type="hidden" name="pareto_cust_month" value="{{ request('pareto_cust_month') }}"> @endif
                    @if(request('pareto_cause_year')) <input type="hidden" name="pareto_cause_year" value="{{ request('pareto_cause_year') }}"> @endif
                    @if(request('pareto_cause_month')) <input type="hidden" name="pareto_cause_month" value="{{ request('pareto_cause_month') }}"> @endif

                    <div class="flex items-center gap-1.5">
                        <span class="text-xs text-[#858796] font-semibold">Akibat:</span>
                        <select name="fishbone_efek" id="fishbone_efek" onchange="this.form.submit()" class="text-xs font-semibold px-2.5 py-1 rounded border border-[#d1d3e2] bg-white text-[#5a5c69] focus:outline-none focus:border-[#4e73df] cursor-pointer shadow-none max-w-[220px]">
                            <option value="AUTO" {{ ($fishbone['selected_efek'] ?? 'AUTO') === 'AUTO' ? 'selected' : '' }}>
                                Auto: {{ $fishbone['top_efek'] ?? 'Tertinggi' }}
                            </option>
                            <option value="ALL" {{ ($fishbone['selected_efek'] ?? '') === 'ALL' ? 'selected' : '' }}>
                                Semua Masalah (Global)
                            </option>
                            @if(!empty($fishbone['available_jenis']))
                                <optgroup label="Jenis Ketidaksesuaian">
                                    @foreach ($fishbone['available_jenis'] as $jOpt)
                                        <option value="{{ $jOpt }}" {{ ($fishbone['selected_efek'] ?? '') === $jOpt ? 'selected' : '' }}>
                                            {{ $jOpt }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                            @if(!empty($fishbone['available_detail']))
                                <optgroup label="Detail Cacat Spesifik">
                                    @foreach ($fishbone['available_detail'] as $dOpt)
                                        <option value="{{ $dOpt }}" {{ ($fishbone['selected_efek'] ?? '') === $dOpt ? 'selected' : '' }}>
                                            {{ $dOpt }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        </select>
                    </div>

                    <input type="hidden" name="fishbone_level" value="detail">

                    <span class="badge badge-primary font-bold text-xs px-2.5 py-1">
                        {{ array_sum(array_column($fishbone['categories'], 'total')) }} Isu
                    </span>
                </form>
            </div>

            <!-- Body: Grid 6 Kategori 6M -->
            <div class="p-4 bg-[#f8f9fc]">
                @php
                    $categoriesConfig = [
                        'Man' => [
                            'title' => 'Man (Tenaga Kerja)',
                            'border' => 'border-left-primary',
                            'text_color' => 'text-[#4e73df]',
                            'icon' => 'fa-user',
                            'badge' => 'badge-primary'
                        ],
                        'Material' => [
                            'title' => 'Material (Bahan Baku)',
                            'border' => 'border-left-success',
                            'text_color' => 'text-[#1cc88a]',
                            'icon' => 'fa-boxes',
                            'badge' => 'badge-success'
                        ],
                        'Machine' => [
                            'title' => 'Machine (Mesin & Alat)',
                            'border' => 'border-left-warning',
                            'text_color' => 'text-[#f6c23e]',
                            'icon' => 'fa-cogs',
                            'badge' => 'badge-warning text-dark'
                        ],
                        'Method' => [
                            'title' => 'Method (Metode Kerja)',
                            'border' => 'border-left-danger',
                            'text_color' => 'text-[#e74a3b]',
                            'icon' => 'fa-tasks',
                            'badge' => 'badge-danger'
                        ],
                        'Measurement' => [
                            'title' => 'Measurement (Pengukuran)',
                            'border' => 'border-left-purple',
                            'text_color' => 'text-[#6f42c1]',
                            'icon' => 'fa-ruler-combined',
                            'badge' => 'badge-purple'
                        ],
                        'Environment' => [
                            'title' => 'Environment (Lingkungan)',
                            'border' => 'border-left-info',
                            'text_color' => 'text-[#36b9cc]',
                            'icon' => 'fa-leaf',
                            'badge' => 'badge-info'
                        ],
                    ];
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($categoriesConfig as $catKey => $cfg)
                        @php $cData = collect($fishbone['categories'])->firstWhere('kategori', $catKey); @endphp
                        <div class="bg-white border border-[#e3e6f0] {{ $cfg['border'] }} shadow-sm rounded flex flex-col justify-between h-full transition hover:shadow">
                            <!-- Category Header -->
                            <div class="px-4 py-2.5 border-b border-[#e3e6f0] flex items-center justify-between bg-white">
                                <div class="flex items-center gap-2">
                                    <i class="fas {{ $cfg['icon'] }} {{ $cfg['text_color'] }} text-sm"></i>
                                    <span class="font-bold text-xs uppercase tracking-wide {{ $cfg['text_color'] }}">
                                        {{ $cfg['title'] }}
                                    </span>
                                </div>
                                <span class="badge {{ ($cData['total'] ?? 0) > 0 ? $cfg['badge'] : 'badge-light border text-[#858796]' }} font-bold text-[11px] px-2 py-0.5">
                                    {{ $cData['total'] ?? 0 }} isu
                                </span>
                            </div>

                            <!-- Causes List (Klik untuk Sorot Penyebab + CAPA) -->
                            <div class="p-3 flex-1 flex flex-col justify-between">
                                <ul class="space-y-2">
                                    @forelse ($cData['top_causes'] ?? [] as $c)
                                        <li>
                                            <button type="button" 
                                                    onclick='showFishboneModal(@json($c), "{{ $catKey }}")'
                                                    class="w-full text-left p-2.5 rounded border border-[#e3e6f0] bg-white hover:bg-[#f8f9fc] hover:border-[#4e73df] flex items-center justify-between transition group cursor-pointer">
                                                <div class="flex-1 pr-2">
                                                    <div class="flex items-center gap-1.5 flex-wrap">
                                                        @if($loop->first && ($c['jumlah'] ?? 0) > 0)
                                                            <span class="badge badge-warning text-dark text-[9px] px-1.5 py-0.5 font-bold">
                                                                <i class="fas fa-fire mr-0.5"></i>Top
                                                            </span>
                                                        @endif
                                                        <span class="text-xs text-[#5a5c69] font-medium leading-snug group-hover:text-[#4e73df] transition">
                                                            {{ $c['nama'] }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <span class="badge badge-light border border-[#d1d3e2] text-[#4e73df] font-bold text-xs px-2 py-1 shrink-0">
                                                    {{ $c['jumlah'] }}
                                                </span>
                                            </button>
                                        </li>
                                    @empty
                                        <li class="py-5 text-center">
                                            <i class="fas fa-check-circle text-emerald-400 text-sm mb-1 block"></i>
                                            <span class="text-xs text-[#858796] italic">Tidak ada isu terdeteksi</span>
                                        </li>
                                    @endforelse
                                </ul>

                                @if(($cData['other_count'] ?? 0) > 0)
                                    <div class="mt-3 pt-2.5 border-t border-[#e3e6f0] text-center">
                                        <button type="button" onclick='showFishboneCategoryAllModal(@json($cData), "{{ $catKey }}")' class="text-xs font-bold text-[#4e73df] hover:underline flex items-center justify-center gap-1.5 w-full py-1">
                                            <i class="fas fa-list-ul"></i>
                                            <span>Lihat +{{ $cData['other_count'] }} penyebab lainnya</span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

</div>

<!-- MODAL LAPORAN PENELUSURAN MASALAH SPESIFIK (SB ADMIN 2 THEME) -->
<div id="fishboneDetailModal"
     class="fixed inset-0 !m-0 z-[9999] hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto"
     onclick="if (event.target === this) closeFishboneModal()">
    <div class="bg-white rounded max-w-4xl w-full border border-[#e3e6f0] shadow-2xl overflow-hidden transform transition-all my-8"
         onclick="event.stopPropagation()">
        <!-- Modal Header SB Admin 2 -->
        <div class="bg-[#f8f9fc] text-[#5a5c69] px-4 py-3 flex items-center justify-between border-b border-[#e3e6f0]">
            <div class="flex items-center gap-2.5">
                <span id="modalCategoryBadge" class="badge badge-primary uppercase font-bold text-xs px-2.5 py-1">MACHINE</span>
                <div>
                    <h6 class="font-bold text-sm text-[#4e73df] m-0 flex items-center gap-1.5">
                        <i class="fas fa-list-alt"></i>
                        <span>Laporan Penelusuran Masalah Spesifik</span>
                    </h6>
                    <small class="text-xs text-[#858796]">Penyebab: <strong id="modalCauseTitle" class="text-[#5a5c69]">MC Tidak Stabil</strong></small>
                </div>
            </div>
            <button type="button" onclick="closeFishboneModal()" class="text-[#858796] hover:text-[#5a5c69] text-2xl font-bold leading-none px-2">&times;</button>
        </div>

        <!-- Modal Content -->
        <div class="p-4 space-y-3 max-h-[70vh] overflow-y-auto">
            <div class="flex items-center justify-between text-xs text-[#858796] pb-2 border-b border-[#e3e6f0]">
                <span>Daftar Transaksi Komplain &amp; Tindakan Perbaikan Lengkap</span>
                <span id="modalTotalCount" class="badge badge-light border font-bold text-[#5a5c69]">Total 0 Komplain</span>
            </div>

            <div class="overflow-x-auto border border-[#e3e6f0] rounded">
                <table class="w-full text-xs text-left">
                    <thead class="bg-[#f8f9fc] text-[#5a5c69] uppercase font-bold text-[11px] border-b border-[#e3e6f0]">
                        <tr>
                            <th class="px-3 py-2.5">No. NCR &amp; Customer</th>
                            <th class="px-3 py-2.5">Tanggal &amp; Area</th>
                            <th class="px-3 py-2.5">Cacat Spesifik</th>
                            <th class="px-3 py-2.5 text-right">Qty</th>
                            <th class="px-3 py-2.5">Tindakan Perbaikan (CAPA)</th>
                        </tr>
                    </thead>
                    <tbody id="modalTableBody" class="divide-y divide-[#e3e6f0] text-[#5a5c69]">
                        <!-- Populated dynamically via JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="bg-[#f8f9fc] px-4 py-2.5 border-t border-[#e3e6f0] flex items-center justify-end">
            <button type="button" onclick="closeFishboneModal()" class="btn btn-secondary btn-sm">
                Tutup Laporan
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showFishboneModal(causeData, categoryName) {
    const modal = document.getElementById('fishboneDetailModal');
    const badge = document.getElementById('modalCategoryBadge');
    const title = document.getElementById('modalCauseTitle');
    const total = document.getElementById('modalTotalCount');
    const tbody = document.getElementById('modalTableBody');

    if (!modal || !causeData) return;

    badge.innerText = categoryName;
    title.innerText = causeData.nama || '-';
    const complaints = causeData.complaints || [];
    total.innerText = `Total ${complaints.length} Kasus Komplain`;

    if (complaints.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" class="px-4 py-6 text-center text-[#858796] italic">Tidak ada rincian data komplain untuk penyebab ini.</td></tr>`;
    } else {
        tbody.innerHTML = complaints.map(c => `
            <tr class="hover:bg-slate-50 transition">
                <td class="px-3 py-2.5 font-medium">
                    <div class="font-bold text-[#4e73df]">${c.no_customer || '-'}</div>
                    <div class="text-[#5a5c69] font-semibold">${c.nama_customer || '-'}</div>
                </td>
                <td class="px-3 py-2.5 text-[#5a5c69]">
                    <div class="flex items-center gap-1.5"><i class="fas fa-calendar-alt text-[#858796]"></i> <span>${c.tanggal_complain || '-'}</span></div>
                    <div class="text-[11px] text-[#858796] flex items-center gap-1.5 mt-0.5"><i class="fas fa-map-marker-alt text-[#858796]"></i> <span>${c.area || '-'}</span></div>
                </td>
                <td class="px-3 py-2.5">
                    <span class="badge badge-warning text-dark text-[10px] font-bold">${c.jenis_ketidaksesuaian || '-'}</span>
                    <div class="text-[#5a5c69] mt-1 font-medium">${c.detail_ketidaksesuaian || '-'}</div>
                </td>
                <td class="px-3 py-2.5 text-right font-bold text-[#5a5c69]">
                    ${c.qty ? c.qty.toLocaleString() : '-'}
                </td>
                <td class="px-3 py-2.5 space-y-1 text-[11px]">
                    <div class="bg-emerald-50 text-emerald-900 p-2 rounded border border-emerald-200">
                        <strong class="text-emerald-700">Corrective:</strong> ${c.corrective_action || '-'}
                    </div>
                    <div class="bg-blue-50 text-blue-900 p-2 rounded border border-blue-200">
                        <strong class="text-blue-700">Preventive:</strong> ${c.preventive_action || '-'}
                    </div>
                </td>
            </tr>
        `).join('');
    }

    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function showFishboneCategoryAllModal(cData, categoryName) {
    if (!cData || !cData.causes) return;
    let allComplaints = [];
    cData.causes.forEach(c => {
        if (c.complaints) allComplaints = allComplaints.concat(c.complaints);
    });
    const aggregatedCause = {
        nama: `Seluruh Isu Kategori ${categoryName}`,
        complaints: allComplaints
    };
    showFishboneModal(aggregatedCause, categoryName);
}

function closeFishboneModal() {
    const modal = document.getElementById('fishboneDetailModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
}

// Tutup modal otomatis saat klik area latar belakang (luar dialog) atau tekan Escape
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('fishboneDetailModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeFishboneModal();
            }
        });
    }
});
window.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeFishboneModal();
    }
});

const pareto = @json($pareto);
const paretoDetail = @json($paretoDetail);
const paretoCust = @json($paretoCust);
const paretoCause = @json($paretoCause);
const histogram = @json($histogram);
const controlChartData = @json($controlChart);
const scatterData = @json($scatter);
const stratData = @json($strat);

const PALET = ['#0ea5e9','#6366f1','#10b981','#f59e0b','#ef4444','#ec4899','#14b8a6','#8b5cf6','#64748b','#84cc16','#f97316','#06b6d4','#a855f7','#22c55e'];

// 2a. Pareto Jenis
const paretoCanvas = document.getElementById('paretoChart');
if (paretoCanvas && pareto.labels && pareto.labels.length) {
    new Chart(paretoCanvas, {
        type: 'bar',
        data: { labels: pareto.labels, datasets: [
            { type:'bar', label:'Jumlah Kasus', data: pareto.values, backgroundColor:'#4e73df', yAxisID:'y', order:2, borderRadius: 4, barPercentage: 0.6 },
            { type:'line', label:'Kumulatif %', data: pareto.cumulative, borderColor:'#e74a3b', backgroundColor:'#e74a3b', yAxisID:'y1', tension:.3, order:1, pointRadius: 4, pointHoverRadius: 6 }
        ]},
        options: { responsive:true, maintainAspectRatio:false,
            plugins:{ legend:{ labels:{ boxWidth:12, font:{size:11, family:"Nunito, sans-serif", weight:'bold'} } } },
            scales:{ 
                x:{ grid:{ color:'#eaecf4' }, ticks:{ font:{size:11, family:"Nunito, sans-serif"}, maxRotation:45, minRotation:0 } },
                y:{ beginAtZero:true, grid:{ color:'#eaecf4' }, ticks:{ font:{size:11, family:"Nunito, sans-serif"} }, title:{ display:true, text:'Jumlah Kasus', font:{size:11, weight:'bold'} } }, 
                y1:{ beginAtZero:true, max:100, position:'right', grid:{drawOnChartArea:false}, ticks:{ font:{size:11, family:"Nunito, sans-serif"}, callback: v => v + '%' }, title:{ display:true, text:'Persentase Kumulatif', font:{size:11, weight:'bold'} } } 
            } 
        }
    });
}

// 2b. Pareto Detail
const paretoDetailCanvas = document.getElementById('paretoDetailChart');
if (paretoDetailCanvas && paretoDetail.labels && paretoDetail.labels.length) {
    new Chart(paretoDetailCanvas, {
        type: 'bar',
        data: { labels: paretoDetail.labels, datasets: [
            { type:'bar', label:'Jumlah Kasus', data: paretoDetail.values, backgroundColor:'#f6c23e', yAxisID:'y', order:2, borderRadius: 4, barPercentage: 0.6 },
            { type:'line', label:'Kumulatif %', data: paretoDetail.cumulative, borderColor:'#e74a3b', backgroundColor:'#e74a3b', yAxisID:'y1', tension:.3, order:1, pointRadius: 4, pointHoverRadius: 6 }
        ]},
        options: { responsive:true, maintainAspectRatio:false,
            plugins:{ legend:{ labels:{ boxWidth:12, font:{size:11, family:"Nunito, sans-serif", weight:'bold'} } } },
            scales:{ 
                x:{ grid:{ color:'#eaecf4' }, ticks:{ font:{size:11, family:"Nunito, sans-serif"}, maxRotation:45, minRotation:0 } },
                y:{ beginAtZero:true, grid:{ color:'#eaecf4' }, ticks:{ font:{size:11, family:"Nunito, sans-serif"} }, title:{ display:true, text:'Jumlah Kasus', font:{size:11, weight:'bold'} } }, 
                y1:{ beginAtZero:true, max:100, position:'right', grid:{drawOnChartArea:false}, ticks:{ font:{size:11, family:"Nunito, sans-serif"}, callback: v => v + '%' }, title:{ display:true, text:'Persentase Kumulatif', font:{size:11, weight:'bold'} } } 
            } 
        }
    });
}

// 2c. Pareto Customer
const paretoCustCanvas = document.getElementById('paretoCustChart');
if (paretoCustCanvas && paretoCust.labels && paretoCust.labels.length) {
    new Chart(paretoCustCanvas, {
        type: 'bar',
        data: { labels: paretoCust.labels, datasets: [
            { type:'bar', label:'Jumlah Komplain', data: paretoCust.values, backgroundColor:'#36b9cc', yAxisID:'y', order:2, borderRadius: 4, barPercentage: 0.6 },
            { type:'line', label:'Kumulatif %', data: paretoCust.cumulative, borderColor:'#e74a3b', backgroundColor:'#e74a3b', yAxisID:'y1', tension:.3, order:1, pointRadius: 4, pointHoverRadius: 6 }
        ]},
        options: { responsive:true, maintainAspectRatio:false,
            plugins:{ legend:{ labels:{ boxWidth:12, font:{size:11, family:"Nunito, sans-serif", weight:'bold'} } } },
            scales:{ 
                x:{ grid:{ color:'#eaecf4' }, ticks:{ font:{size:11, family:"Nunito, sans-serif"}, maxRotation:45, minRotation:0 } },
                y:{ beginAtZero:true, grid:{ color:'#eaecf4' }, ticks:{ font:{size:11, family:"Nunito, sans-serif"} }, title:{ display:true, text:'Jumlah Kasus Komplain', font:{size:11, weight:'bold'} } }, 
                y1:{ beginAtZero:true, max:100, position:'right', grid:{drawOnChartArea:false}, ticks:{ font:{size:11, family:"Nunito, sans-serif"}, callback: v => v + '%' }, title:{ display:true, text:'Persentase Kumulatif', font:{size:11, weight:'bold'} } } 
            } 
        }
    });
}

// 2d. Pareto Penyebab Masalah
const paretoCauseCanvas = document.getElementById('paretoCauseChart');
if (paretoCauseCanvas && paretoCause.labels && paretoCause.labels.length) {
    new Chart(paretoCauseCanvas, {
        type: 'bar',
        data: { labels: paretoCause.labels, datasets: [
            { type:'bar', label:'Jumlah Kemunculan', data: paretoCause.values, backgroundColor:'#1cc88a', yAxisID:'y', order:2, borderRadius: 4, barPercentage: 0.6 },
            { type:'line', label:'Kumulatif %', data: paretoCause.cumulative, borderColor:'#e74a3b', backgroundColor:'#e74a3b', yAxisID:'y1', tension:.3, order:1, pointRadius: 4, pointHoverRadius: 6 }
        ]},
        options: { responsive:true, maintainAspectRatio:false,
            plugins:{ legend:{ labels:{ boxWidth:12, font:{size:11, family:"Nunito, sans-serif", weight:'bold'} } } },
            scales:{ 
                x:{ grid:{ color:'#eaecf4' }, ticks:{ font:{size:11, family:"Nunito, sans-serif"}, maxRotation:45, minRotation:0 } },
                y:{ beginAtZero:true, grid:{ color:'#eaecf4' }, ticks:{ font:{size:11, family:"Nunito, sans-serif"} }, title:{ display:true, text:'Jumlah Kasus', font:{size:11, weight:'bold'} } }, 
                y1:{ beginAtZero:true, max:100, position:'right', grid:{drawOnChartArea:false}, ticks:{ font:{size:11, family:"Nunito, sans-serif"}, callback: v => v + '%' }, title:{ display:true, text:'Persentase Kumulatif', font:{size:11, weight:'bold'} } } 
            } 
        }
    });
}

// 3. Histogram
const histCanvas = document.getElementById('histogramChart');
if (histCanvas && histogram.labels) {
    new Chart(histCanvas, {
        type: 'bar',
        data: {
            labels: histogram.labels,
            datasets: [{
                label: 'Frekuensi Komplain',
                data: histogram.values,
                backgroundColor: '#38bdf8',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { title: { display: true, text: 'Rentang Qty NG (pcs)', font: { size: 10 } } },
                y: { beginAtZero: true, title: { display: true, text: 'Jumlah Kasus', font: { size: 10 } } }
            }
        }
    });
}

// 4. Control Chart (c-chart)
const ccCanvas = document.getElementById('controlChart');
if (ccCanvas && controlChartData.labels) {
    new Chart(ccCanvas, {
        type: 'line',
        data: {
            labels: controlChartData.labels,
            datasets: [
                {
                    label: 'Jumlah Cacat (c)',
                    data: controlChartData.values,
                    borderColor: '#0284c7',
                    backgroundColor: '#0284c7',
                    borderWidth: 2,
                    tension: 0.1,
                    pointRadius: 4,
                    pointBackgroundColor: controlChartData.values.map(v => (v > controlChartData.ucl || v < controlChartData.lcl) ? '#ef4444' : '#0284c7')
                },
                {
                    label: 'UCL (' + controlChartData.ucl + ')',
                    data: controlChartData.ucl_line,
                    borderColor: '#ef4444',
                    borderDash: [5, 5],
                    borderWidth: 1.5,
                    pointRadius: 0,
                    fill: false
                },
                {
                    label: 'CL (' + controlChartData.cl + ')',
                    data: controlChartData.cl_line,
                    borderColor: '#10b981',
                    borderWidth: 1.5,
                    pointRadius: 0,
                    fill: false
                },
                {
                    label: 'LCL (' + controlChartData.lcl + ')',
                    data: controlChartData.lcl_line,
                    borderColor: '#ef4444',
                    borderDash: [5, 5],
                    borderWidth: 1.5,
                    pointRadius: 0,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { labels: { boxWidth: 10, font: { size: 10 } } }
            },
            scales: {
                x: { ticks: { font: { size: 9 }, maxRotation: 60 } },
                y: { beginAtZero: true, title: { display: true, text: 'Jumlah Kasus Cacat (c)', font: { size: 10 } } }
            }
        }
    });
}

// 5. Scatter Chart
const scatterCanvas = document.getElementById('scatterChart');
if (scatterCanvas && scatterData.points) {
    new Chart(scatterCanvas, {
        type: 'scatter',
        data: {
            datasets: [{
                label: 'Kasus Komplain',
                data: scatterData.points,
                backgroundColor: '#6366f1',
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            const p = ctx.raw;
                            return (p.label || '') + ': Qty=' + p.x + ' pcs, Lead Time=' + p.y + ' hari';
                        }
                    }
                }
            },
            scales: {
                x: { title: { display: true, text: scatterData.x_title || 'Qty (pcs)', font: { size: 10 } } },
                y: { title: { display: true, text: scatterData.y_title || 'Lead Time (hari)', font: { size: 10 } }, beginAtZero: true }
            }
        }
    });
}

// 7. Stratifikasi (Area)
const stratCanvas = document.getElementById('stratChart');
if (stratCanvas && stratData.area) {
    new Chart(stratCanvas, {
        type: 'bar',
        data: {
            labels: stratData.area.labels,
            datasets: [{
                label: 'Jumlah per Area',
                data: stratData.area.values,
                backgroundColor: PALET.slice(0, stratData.area.labels.length),
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true, title: { display: true, text: 'Jumlah Kasus', font: { size: 10 } } }
            }
        }
    });
}
</script>
@endpush
@endsection
