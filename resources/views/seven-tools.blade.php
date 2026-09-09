@extends('layouts.app')
@section('title', 'Seven Tools Quality Control')
@section('subtitle', 'Analisis pengendalian kualitas terpadu dengan 7 alat bantu statistik mutu (QC 7 Tools)')
@section('hide_create_btn', true)

@section('content')
@php
    function tool_header($no, $title, $desc) {
        return "<div class='flex items-start gap-3 mb-4'>
            <span class='shrink-0 w-8 h-8 rounded-lg bg-sky-600 text-white flex items-center justify-center font-bold text-sm shadow-sm'>{$no}</span>
            <div><h2 class='font-bold text-slate-800 text-base'>{$title}</h2><p class='text-xs text-slate-500'>{$desc}</p></div>
        </div>";
    }
@endphp

<div class="space-y-8">

    <!-- 1. CHECK SHEET -->
    <section class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        {!! tool_header(1, 'Check Sheet (Lembar Pemeriksaan Mutu)', 'Tabulasi frekuensi kemunculan ketidaksesuaian per bulan untuk rekapitulasi data lapangan.') !!}

        {{-- Mode CheckSheet --}}
        <form method="GET" action="{{ url()->current() }}" class="flex flex-wrap items-center gap-2 mb-4 text-xs">
            {{-- Preserve all other filter params --}}
            @foreach (['pareto_year','pareto_month','pareto_detail_year','pareto_detail_month','pareto_cust_year','pareto_cust_month','pareto_cause_year','pareto_cause_month'] as $p)
                @if (request($p))<input type="hidden" name="{{ $p }}" value="{{ request($p) }}">@endif
            @endforeach

            <span class="text-slate-500 font-medium">Tabulasi Berdasarkan:</span>
            <select name="cs_mode" onchange="this.form.submit()"
                    class="rounded-xl border border-slate-300 px-3 py-1.5 text-xs focus:ring-2 focus:ring-sky-500 focus:border-sky-500 font-semibold bg-slate-50">
                <option value="jenis_ketidaksesuaian" @selected($csMode === 'jenis_ketidaksesuaian')>Jenis Ketidaksesuaian</option>
                <option value="detail_ketidaksesuaian" @selected($csMode === 'detail_ketidaksesuaian')>Detail Ketidaksesuaian</option>
            </select>
        </form>

        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="w-full text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-800 text-white font-bold">
                        <th class="border border-slate-700 px-3 py-2.5 text-left sticky left-0 bg-slate-800">
                            {{ $csMode === 'detail_ketidaksesuaian' ? 'Detail Ketidaksesuaian' : 'Jenis Ketidaksesuaian' }}
                        </th>
                        @foreach ($checkSheet['bulan'] as $b)
                            <th class="border border-slate-700 px-3 py-2.5 whitespace-nowrap text-center">{{ $b }}</th>
                        @endforeach
                        <th class="border border-slate-700 px-3 py-2.5 bg-sky-900 text-center">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($checkSheet['rows'] as $row)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="border border-slate-200 px-3 py-2 font-medium text-slate-700 sticky left-0 bg-white">{{ $row['kategori'] }}</td>
                            @foreach ($row['per_bulan'] as $v)
                                <td class="border border-slate-200 px-2 py-2 text-center {{ $v ? 'text-slate-800 font-semibold' : 'text-slate-300' }}">{{ $v ?: '·' }}</td>
                            @endforeach
                            <td class="border border-slate-200 px-3 py-2 text-center font-bold bg-slate-50 text-sky-800">{{ $row['total'] }}</td>
                        </tr>
                    @endforeach
                    <tr class="bg-slate-100 font-bold text-slate-900">
                        <td class="border border-slate-200 px-3 py-2.5 sticky left-0 bg-slate-100 uppercase tracking-wider">TOTAL KESELURUHAN</td>
                        @foreach ($checkSheet['total_per_bulan'] as $v)
                            <td class="border border-slate-200 px-2 py-2.5 text-center font-mono text-xs">{{ $v }}</td>
                        @endforeach
                        <td class="border border-slate-200 px-3 py-2.5 text-center bg-sky-100 text-sky-900 font-mono text-sm">{{ $checkSheet['grand_total'] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- 2. DIAGRAM PARETO (4 VARIAN) -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-sky-600 text-white flex items-center justify-center font-bold text-sm shadow-sm">2</span>
                    Diagram Pareto (Prinsip 80/20 Prioritas Masalah)
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Identifikasi 20% penyebab utama yang menimbulkan 80% total masalah mutu.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- 2a. PARETO JENIS -->
            <section id="paretoCard" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 scroll-mt-6">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-bold text-slate-800 text-sm">2a. Pareto — Jenis Ketidaksesuaian</h4>
                </div>
                @include('partials._pareto_filter')
                <div class="relative h-72 mt-2">
                    @if (empty($pareto['labels']))
                        <div class="absolute inset-0 flex items-center justify-center text-sm text-slate-400">Tidak ada data pada periode ini.</div>
                    @else
                        <canvas id="paretoChart"></canvas>
                    @endif
                </div>
            </section>

            <!-- 2b. PARETO DETAIL -->
            <section id="paretoDetailCard" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 scroll-mt-6">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-bold text-slate-800 text-sm">2b. Pareto — Detail Ketidaksesuaian</h4>
                </div>
                @include('partials._pareto_detail_filter')
                <div class="relative h-72 mt-2">
                    @if (empty($paretoDetail['labels']))
                        <div class="absolute inset-0 flex items-center justify-center text-sm text-slate-400">Tidak ada data pada periode ini.</div>
                    @else
                        <canvas id="paretoDetailChart"></canvas>
                    @endif
                </div>
            </section>

            <!-- 2c. PARETO CUSTOMER -->
            <section id="paretoCustCard" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 scroll-mt-6">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-bold text-slate-800 text-sm">2c. Pareto — Frekuensi Customer</h4>
                </div>
                @include('partials._pareto_cust_filter')
                <div class="relative h-72 mt-2">
                    @if (empty($paretoCust['labels']))
                        <div class="absolute inset-0 flex items-center justify-center text-sm text-slate-400">Tidak ada data pada periode ini.</div>
                    @else
                        <canvas id="paretoCustChart"></canvas>
                    @endif
                </div>
            </section>

            <!-- 2d. PARETO PENYEBAB -->
            <section id="paretoCauseCard" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 scroll-mt-6">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-bold text-slate-800 text-sm">2d. Pareto — Akar Penyebab Dominan</h4>
                </div>
                @include('partials._pareto_cause_filter')
                <div class="relative h-72 mt-2">
                    @if (empty($paretoCause['labels']))
                        <div class="absolute inset-0 flex items-center justify-center text-sm text-slate-400">Tidak ada data pada periode ini.</div>
                    @else
                        <canvas id="paretoCauseChart"></canvas>
                    @endif
                </div>
            </section>
        </div>
    </div>


    <!-- 3. FISHBONE / ISHIKAWA (6M) -->
    <section id="fishbone-section" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-6">
        {!! tool_header(3, 'Diagram Sebab-Akibat (Fishbone / Ishikawa 6M)', 'Analisis struktur akar masalah penyebab komplain berdasarkan kerangka 6M. Klik cabang penyebab untuk penelusuran transaksi komplain.') !!}

        <!-- Control Filter Bar (Clean & Spacious) -->
        <form method="GET" action="{{ route('seven-tools') }}#fishbone-section" class="bg-slate-50/80 p-4 rounded-2xl border border-slate-200 flex flex-wrap items-center justify-between gap-4 shadow-sm">
            @if(request('cs_mode')) <input type="hidden" name="cs_mode" value="{{ request('cs_mode') }}"> @endif
            @if(request('pareto_year')) <input type="hidden" name="pareto_year" value="{{ request('pareto_year') }}"> @endif
            @if(request('pareto_month')) <input type="hidden" name="pareto_month" value="{{ request('pareto_month') }}"> @endif
            @if(request('pareto_detail_year')) <input type="hidden" name="pareto_detail_year" value="{{ request('pareto_detail_year') }}"> @endif
            @if(request('pareto_detail_month')) <input type="hidden" name="pareto_detail_month" value="{{ request('pareto_detail_month') }}"> @endif
            @if(request('pareto_cust_year')) <input type="hidden" name="pareto_cust_year" value="{{ request('pareto_cust_year') }}"> @endif
            @if(request('pareto_cust_month')) <input type="hidden" name="pareto_cust_month" value="{{ request('pareto_cust_month') }}"> @endif
            @if(request('pareto_cause_year')) <input type="hidden" name="pareto_cause_year" value="{{ request('pareto_cause_year') }}"> @endif
            @if(request('pareto_cause_month')) <input type="hidden" name="pareto_cause_month" value="{{ request('pareto_cause_month') }}"> @endif

            <!-- Left Controls: Dropdown Fokus Akibat -->
            <div class="flex flex-wrap items-center gap-2">
                <label for="fishbone_efek" class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5 whitespace-nowrap">
                    <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.586V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.414L3.293 6.707A1 1 0 013 6V4z"/></svg>
                    Fokus Akibat:
                </label>
                <select name="fishbone_efek" id="fishbone_efek" onchange="this.form.submit()" class="bg-white text-slate-800 text-xs font-semibold px-3 py-2 rounded-xl border border-slate-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 cursor-pointer min-w-[280px]">
                    <option value="AUTO" {{ ($fishbone['selected_efek'] ?? 'AUTO') === 'AUTO' ? 'selected' : '' }}>
                        ⚡ Auto Pareto (Kasus Tertinggi: {{ $fishbone['top_efek'] ?? '-' }})
                    </option>
                    <option value="ALL" {{ ($fishbone['selected_efek'] ?? '') === 'ALL' ? 'selected' : '' }}>
                        🌐 Semua Masalah / Global
                    </option>

                    @if(!empty($fishbone['available_jenis']))
                        <optgroup label="📌 Jenis Ketidaksesuaian">
                            @foreach ($fishbone['available_jenis'] as $jOpt)
                                <option value="{{ $jOpt }}" {{ ($fishbone['selected_efek'] ?? '') === $jOpt ? 'selected' : '' }}>
                                    🏷️ {{ $jOpt }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endif

                    @if(!empty($fishbone['available_detail']))
                        <optgroup label="🔍 Detail Ketidaksesuaian (Cacat Spesifik)">
                            @foreach ($fishbone['available_detail'] as $dOpt)
                                <option value="{{ $dOpt }}" {{ ($fishbone['selected_efek'] ?? '') === $dOpt ? 'selected' : '' }}>
                                    🔍 {{ $dOpt }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endif
                </select>
            </div>

            <!-- Right Controls: Level Penyebab (Detail Spesifik vs Kategori Umum) -->
            <div class="inline-flex rounded-xl border border-slate-300 bg-white p-1 shadow-sm">
                <button type="submit" name="fishbone_level" value="detail" class="px-3.5 py-1.5 text-xs font-bold rounded-lg transition {{ ($fishbone['level'] ?? 'detail') === 'detail' ? 'bg-sky-500 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                    🔍 Detail Spesifik
                </button>
                <button type="submit" name="fishbone_level" value="general" class="px-3.5 py-1.5 text-xs font-bold rounded-lg transition {{ ($fishbone['level'] ?? '') === 'general' ? 'bg-sky-500 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                    📊 Kategori Umum
                </button>
            </div>
        </form>

        <!-- DIAGRAM TULANG IKAN (SKELETON DARK BLUE GRADIENT) -->
        <div class="space-y-4">
            <div class="overflow-x-auto pb-4">
                <div class="min-w-[960px] bg-slate-950 text-white rounded-3xl p-8 relative border border-slate-800 shadow-2xl overflow-hidden">
                    <!-- Subtle Glow Effect -->
                    <div class="absolute -right-16 -top-16 w-96 h-96 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>

                    <!-- Diagram Top Header Bar -->
                    <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-800/80">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-sky-400 animate-pulse"></span>
                            <h4 class="text-xs font-mono font-bold uppercase tracking-widest text-sky-400">DIAGRAM SEBAB-AKIBAT ISHIKAWA 6M</h4>
                        </div>
                        <div class="text-xs font-semibold text-sky-200 bg-sky-950/90 border border-sky-800/70 px-4 py-1.5 rounded-full font-mono shadow-inner">
                            Fokus Akibat: <strong class="text-white">{{ $fishbone['efek'] }}</strong>
                        </div>
                    </div>

                    <!-- FISHBONE SKELETON LAYOUT -->
                    <div class="grid grid-cols-12 gap-6 items-center relative py-4">
                        
                        <!-- LEFT BODY: 6M BRANCH CARDS IN DARK BLUE GRADIENT (9 COLS) -->
                        <div class="col-span-9 space-y-6">
                            
                            <!-- UPPER BRANCHES (MAN, MATERIAL, ENVIRONMENT) -->
                            <div class="grid grid-cols-3 gap-5">
                                @foreach (['Man', 'Material', 'Environment'] as $catKey)
                                    @php $cData = collect($fishbone['categories'])->firstWhere('kategori', $catKey); @endphp
                                    <div class="bg-gradient-to-br from-slate-900 via-slate-850 to-sky-950 border border-sky-500/30 rounded-2xl p-4 shadow-lg text-white flex flex-col justify-between hover:border-sky-400/60 transition-all">
                                        <div>
                                            <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-800">
                                                <span class="font-bold text-xs text-sky-400 uppercase tracking-wider font-mono flex items-center gap-1.5">
                                                    <span class="w-2 h-2 rounded-full bg-sky-400"></span> {{ $catKey }}
                                                </span>
                                                <span class="text-[10px] bg-sky-950 text-sky-300 font-mono px-2 py-0.5 rounded-full border border-sky-800 font-bold">
                                                    {{ $cData['total'] ?? 0 }} isu
                                                </span>
                                            </div>
                                            <ul class="space-y-2">
                                                @forelse ($cData['top_causes'] ?? [] as $c)
                                                    <li>
                                                        <button type="button" onclick='showFishboneModal(@json($c), "{{ $catKey }}")' class="w-full text-left p-2.5 rounded-xl text-xs flex items-center justify-between transition group {{ $loop->first ? 'bg-sky-500/20 border-2 border-sky-400 text-white font-bold shadow-md hover:bg-sky-500/30' : 'bg-slate-800/90 border border-slate-700/80 text-slate-200 hover:bg-slate-700/90' }}">
                                                            <span class="whitespace-normal break-words text-left leading-snug flex-1 pr-2 flex items-center gap-1.5">
                                                                @if($loop->first) <span class="text-[11px] text-amber-400 shrink-0" title="Penyebab Utama Dominan">🔥</span> @endif
                                                                <span>{{ $c['nama'] }}</span>
                                                            </span>
                                                            <span class="text-[11px] font-bold text-sky-300 bg-slate-900 px-2 py-0.5 rounded-md font-mono shrink-0 border border-sky-800/70">{{ $c['jumlah'] }}</span>
                                                        </button>
                                                    </li>
                                                @empty
                                                    <li class="text-[11px] text-slate-500 italic text-center py-2">Tidak ada isu</li>
                                                @endforelse
                                            </ul>
                                        </div>

                                        @if(($cData['other_count'] ?? 0) > 0)
                                            <div class="mt-3 pt-2 border-t border-slate-800 text-center">
                                                <button type="button" onclick='showFishboneCategoryAllModal(@json($cData), "{{ $catKey }}")' class="text-[10px] font-bold text-sky-400 hover:text-sky-300 hover:underline">
                                                    + {{ $cData['other_count'] }} penyebab minor lainnya
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <!-- CENTRAL HORIZONTAL SPINE LINE (PANAH TULANG UTAMA) -->
                            <div class="relative py-2 flex items-center">
                                <div class="w-full h-2 bg-gradient-to-r from-slate-700 via-sky-500 to-sky-400 rounded-full shadow-lg shadow-sky-500/20"></div>
                                <div class="text-sky-400 -ml-1">
                                    <svg class="w-7 h-7 fill-current" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                            </div>

                            <!-- LOWER BRANCHES (MACHINE, METHOD, MEASUREMENT) -->
                            <div class="grid grid-cols-3 gap-5">
                                @foreach (['Machine', 'Method', 'Measurement'] as $catKey)
                                    @php $cData = collect($fishbone['categories'])->firstWhere('kategori', $catKey); @endphp
                                    <div class="bg-gradient-to-br from-slate-900 via-slate-850 to-indigo-950 border border-indigo-500/30 rounded-2xl p-4 shadow-lg text-white flex flex-col justify-between hover:border-indigo-400/60 transition-all">
                                        <div>
                                            <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-800">
                                                <span class="font-bold text-xs text-indigo-400 uppercase tracking-wider font-mono flex items-center gap-1.5">
                                                    <span class="w-2 h-2 rounded-full bg-indigo-400"></span> {{ $catKey }}
                                                </span>
                                                <span class="text-[10px] bg-indigo-950 text-indigo-300 font-mono px-2 py-0.5 rounded-full border border-indigo-800 font-bold">
                                                    {{ $cData['total'] ?? 0 }} isu
                                                </span>
                                            </div>
                                            <ul class="space-y-2">
                                                @forelse ($cData['top_causes'] ?? [] as $c)
                                                    <li>
                                                        <button type="button" onclick='showFishboneModal(@json($c), "{{ $catKey }}")' class="w-full text-left p-2.5 rounded-xl text-xs flex items-center justify-between transition group {{ $loop->first ? 'bg-indigo-500/20 border-2 border-indigo-400 text-white font-bold shadow-md hover:bg-indigo-500/30' : 'bg-slate-800/90 border border-slate-700/80 text-slate-200 hover:bg-slate-700/90' }}">
                                                            <span class="whitespace-normal break-words text-left leading-snug flex-1 pr-2 flex items-center gap-1.5">
                                                                @if($loop->first) <span class="text-[11px] text-amber-400 shrink-0" title="Penyebab Utama Dominan">🔥</span> @endif
                                                                <span>{{ $c['nama'] }}</span>
                                                            </span>
                                                            <span class="text-[11px] font-bold text-indigo-300 bg-slate-900 px-2 py-0.5 rounded-md font-mono shrink-0 border border-indigo-800/70">{{ $c['jumlah'] }}</span>
                                                        </button>
                                                    </li>
                                                @empty
                                                    <li class="text-[11px] text-slate-500 italic text-center py-2">Tidak ada isu</li>
                                                @endforelse
                                            </ul>
                                        </div>

                                        @if(($cData['other_count'] ?? 0) > 0)
                                            <div class="mt-3 pt-2 border-t border-slate-800 text-center">
                                                <button type="button" onclick='showFishboneCategoryAllModal(@json($cData), "{{ $catKey }}")' class="text-[10px] font-bold text-indigo-400 hover:text-indigo-300 hover:underline">
                                                    + {{ $cData['other_count'] }} penyebab minor lainnya
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                        </div>

                        <!-- RIGHT HEAD: FISH HEAD CARD (3 COLS) -->
                        <div class="col-span-3 pl-2">
                            <div class="bg-gradient-to-b from-slate-900 via-slate-900 to-sky-950 text-white p-6 rounded-3xl shadow-2xl text-center border-2 border-sky-400 relative overflow-hidden group">
                                <div class="text-[10px] font-mono tracking-widest text-sky-400 font-bold uppercase mb-1">AKIBAT (PROBLEM HEAD)</div>
                                <div class="text-base font-black text-white leading-snug drop-shadow-sm break-words my-2">
                                    {{ $fishbone['efek'] }}
                                </div>
                                <div class="mt-4 inline-block bg-sky-950 text-sky-300 border border-sky-800 text-[11px] font-bold font-mono px-3 py-1 rounded-full shadow-inner">
                                    Total 6M Isu: {{ array_sum(array_column($fishbone['categories'], 'total')) }}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MODAL LAPORAN PENELUSURAN MASALAH SPESIFIK -->
    <div id="fishboneDetailModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full border border-slate-200 overflow-hidden transform transition-all my-8">
            <!-- Modal Header -->
            <div class="bg-slate-900 text-white px-6 py-4 flex items-center justify-between border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <span id="modalCategoryBadge" class="bg-sky-500 text-white text-xs font-mono font-bold px-3 py-1 rounded-full uppercase">MACHINE</span>
                    <div>
                        <h3 class="font-bold text-base text-white flex items-center gap-2">
                            Laporan Penelusuran Masalah Spesifik
                        </h3>
                        <p class="text-xs text-sky-300">Penyebab: <strong id="modalCauseTitle" class="text-white">MC Tidak Stabil</strong></p>
                    </div>
                </div>
                <button type="button" onclick="closeFishboneModal()" class="text-slate-400 hover:text-white transition p-1 rounded-lg hover:bg-slate-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Modal Content (Table of Specific Complaints & Actions) -->
            <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                <div class="flex items-center justify-between text-xs text-slate-500 pb-2 border-b border-slate-100">
                    <span>Daftar Transaksi Komplain & Tindakan Perbaikan Lengkap</span>
                    <span id="modalTotalCount" class="font-bold text-slate-800 font-mono">Total 0 Komplain</span>
                </div>

                <div class="overflow-x-auto border border-slate-200 rounded-xl shadow-sm">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-slate-50 text-slate-700 uppercase font-mono font-bold text-[11px] border-b border-slate-200">
                            <tr>
                                <th class="px-3 py-3">No. NCR & Customer</th>
                                <th class="px-3 py-3">Tanggal & Area</th>
                                <th class="px-3 py-3">Cacat Spesifik</th>
                                <th class="px-3 py-3 text-right">Qty</th>
                                <th class="px-3 py-3">Tindakan Perbaikan (Corrective & Preventive Action)</th>
                            </tr>
                        </thead>
                        <tbody id="modalTableBody" class="divide-y divide-slate-100 text-slate-700">
                            <!-- Populated dynamically via JS -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="bg-slate-50 px-6 py-3 border-t border-slate-200 flex items-center justify-end">
                <button type="button" onclick="closeFishboneModal()" class="px-4 py-2 bg-slate-800 text-white text-xs font-bold rounded-xl hover:bg-slate-900 transition">
                    Tutup Laporan
                </button>
            </div>
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
        tbody.innerHTML = `<tr><td colspan="5" class="px-4 py-6 text-center text-slate-400 italic">Tidak ada rincian data komplain untuk penyebab ini.</td></tr>`;
    } else {
        tbody.innerHTML = complaints.map(c => `
            <tr class="hover:bg-sky-50/50 transition">
                <td class="px-3 py-3 font-medium">
                    <div class="font-bold text-slate-900">${c.no_customer || '-'}</div>
                    <div class="text-sky-600 font-semibold">${c.nama_customer || '-'}</div>
                </td>
                <td class="px-3 py-3">
                    <div>📅 ${c.tanggal_complain || '-'}</div>
                    <div class="text-[11px] text-slate-500 font-mono">📍 ${c.area || '-'}</div>
                </td>
                <td class="px-3 py-3">
                    <span class="inline-block bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded font-mono">${c.jenis_ketidaksesuaian || '-'}</span>
                    <div class="text-slate-600 mt-1 font-medium">${c.detail_ketidaksesuaian || '-'}</div>
                </td>
                <td class="px-3 py-3 text-right font-mono font-bold text-slate-900">
                    ${c.qty ? c.qty.toLocaleString() : '-'}
                </td>
                <td class="px-3 py-3 space-y-1 text-[11px]">
                    <div class="bg-emerald-50 text-emerald-900 p-2 rounded border border-emerald-200/60">
                        <strong class="text-emerald-700">Corrective:</strong> ${c.corrective_action}
                    </div>
                    <div class="bg-blue-50 text-blue-900 p-2 rounded border border-blue-200/60">
                        <strong class="text-blue-700">Preventive:</strong> ${c.preventive_action}
                    </div>
                </td>
            </tr>
        `).join('');
    }

    modal.classList.remove('hidden');
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
    if (modal) modal.classList.add('hidden');
}

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
        data: { labels: pareto.labels, datasets: [
            { type:'bar', label:'Jumlah Kasus', data: pareto.values, backgroundColor:'#0ea5e9', yAxisID:'y', order:2, borderRadius: 6 },
            { type:'line', label:'Kumulatif %', data: pareto.cumulative, borderColor:'#ef4444', backgroundColor:'#ef4444', yAxisID:'y1', tension:.3, order:1, pointRadius: 3 }
        ]},
        options: { responsive:true, maintainAspectRatio:false,
            plugins:{ legend:{ labels:{ boxWidth:12, font:{size:11} } } },
            scales:{ x:{ ticks:{ font:{size:9}, maxRotation:60, minRotation:45 } },
                y:{ beginAtZero:true }, y1:{ beginAtZero:true, max:100, position:'right', grid:{drawOnChartArea:false} } } }
    });
}

