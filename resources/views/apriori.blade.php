@extends('layouts.app')
@section('title', 'Analisis Pola Hubungan (Apriori)')
@section('subtitle', 'Menemukan hubungan antara jenis cacat produk dan penyebab utamanya dalam bahasa yang mudah dipahami')
@section('hide_create_btn', true)

@section('content')

{{-- Info Banner Awam --}}
<div class="mb-6 bg-gradient-to-r from-sky-600 to-indigo-700 text-white rounded-2xl p-5 shadow-sm">
    <div class="flex items-start gap-4">
        <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center text-xl shrink-0">💡</div>
        <div>
            <h2 class="text-base font-bold">Panduan Analisis Pola Hubungan (Apriori)</h2>
            <p class="text-xs text-sky-100 mt-1 leading-relaxed">
                Halaman ini secara otomatis menemukan <b>sebab-akibat</b> antara jenis cacat barang dan faktor penyebabnya dari seluruh data komplain.
                Staff QA &amp; Supervisor tidak perlu menghitung angka statistik — cukup perhatikan <b>panah hubungan (➔)</b> dan <b>tingkat kekuatan</b> (Sangat Kuat / Kuat).
            </p>
        </div>
    </div>
</div>

<!-- Form Filter & Parameter -->
<form method="GET" class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 mb-6">
    <div class="flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1 uppercase tracking-wide">Filter Tahun</label>
            <select name="tahun" class="w-32 rounded-lg border-slate-300 text-sm focus:ring-sky-500 focus:border-sky-500 border px-3 py-2">
                <option value="">Semua Tahun</option>
                @foreach ($years as $y)
                    <option value="{{ $y }}" @selected(request()->integer('tahun') === (int) $y)>Tahun {{ $y }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1 uppercase tracking-wide">Filter Bulan</label>
            <select name="bulan" class="w-36 rounded-lg border-slate-300 text-sm focus:ring-sky-500 focus:border-sky-500 border px-3 py-2">
                <option value="">Semua Bulan</option>
                @php
                    $namaBulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                                 7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                @endphp
                @foreach ($namaBulan as $n => $label)
                    <option value="{{ $n }}" @selected(request()->integer('bulan') === $n)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1 uppercase tracking-wide">Min. Kemungkinan (Confidence %)</label>
            <input type="number" name="min_confidence" value="{{ $minConfidence }}" step="5" min="10" max="100"
                   class="w-32 rounded-lg border-slate-300 text-sm focus:ring-sky-500 focus:border-sky-500 border px-3 py-2">
        </div>
        <input type="hidden" name="min_support" value="{{ $minSupport }}">
        <button class="bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold px-5 py-2 rounded-lg shadow-sm">Tampilkan Pola</button>
        @if (request()->hasAny(['tahun','bulan','min_confidence']))
            <a href="{{ url()->current() }}" class="text-sm text-slate-500 hover:text-slate-700 py-2">Reset Filter</a>
        @endif
        <div class="ml-auto text-xs text-slate-500 py-2">
            Data teranalisis: <b class="text-slate-800 font-bold text-sm">{{ $totalTrx }}</b> komplain
        </div>
    </div>
</form>

{{-- Mode Switcher (Kartu Awam vs Tabel Teknis) --}}
<div class="flex items-center justify-between mb-4" x-data="{ mode: 'cards' }">
    <div class="flex items-center gap-2">
        <h2 class="text-base font-bold text-slate-800">Daftar Pola Hubungan Terdeteksi</h2>
        <span class="text-xs bg-sky-100 text-sky-700 font-bold px-2.5 py-0.5 rounded-full">{{ count($rules) }} Pola</span>
    </div>
    <div class="flex items-center bg-slate-200 p-1 rounded-xl text-xs font-medium">
        <button type="button" @click="mode = 'cards'" id="btnModeCards" onclick="toggleAprioriView('cards')"
                class="px-3 py-1.5 rounded-lg bg-white shadow-sm text-slate-800 font-bold transition">
            📱 Tampilan Kartu
        </button>
        <button type="button" @click="mode = 'table'" id="btnModeTable" onclick="toggleAprioriView('table')"
                class="px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-800 transition">
            📊 Tabel Data 
        </button>
    </div>
</div>

{{-- SECTION 1: TAMPILAN KARTU EXECUTIVE & SEGAR --}}
<div id="aprioriCardsView" class="space-y-6 mb-8">
    @if (!empty($rules))
        {{-- TOP 3 HIGHLIGHT KRITIS BANNER (APRIORI HERO INSIGHTS) --}}
        @php
            $topThreeRules = array_slice($rules, 0, 3);
        @endphp
        <div class="bg-gradient-to-br from-slate-950 via-slate-900 to-sky-950 text-white rounded-3xl p-6 shadow-2xl border border-sky-500/30 relative overflow-hidden">
            <!-- Subtle Ambient Background Glow -->
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="flex items-center justify-between mb-5 pb-3 border-b border-slate-800">
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-amber-400 animate-pulse"></span>
                    <h3 class="text-xs font-mono font-bold uppercase tracking-widest text-sky-400">🔥 TOP {{ count($topThreeRules) }} POLA HUBUNGAN PALING KRITIS</h3>
                </div>
                <span class="text-[11px] font-mono text-slate-400">Berdasarkan {{ $totalTrx }} Data Komplain</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach ($topThreeRules as $hIdx => $hRule)
                    @php
                        $hPct = round($hRule['confidence'] * 100);
                        $hIsSangatKuat = $hRule['kekuatan'] === 'Sangat Kuat';
                    @endphp
                    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 flex flex-col justify-between hover:border-sky-500/50 transition-all group shadow-lg">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-[10px] font-bold font-mono px-2 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700">Pola Kritis #{{ $hIdx + 1 }}</span>
                                <span class="text-xs font-black font-mono px-2.5 py-1 rounded-full {{ $hIsSangatKuat ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40' : 'bg-amber-500/20 text-amber-300 border border-amber-500/40' }}">
                                    ⚡ {{ $hPct }}% Akurasi
                                </span>
                            </div>

                            <div class="space-y-2 my-2">
                                <div class="bg-slate-950/70 p-2.5 rounded-xl border border-slate-800">
                                    <div class="text-[9px] uppercase font-mono font-bold text-sky-400">Kondisi Cacat:</div>
                                    <div class="text-xs font-bold text-white leading-tight mt-0.5">{{ $hRule['antecedents'] }}</div>
                                </div>
                                <div class="flex items-center justify-center text-sky-400 text-xs font-mono font-bold my-1 gap-1">
                                    <svg class="w-4 h-4 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                    <span class="text-[10px] text-slate-400">Penyebab Dominan</span>
                                </div>
                                <div class="bg-amber-950/40 p-2.5 rounded-xl border border-amber-900/50">
                                    <div class="text-[9px] uppercase font-mono font-bold text-amber-400">Akar Masalah:</div>
                                    <div class="text-xs font-bold text-amber-200 leading-tight mt-0.5">{{ $hRule['consequents'] }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 pt-2.5 border-t border-slate-800/80 text-[11px] text-slate-300">
                            <strong class="text-sky-300">Saran Preventif:</strong> Perbaiki faktor <span class="text-amber-300 font-semibold">{{ $hRule['consequents'] }}</span>.
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- DAFTAR SELURUH POLA (COLOR-CODED BY KEKUATAN) --}}
    @forelse ($rules as $idx => $r)
        @php
            $isSangatKuat = $r['kekuatan'] === 'Sangat Kuat';
            $isKuat       = $r['kekuatan'] === 'Kuat';
            $isSedang     = $r['kekuatan'] === 'Sedang';

            // COLOR CODING BERDASARKAN TINGKAT KEKUATAN POLA:
            // 🔴 Sangat Kuat (Tinggi/Risiko Kritis) -> Rose / Merah
            // 🟡 Kuat (Sedang/Perlu Waspada)        -> Amber / Kuning
            // 🟢 Sedang/Rendah (Aman/Rendah)         -> Emerald / Hijau

            $cardBg = $isSangatKuat 
                ? 'bg-gradient-to-br from-rose-50/80 via-white to-rose-50/30 border-2 border-rose-300/90 hover:border-rose-400 shadow-sm hover:shadow-md' 
                : ($isKuat 
                    ? 'bg-gradient-to-br from-amber-50/80 via-white to-amber-50/30 border-2 border-amber-300/90 hover:border-amber-400 shadow-sm hover:shadow-md' 
                    : 'bg-gradient-to-br from-emerald-50/80 via-white to-emerald-50/30 border-2 border-emerald-300/90 hover:border-emerald-400 shadow-sm hover:shadow-md');

            $badgeBg = $isSangatKuat 
                ? 'bg-rose-600 text-white border-rose-700' 
                : ($isKuat 
                    ? 'bg-amber-500 text-white border-amber-600' 
                    : 'bg-emerald-600 text-white border-emerald-700');

            $pctBadgeBg = $isSangatKuat 
                ? 'bg-rose-100 text-rose-800 border-rose-300' 
                : ($isKuat 
                    ? 'bg-amber-100 text-amber-900 border-amber-300' 
                    : 'bg-emerald-100 text-emerald-900 border-emerald-300');

            $icon = $isSangatKuat ? '🚨' : ($isKuat ? '⚠️' : '✅');
            $pct  = round($r['confidence'] * 100);
        @endphp
        <div x-data="{ showDetail: false }" class="rounded-2xl p-5 transition-all group {{ $cardBg }}">
            {{-- Header Bar Kartu --}}
            <div class="flex flex-wrap items-center justify-between gap-3 mb-4 pb-3 border-b border-slate-200/70">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold font-mono px-3 py-1 rounded-xl bg-slate-900 text-white shadow-xs">Pola #{{ $idx + 1 }}</span>
                    <span class="text-xs font-bold px-3 py-1 rounded-full border {{ $badgeBg }} flex items-center gap-1.5 font-mono shadow-2xs">
                        <span>{{ $icon }}</span> Hubungan {{ $r['kekuatan'] }}
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-600 font-semibold">Tingkat Kepastian (Confidence):</span>
                    <span class="text-sm font-black font-mono px-3 py-1 rounded-xl border shadow-2xs {{ $pctBadgeBg }}">
                        {{ $pct }}%
                    </span>
                </div>
            </div>

            {{-- Flow Hubungan Visual Ringkas & Menarik --}}
            <div class="bg-white/90 backdrop-blur-xs rounded-2xl p-4 border border-slate-200/80 flex flex-col md:flex-row items-center justify-between gap-4 shadow-2xs">
                {{-- Antecedent (Kondisi Cacat / Penyebab) --}}
                <div class="flex-1 bg-slate-50/80 border border-slate-200 rounded-xl p-3.5 shadow-2xs w-full">
                    <div class="text-[10px] font-mono font-bold uppercase tracking-wider text-sky-600 mb-1 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span> {{ $r['ant_label'] ?? 'JIKA TERJADI CACAT' }}:
                    </div>
                    <div class="text-sm font-black text-slate-900 leading-snug">{{ $r['antecedents'] }}</div>
                </div>

                {{-- Panah Alur Modern --}}
                <div class="flex flex-col items-center justify-center shrink-0 py-1 px-3 bg-slate-100/90 rounded-xl border border-slate-200 shadow-2xs">
                    <div class="text-[11px] font-bold text-slate-800 font-mono flex items-center gap-1.5">
                        <span>{{ $r['arrow_text'] ?? 'Cenderung Menyebabkan' }}</span>
                        <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </div>
                    <span class="text-[10px] text-slate-500 font-mono">Akurasi {{ $pct }}%</span>
                </div>

                {{-- Consequent (Penyebab Utama / Dampak Cacat) --}}
                <div class="flex-1 bg-amber-50/60 border border-amber-200 rounded-xl p-3.5 shadow-2xs w-full">
                    <div class="text-[10px] font-mono font-bold uppercase tracking-wider text-amber-700 mb-1 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> {{ $r['con_label'] ?? 'MAKA PENYEBAB UTAMA' }}:
                    </div>
                    <div class="text-sm font-black text-amber-950 leading-snug">{{ $r['consequents'] }}</div>
                </div>
            </div>

            {{-- Saran Ringkas & Akses Detail --}}
            <div class="mt-4 flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-200/60">
                <div class="flex items-center gap-2 text-xs text-slate-700">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                    <span><strong>Saran Supervisor:</strong> {!! $r['saran_text'] ?? "Perbaiki faktor <strong class=\"text-slate-900\">{$r['consequents']}</strong> untuk mencegah cacat <strong class=\"text-slate-900\">{$r['antecedents']}</strong>." !!}</span>
                </div>

                <button type="button" @click="showDetail = !showDetail"
                        class="inline-flex items-center gap-2 text-xs font-bold text-slate-800 hover:text-slate-900 bg-white hover:bg-slate-50 px-3.5 py-1.5 rounded-xl transition border border-slate-300 shadow-2xs cursor-pointer shrink-0">
                    <span>🔍</span>
                    <span x-text="showDetail ? 'Tutup Detail Lapangan' : 'Rincian Detail Lapangan'">Rincian Detail Lapangan</span>
                    <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="showDetail ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
            </div>

            {{-- Breakdown Detail Real Lapangan (Multi-Level Drill-Down) --}}
            @php
                $dbd = $r['detail_breakdown'] ?? ['detail_ketidaksesuaian' => [], 'detail_penyebab' => [], 'total_matching' => 0];
                $topKets = $dbd['detail_ketidaksesuaian'] ?? [];
                $topPens = $dbd['detail_penyebab'] ?? [];
            @endphp
            @if (!empty($topKets) || !empty($topPens))
                <div x-show="showDetail" x-cloak class="mt-3 pt-3.5 border-t border-slate-200/80 bg-slate-50 rounded-2xl p-4 text-xs transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <span class="font-bold text-slate-800 font-mono flex items-center gap-1.5">
                            <span>📊</span> Rincian Bukti Transaksi Real Komplain di Lapangan:
                        </span>
                        <span class="text-[11px] text-sky-700 font-mono font-bold bg-white px-2.5 py-0.5 rounded-full border border-slate-200">
                            {{ $dbd['total_matching'] }} Kasus Komplain Terkait
                        </span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Detail Cacat --}}
                        <div class="space-y-2 bg-white p-3.5 rounded-xl border border-slate-200 shadow-2xs">
                            <span class="text-[11px] font-bold font-mono text-sky-700 block border-b border-slate-100 pb-1.5">
                                📌 Detail Cacat Terbanyak ({{ $r['antecedents'] }}):
                            </span>
                            @forelse ($topKets as $k => $item)
                                <div class="flex items-center justify-between text-slate-700 text-xs py-0.5">
                                    <span class="truncate pr-2 font-medium">• {{ $item['detail'] }}</span>
                                    <span class="font-bold text-sky-700 bg-sky-50 px-2 py-0.5 rounded font-mono text-[10px] shrink-0 border border-sky-100">
                                        {{ $item['count'] }}x ({{ $item['percent'] }}%)
                                    </span>
                                </div>
                            @empty
                                <span class="text-slate-400 italic text-[11px]">Belum ada rincian detail cacat.</span>
                            @endforelse
                        </div>

                        {{-- Detail Penyebab --}}
                        <div class="space-y-2 bg-white p-3.5 rounded-xl border border-slate-200 shadow-2xs">
                            <span class="text-[11px] font-bold font-mono text-amber-700 block border-b border-slate-100 pb-1.5">
                                ⚙️ Detail Penyebab Terbanyak ({{ $r['consequents'] }}):
                            </span>
                            @forelse ($topPens as $k => $item)
                                <div class="flex items-center justify-between text-slate-700 text-xs py-0.5">
                                    <span class="truncate pr-2 font-medium">• {{ $item['detail'] }}</span>
                                    <span class="font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded font-mono text-[10px] shrink-0 border border-amber-100">
                                        {{ $item['count'] }}x ({{ $item['percent'] }}%)
                                    </span>
                                </div>
                            @empty
                                <span class="text-slate-400 italic text-[11px]">Belum ada rincian detail penyebab.</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @empty
        <div class="bg-white rounded-2xl p-8 text-center text-slate-400 border border-slate-200">
            <span class="text-3xl block mb-2">🔍</span>
            <p class="text-sm font-medium">Tidak ada pola keterkaitan yang memenuhi ambang batas filter saat ini.</p>
            <p class="text-xs text-slate-400 mt-1">Coba turunkan nilai Min. Kemungkinan (Confidence %) di atas.</p>
        </div>
    @endforelse