// 2b. Pareto Detail
const paretoDetailCanvas = document.getElementById('paretoDetailChart');
if (paretoDetailCanvas && paretoDetail.labels && paretoDetail.labels.length) {
    new Chart(paretoDetailCanvas, {
        data: { labels: paretoDetail.labels, datasets: [
            { type:'bar', label:'Jumlah', data: paretoDetail.values, backgroundColor:'#f59e0b', yAxisID:'y', order:2, borderRadius: 6 },
            { type:'line', label:'Kumulatif %', data: paretoDetail.cumulative, borderColor:'#ef4444', backgroundColor:'#ef4444', yAxisID:'y1', tension:.3, order:1, pointRadius: 3 }
        ]},
        options: { responsive:true, maintainAspectRatio:false,
            plugins:{ legend:{ labels:{ boxWidth:12, font:{size:11} } } },
            scales:{ x:{ ticks:{ font:{size:9}, maxRotation:60, minRotation:45 } },
                y:{ beginAtZero:true }, y1:{ beginAtZero:true, max:100, position:'right', grid:{drawOnChartArea:false} } } }
    });
}

// 2c. Pareto Customer
const paretoCustCanvas = document.getElementById('paretoCustChart');
if (paretoCustCanvas && paretoCust.labels && paretoCust.labels.length) {
    new Chart(paretoCustCanvas, {
        data: { labels: paretoCust.labels, datasets: [
            { type:'bar', label:'Jumlah Complaint', data: paretoCust.values, backgroundColor:'#6366f1', yAxisID:'y', order:2, borderRadius: 6 },
            { type:'line', label:'Kumulatif %', data: paretoCust.cumulative, borderColor:'#ef4444', backgroundColor:'#ef4444', yAxisID:'y1', tension:.3, order:1, pointRadius: 3 }
        ]},
        options: { responsive:true, maintainAspectRatio:false,
            plugins:{ legend:{ labels:{ boxWidth:12, font:{size:11} } } },
            scales:{ x:{ ticks:{ font:{size:9}, maxRotation:60, minRotation:45 } },
                y:{ beginAtZero:true }, y1:{ beginAtZero:true, max:100, position:'right', grid:{drawOnChartArea:false} } } }
    });
}

// 2d. Pareto Penyebab Masalah
const paretoCauseCanvas = document.getElementById('paretoCauseChart');
if (paretoCauseCanvas && paretoCause.labels && paretoCause.labels.length) {
    new Chart(paretoCauseCanvas, {
        data: { labels: paretoCause.labels, datasets: [
            { type:'bar', label:'Jumlah', data: paretoCause.values, backgroundColor:'#10b981', yAxisID:'y', order:2, borderRadius: 6 },
            { type:'line', label:'Kumulatif %', data: paretoCause.cumulative, borderColor:'#ef4444', backgroundColor:'#ef4444', yAxisID:'y1', tension:.3, order:1, pointRadius: 3 }
        ]},
        options: { responsive:true, maintainAspectRatio:false,
            plugins:{ legend:{ labels:{ boxWidth:12, font:{size:11} } } },
            scales:{ x:{ ticks:{ font:{size:9}, maxRotation:60, minRotation:45 } },
                y:{ beginAtZero:true }, y1:{ beginAtZero:true, max:100, position:'right', grid:{drawOnChartArea:false} } } }
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