</div>

{{-- SECTION 2: TAMPILAN TABEL TEKNIS (Disembunyikan secara default, bisa ditoggle) --}}
<div id="aprioriTableView" class="hidden mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <h3 class="font-semibold text-slate-800 mb-3">Tabel Matematika Association Rules</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-slate-500 border-b border-slate-200 bg-slate-50">
                        <th class="py-2.5 px-3">Kondisi Awal (Antecedent)</th>
                        <th class="py-2.5 px-3">Penyebab / Akibat (Consequent)</th>
                        <th class="py-2.5 px-3 text-right">Support (%)</th>
                        <th class="py-2.5 px-3 text-right">Confidence (%)</th>
                        <th class="py-2.5 px-3 text-right">Lift Ratio</th>
                        <th class="py-2.5 px-3 text-center">Kekuatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($rules as $r)
                        <tr class="hover:bg-slate-50">
                            <td class="py-2.5 px-3 font-medium text-slate-700">{{ $r['antecedents'] }}</td>
                            <td class="py-2.5 px-3 font-semibold text-sky-700">{{ $r['consequents'] }}</td>
                            <td class="py-2.5 px-3 text-right">{{ number_format($r['support']*100,1) }}%</td>
                            <td class="py-2.5 px-3 text-right font-bold text-slate-800">{{ number_format($r['confidence']*100,1) }}%</td>
                            <td class="py-2.5 px-3 text-right font-bold text-indigo-600">{{ number_format($r['lift'],2) }}</td>
                            <td class="py-2.5 px-3 text-center">
                                <span class="text-[11px] px-2.5 py-0.5 rounded-full font-medium
                                    {{ $r['kekuatan']==='Sangat Kuat' ? 'bg-rose-100 text-rose-700' : ($r['kekuatan']==='Kuat' ? 'bg-amber-100 text-amber-700' : 'bg-slate-200 text-slate-600') }}">
                                    {{ $r['kekuatan'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-4 text-center text-slate-400 text-sm">Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Visualisasi Lift Chart -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
    <h2 class="font-semibold text-slate-800 mb-1">Grafik Perbandingan Kekuatan Pola (Lift Ratio)</h2>
    <p class="text-xs text-slate-500 mb-4">Grafik tingkat kekuatan kaitan sebab-akibat. Semakin tinggi batang grafik, semakin kuat hubungan antar variabel.</p>
    <div class="relative w-full rounded-lg bg-white" style="height: 400px;">
        <canvas id="aprioriChart"></canvas>
    </div>
</div>
@endsection


@push('scripts')
<script>
function toggleAprioriView(mode) {
    const cardsView = document.getElementById('aprioriCardsView');
    const tableView = document.getElementById('aprioriTableView');
    const btnCards  = document.getElementById('btnModeCards');
    const btnTable  = document.getElementById('btnModeTable');

    if (mode === 'cards') {
        cardsView?.classList.remove('hidden');
        tableView?.classList.add('hidden');
        btnCards?.classList.add('bg-white', 'shadow-sm', 'text-slate-800', 'font-bold');
        btnCards?.classList.remove('text-slate-600');
        btnTable?.classList.remove('bg-white', 'shadow-sm', 'text-slate-800', 'font-bold');
        btnTable?.classList.add('text-slate-600');
    } else {
        cardsView?.classList.add('hidden');
        tableView?.classList.remove('hidden');
        btnTable?.classList.add('bg-white', 'shadow-sm', 'text-slate-800', 'font-bold');
        btnTable?.classList.remove('text-slate-600');
        btnCards?.classList.remove('bg-white', 'shadow-sm', 'text-slate-800', 'font-bold');
        btnCards?.classList.add('text-slate-600');
    }
}

document.addEventListener("DOMContentLoaded", function() {
    const rules = @json($rules);
    
    if (rules.length === 0) {
        document.getElementById('aprioriChart').parentElement.innerHTML = '<div class="flex items-center justify-center h-full text-sm text-slate-400">Tidak ada data untuk divisualisasikan.</div>';
        return;
    }

    // Ambil maksimal 15 rules teratas (sudah diurutkan by lift desc di controller/service)
    const topRules = rules.slice(0, 15);
    
    const labels = topRules.map(r => r.antecedents + ' ➔ ' + r.consequents);
    const dataLift = topRules.map(r => r.lift);
    
    const bgColors = topRules.map(r => {
        if (r.lift >= 5) return '#e11d48'; // Sangat Kuat
        if (r.lift >= 3) return '#d97706'; // Kuat
        if (r.lift >= 1.5) return '#0ea5e9'; // Sedang
        return '#94a3b8'; // Lemah
    });

    const ctx = document.getElementById('aprioriChart');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Lift Ratio',
                data: dataLift,
                backgroundColor: bgColors,
                borderRadius: 4,
                barThickness: 24
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            const r = topRules[ctx.dataIndex];
                            return [
                                ` Lift: ${r.lift.toFixed(2)} (${r.kekuatan})`,
                                ` Support: ${(r.support*100).toFixed(1)}%`,
                                ` Confidence: ${(r.confidence*100).toFixed(1)}%`
                            ];
                        }
                    }
                },
                datalabels: {
                    display: true,
                    anchor: 'end',
                    align: 'end',
                    color: '#475569',
                    font: { size: 11, weight: '600' },
                    formatter: (v) => v.toFixed(2)
                }
            },
            layout: { padding: { right: 40 } },
            scales: {
                x: { 
                    beginAtZero: true, 
                    title: { display: true, text: 'Lift Ratio' },
                    grid: { color: '#f1f5f9' }
                },
                y: { 
                    grid: { display: false }, 
                    ticks: { font: { size: 11 }, autoSkip: false }
                }
            }
        }
    });
});
</script>
@endpush
