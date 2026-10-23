@extends('layouts.app')
@section('title', 'Analisis Pola Hubungan (Apriori)')
@section('subtitle', 'Mengidentifikasi pola hubungan antara jenis cacat produk dan faktor penyebabnya')
@section('hide_create_btn', true)

@section('content')



<!-- Form Filter & Parameter -->
<form method="GET" class="bg-white rounded shadow-sm border border-gray-200 p-5 mb-6">
    <div class="flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-xs font-bold text-gray-600 mb-1 uppercase tracking-wide">Filter Tahun</label>
            <select name="tahun" class="w-32 rounded-md border-gray-300 text-xs font-semibold focus:ring-[#4e73df] focus:border-[#4e73df] border px-3 py-1.5 h-9 bg-white">
                <option value="">Semua Tahun</option>
                @foreach ($years as $y)
                    <option value="{{ $y }}" @selected(request()->integer('tahun') === (int) $y)>Tahun {{ $y }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-600 mb-1 uppercase tracking-wide">Filter Bulan</label>
            <select name="bulan" class="w-36 rounded-md border-gray-300 text-xs font-semibold focus:ring-[#4e73df] focus:border-[#4e73df] border px-3 py-1.5 h-9 bg-white">
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
            <label class="block text-xs font-bold text-gray-600 mb-1 uppercase tracking-wide" title="Frekuensi minimal pola harus muncul dari total komplain">Min. Support (%)</label>
            <input type="number" name="min_support" value="{{ $minSupport }}" step="0.5" min="0.5" max="100"
                   class="w-32 rounded-md border-gray-300 text-xs font-semibold focus:ring-[#4e73df] focus:border-[#4e73df] border px-3 py-1.5 h-9 bg-white"
                   title="Contoh: 1.5% berarti pola harus muncul minimal 1.5% dari seluruh kasus">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-600 mb-1 uppercase tracking-wide" title="Tingkat kepastian minimal hubungan sebab-akibat">Min. Confidence (%)</label>
            <input type="number" name="min_confidence" value="{{ $minConfidence }}" step="5" min="10" max="100"
                   class="w-32 rounded-md border-gray-300 text-xs font-semibold focus:ring-[#4e73df] focus:border-[#4e73df] border px-3 py-1.5 h-9 bg-white"
                   title="Contoh: 50% berarti jika cacat muncul, minimal 50% disebabkan oleh faktor tersebut">
        </div>
        <button class="btn btn-primary btn-sm h-9 rounded-md">
            <i class="fas fa-search fa-sm"></i>
            <span>Tampilkan Pola</span>
        </button>
        @if (request()->hasAny(['tahun','bulan','min_confidence','min_support']))
            <a href="{{ url()->current() }}" class="btn btn-outline-secondary btn-sm h-9 rounded-md">
                <i class="fas fa-undo"></i>
                <span>Reset Filter</span>
            </a>
        @endif
        <div class="ml-auto text-xs text-slate-500 flex items-center h-9">
            Data teranalisis: <b class="text-slate-800 font-bold text-sm ml-1">{{ $totalTrx }}</b> 
              <span class="ml-1">Komplain</span>
        </div>
    </div>
</form>

{{-- Matriks Aturan Analisis Apriori --}}

{{-- Matriks Aturan Analisis Apriori --}}
<div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6">

    {{-- Header Utama --}}
    <div class="px-5 py-3 border-b border-gray-200">
        <h3 class="text-sm font-bold text-gray-800">
            Matriks Aturan Analisis Apriori &amp; Rekomendasi Tindak Lanjut
        </h3>
        <p class="text-xs text-gray-500 mt-1">
            Klasifikasi pola asosiasi sebagai dasar penentuan tindak lanjut mutu.
        </p>
    </div>

    {{-- Isi Card --}}
    <div class="p-5">

        <div class="grid grid-cols-3 gap-4 items-stretch">

            {{-- Prioritas --}}
            <div class="bg-white border border-gray-200 border-l-4 border-l-red-500 rounded-md shadow-sm p-4 flex flex-col">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-3 h-3 rounded-full bg-red-500 flex-shrink-0"></span>
                    <h4 class="text-sm font-bold text-red-600">Prioritas</h4>
                </div>

                <p class="text-xs text-gray-600 leading-relaxed flex-grow">
                    Pola utama untuk tindakan korektif dan pencegahan.
                </p>

                <div class="border-t border-gray-200 mt-4 pt-3 flex items-center justify-between">
                    <span class="text-xs text-gray-500">Jumlah pola</span>
                    <span class="text-lg font-bold text-red-600">
                        {{ collect($rules)->where('prioritas', 'Prioritas')->count() }}
                    </span>
                </div>
            </div>

            {{-- Dipertimbangkan --}}
            <div class="bg-white border border-gray-200 border-l-4 border-l-yellow-400 rounded-md shadow-sm p-4 flex flex-col">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 flex-shrink-0"></span>
                    <h4 class="text-sm font-bold text-yellow-600">Dipertimbangkan</h4>
                </div>

                <p class="text-xs text-gray-600 leading-relaxed flex-grow">
                    Pola pendukung untuk menentukan tindakan perbaikan.
                </p>

                <div class="border-t border-gray-200 mt-4 pt-3 flex items-center justify-between">
                    <span class="text-xs text-gray-500">Jumlah pola</span>
                    <span class="text-lg font-bold text-yellow-600">
                        {{ collect($rules)->where('prioritas', 'Dipertimbangkan')->count() }}
                    </span>
                </div>
            </div>

            {{-- Dipantau --}}
            <div class="bg-white border border-gray-200 border-l-4 border-l-blue-500 rounded-md shadow-sm p-4 flex flex-col">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-3 h-3 rounded-full bg-blue-500 flex-shrink-0"></span>
                    <h4 class="text-sm font-bold text-blue-600">Dipantau</h4>
                </div>

                <p class="text-xs text-gray-600 leading-relaxed flex-grow">
                    Pola yang dipantau untuk melihat perubahan tren komplain.
                </p>

                <div class="border-t border-gray-200 mt-4 pt-3 flex items-center justify-between">
                    <span class="text-xs text-gray-500">Jumlah pola</span>
                    <span class="text-lg font-bold text-blue-600">
                        {{ collect($rules)->where('prioritas', 'Dipantau')->count() }}
                    </span>
                </div>
            </div>

        </div>

    </div>
</div>

<script>
function aprioriManager() {
    return {
        mode: 'chart',
        katFilter: 'all',
        allRules: @json($rules),
        search: '',
        perPage: 25,
        currentPage: 1,

        get filteredRules() {
            let list = this.allRules;
            if (this.katFilter !== 'all') {
                list = list.filter(r => r.prioritas === this.katFilter);
            }
            if (this.search && this.search.trim() !== '') {
                const q = this.search.toLowerCase().trim();
                list = list.filter(r => 
                    (r.antecedents && r.antecedents.toLowerCase().includes(q)) ||
                    (r.consequents && r.consequents.toLowerCase().includes(q)) ||
                    (r.prioritas && r.prioritas.toLowerCase().includes(q))
                );
            }
            return list;
        },

        get totalEntries() {
            return this.filteredRules.length;
        },

        get totalPages() {
            return Math.ceil(this.totalEntries / this.perPage) || 1;
        },

        get paginatedRules() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.filteredRules.slice(start, start + this.perPage);
        },

        get startEntry() {
            if (this.totalEntries === 0) return 0;
            return (this.currentPage - 1) * this.perPage + 1;
        },

        get endEntry() {
            return Math.min(this.currentPage * this.perPage, this.totalEntries);
        },

        setFilter(kat) {
            this.katFilter = kat;
            this.currentPage = 1;
        },

        gotoPage(p) {
            if (p >= 1 && p <= this.totalPages) {
                this.currentPage = p;
            }
        },

        prevPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
            }
        },

        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
            }
        },

        get paginationElements() {
            const total = this.totalPages;
            const current = this.currentPage;
            if (total <= 5) {
                return Array.from({ length: total }, (_, i) => i + 1);
            }
            if (current <= 2) {
                return [1, 2, 3, '...'];
            }
            if (current === 3) {
                return [1, 2, 3, 4, '...'];
            }
            if (current >= total - 1) {
                return ['...', total - 2, total - 1, total];
            }
            if (current === total - 2) {
                return ['...', total - 3, total - 2, total - 1, total];
            }
            return ['...', current - 1, current, current + 1, '...'];
        },

        get pageNumbers() {
            return this.paginationElements;
        }
    };
}
</script>

<div x-data="aprioriManager()">
    {{-- Header Bar: Judul di Kiri dan Switcher Mode SEJAJAR di Kanan --}}
    <div class="flex items-center justify-between gap-4 mb-4">
        <h2 class="text-base font-bold text-slate-800" x-text="mode === 'chart' ? 'Grafik Top Pola Hubungan Prioritas' : 'Tabel Analisis Association Rules'">
            Grafik Top Pola Hubungan Prioritas
        </h2>

        {{-- Switcher Mode (Tampilan Grafik & Tabel Data) SEJAJAR --}}
        <div class="flex items-center bg-slate-200/80 p-1 rounded-full text-xs font-semibold gap-1 border border-slate-200 shrink-0">
            <button type="button" @click="mode = 'chart'" id="btnModeChart" onclick="toggleAprioriView('chart')"
                    :class="mode === 'chart' ? 'bg-white shadow-xs text-[#4e73df] font-bold' : 'text-slate-600 hover:text-slate-800'"
                    class="h-8 px-3.5 rounded-full transition flex items-center gap-1.5 cursor-pointer">
                <i class="fas fa-chart-bar fa-xs"></i> <span>Tampilan Grafik</span>
            </button>
            <button type="button" @click="mode = 'table'" id="btnModeTable" onclick="toggleAprioriView('table')"
                    :class="mode === 'table' ? 'bg-white shadow-xs text-[#4e73df] font-bold' : 'text-slate-600 hover:text-slate-800'"
                    class="h-8 px-3.5 rounded-full transition flex items-center gap-1.5 cursor-pointer">
                <i class="fas fa-table fa-xs"></i> <span>Tabel Data</span>
            </button>
        </div>
    </div>

    {{-- SECTION 1: TAMPILAN GRAFIK (Top Prioritas yang Harus Ditangani - Template SB Admin 2) --}}
    <div id="aprioriChartView" x-show="mode === 'chart'" class="mb-8">
        @php
            $priorityRulesList = collect($rules)->where('prioritas', 'Prioritas')->sortByDesc('lift')->values()->take(10);
            $totalPriority = $priorityRulesList->count();
            $maxLift = $priorityRulesList->isNotEmpty() ? $priorityRulesList->max('lift') : (collect($rules)->max('lift') ?? 0);
            $totalPriorityKasus = $priorityRulesList->sum('count');
        @endphp

        {{-- Card Grafik Utama SB Admin 2 --}}
        <div class="card-sb shadow-sm mb-6">
            <div class="card-sb-header py-3 px-4 flex flex-row items-center justify-between">
                <h6 class="m-0 font-bold text-[#4e73df] flex items-center gap-2">
                    <i class="fas fa-chart-bar"></i>
                    <span>Grafik Pola Top 10 Prioritas Mutu (Wajib Ditangani)</span>
                </h6>

            </div>
            <div class="p-5">
                <div class="relative w-full rounded-lg bg-white" style="height: 420px;">
                    <canvas id="aprioriChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Rincian Penanganan Top Prioritas --}}
        @if($priorityRulesList->isNotEmpty())
            <div class="card-sb shadow-sm">
                <div class="card-sb-header py-3 px-4 flex items-center justify-between">
                    <h6 class="m-0 font-bold text-[#e74a3b] flex items-center gap-2">
                        <i class="fas fa-tasks"></i>
                        <span>Daftar Tindakan Korektif &amp; Preventif untuk Pola Top 10 Prioritas</span>
                    </h6>

                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($priorityRulesList as $idx => $pr)
                        <div class="p-4 hover:bg-slate-50 transition flex flex-col md:flex-row md:items-center justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <div class="w-7 h-7 rounded-full bg-rose-100 text-rose-700 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                                    #{{ $idx + 1 }}
                                </div>
                                <div>
                                    <div class="flex flex-wrap items-center gap-2 mb-1">
                                        <span class="font-bold text-sm text-slate-800">{{ $pr['antecedents'] }}</span>
                                        <span class="text-rose-600 font-bold text-xs flex items-center gap-1">
                                            <i class="fas fa-long-arrow-alt-right"></i> {{ $pr['consequents'] }}
                                        </span>
                                        <span class="badge badge-danger">
                                            Prioritas Kritis
                                        </span>
                                    </div>

                                </div>
                            </div>
                            <div class="flex items-center gap-4 text-xs shrink-0 self-end md:self-center">
                                <div class="text-right">
                                    <div class="text-[10px] text-slate-400 font-semibold uppercase">Kasus</div>
                                    <div class="font-bold text-slate-700 font-mono">{{ $pr['count'] }}x</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-[10px] text-slate-400 font-semibold uppercase">Confidence</div>
                                    <div class="font-bold text-slate-700 font-mono">{{ number_format($pr['confidence'] * 100, 1) }}%</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-[10px] text-slate-400 font-semibold uppercase">Lift Ratio</div>
                                    <div class="font-bold text-[#e74a3b] font-mono text-sm">{{ number_format($pr['lift'], 2) }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="card-sb shadow-sm p-6 text-center">
                <i class="fas fa-check-circle text-emerald-500 text-3xl mb-2"></i>
                <h5 class="text-base font-bold text-slate-800">Tidak Ada Pola Kritis (Prioritas) Pada Periode Ini</h5>
                <p class="text-xs text-slate-500 max-w-md mx-auto mt-1">
                    Seluruh pola hubungan berada dalam batas toleransi ("Dipertimbangkan" atau "Dipantau"). Silakan pilih tab <b>Tabel Data</b> untuk melihat seluruh association rules.
                </p>
            </div>
        @endif
    </div>

    {{-- SECTION 2: TAMPILAN TABEL TEKNIS (Saling Bergantian dengan Grafik) --}}
    <div id="aprioriTableView" x-show="mode === 'table'" class="hidden mb-8">

        {{-- Filter Kategori Prioritas: Di Bawah Tulisan Judul, Di Atas Tabel --}}
        <div class="flex flex-wrap items-center gap-2 mb-3">
            <button type="button"
                    @click="setFilter('all')"
                    :class="katFilter === 'all'
                        ? 'bg-[#4e73df] text-white border-[#4e73df] shadow-xs'
                        : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 hover:text-slate-900'"
                    class="inline-flex items-center gap-2 rounded-full border px-3.5 h-8 text-xs font-bold transition cursor-pointer">
                Semua
                <span :class="katFilter === 'all' ? 'bg-white/25 text-white' : 'bg-slate-100 text-slate-600'"
                      class="rounded-full px-2 py-0.5 text-[11px] font-bold font-mono">
                    {{ count($rules) }}
                </span>
            </button>

            <button type="button"
                    @click="setFilter('Prioritas')"
                    :class="katFilter === 'Prioritas'
                        ? 'bg-[#e74a3b] text-white border-[#e74a3b] shadow-xs'
                        : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 hover:text-red-600'"
                    class="inline-flex items-center gap-2 rounded-full border px-3.5 h-8 text-xs font-bold transition cursor-pointer">
                <span :class="katFilter === 'Prioritas' ? 'bg-white' : 'bg-[#e74a3b]'" class="w-2 h-2 rounded-full"></span>
                Prioritas
                <span :class="katFilter === 'Prioritas' ? 'bg-white/25 text-white' : 'bg-slate-100 text-slate-600'"
                      class="rounded-full px-2 py-0.5 text-[11px] font-bold font-mono">
                    {{ collect($rules)->where('prioritas', 'Prioritas')->count() }}
                </span>
            </button>

            <button type="button"
                    @click="setFilter('Dipertimbangkan')"
                    :class="katFilter === 'Dipertimbangkan'
                        ? 'bg-[#f6c23e] text-white border-[#f6c23e] shadow-xs'
                        : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 hover:text-yellow-600'"
                    class="inline-flex items-center gap-2 rounded-full border px-3.5 h-8 text-xs font-bold transition cursor-pointer">
                <span :class="katFilter === 'Dipertimbangkan' ? 'bg-white' : 'bg-[#f6c23e]'" class="w-2 h-2 rounded-full"></span>
                Dipertimbangkan
                <span :class="katFilter === 'Dipertimbangkan' ? 'bg-white/25 text-white' : 'bg-slate-100 text-slate-600'"
                      class="rounded-full px-2 py-0.5 text-[11px] font-bold font-mono">
                    {{ collect($rules)->where('prioritas', 'Dipertimbangkan')->count() }}
                </span>
            </button>

            <button type="button"
                    @click="setFilter('Dipantau')"
                    :class="katFilter === 'Dipantau'
                        ? 'bg-[#36b9cc] text-white border-[#36b9cc] shadow-xs'
                        : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 hover:text-cyan-600'"
                    class="inline-flex items-center gap-2 rounded-full border px-3.5 h-8 text-xs font-bold transition cursor-pointer">
                <span :class="katFilter === 'Dipantau' ? 'bg-white' : 'bg-[#36b9cc]'" class="w-2 h-2 rounded-full"></span>
                Dipantau
                <span :class="katFilter === 'Dipantau' ? 'bg-white/25 text-white' : 'bg-slate-100 text-slate-600'"
                      class="rounded-full px-2 py-0.5 text-[11px] font-bold font-mono">
                    {{ collect($rules)->where('prioritas', 'Dipantau')->count() }}
                </span>
            </button>
        </div>

        <div class="card-sb shadow-sm">
            <div class="card-sb-header py-3 px-4 flex items-center justify-between">
                <h6 class="m-0 font-bold text-[#4e73df] flex items-center gap-2">
                    <i class="fas fa-table"></i>
                    <span>Tabel Matematika Association Rules &amp; Parameter Mutu</span>
                </h6>
                <span class="text-xs font-semibold text-slate-500 font-mono">
                    Total: <span x-text="totalEntries"></span> Aturan
                </span>
            </div>


            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs text-slate-500 border-b border-slate-200 bg-slate-50">
                            <th class="py-2.5 px-3">Kondisi Awal (Antecedent)</th>
                            <th class="py-2.5 px-3">Penyebab / Akibat (Consequent)</th>
                            <th class="py-2.5 px-3 text-center">Kejadian</th>
                            <th class="py-2.5 px-3 text-right">Support (%)</th>
                            <th class="py-2.5 px-3 text-right">Confidence (%)</th>
                            <th class="py-2.5 px-3 text-right">Lift Ratio</th>
                            <th class="py-2.5 px-3 text-center">Kategori Prioritas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="(r, idx) in paginatedRules" :key="idx">
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-2.5 px-3 font-medium text-slate-700" x-text="r.antecedents"></td>
                                <td class="py-2.5 px-3 font-semibold text-[#4e73df]" x-text="r.consequents"></td>
                                <td class="py-2.5 px-3 text-center font-bold text-slate-800 font-mono" x-text="r.count + 'x'"></td>
                                <td class="py-2.5 px-3 text-right" x-text="(r.support * 100).toFixed(1) + '%'"></td>
                                <td class="py-2.5 px-3 text-right font-bold text-slate-800" x-text="(r.confidence * 100).toFixed(1) + '%'"></td>
                                <td class="py-2.5 px-3 text-right font-bold text-[#4e73df]" x-text="Number(r.lift).toFixed(2)"></td>
                                <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                    <span :class="r.prioritas === 'Prioritas' ? 'badge badge-danger' : (r.prioritas === 'Dipertimbangkan' ? 'badge badge-warning' : 'badge badge-info')"
                                          x-text="r.prioritas">
                                    </span>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="totalEntries === 0">
                            <td colspan="7" class="py-6 text-center text-slate-400 text-sm">
                                Tidak ada data association rules yang memenuhi kriteria pencarian atau filter.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Footer: Rows per page & Pagination Controls --}}
            <div class="px-4 py-3 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 text-sm bg-white">
                {{-- Sisi Kiri Bawah: Rows per page --}}
                <div class="flex items-center gap-3">
                    <label for="apriori_per_page" class="text-sm font-normal text-slate-900 whitespace-nowrap">
                        Rows per page
                    </label>
                    <div class="relative inline-flex items-center">
                        <select id="apriori_per_page"
                                x-model.number="perPage"
                                @change="currentPage = 1"
                                style="-webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: none;"
                                class="no-native-arrow bg-white border border-slate-200 hover:border-slate-300 text-slate-900 text-sm font-semibold rounded-xl pl-3.5 pr-8 py-1.5 min-w-[70px] focus:outline-hidden focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-[0_1px_2px_rgba(0,0,0,0.04)] cursor-pointer transition">
                            <option :value="10">10</option>
                            <option :value="25">25</option>
                            <option :value="50">50</option>
                            <option :value="100">100</option>
                        </select>
                        <div class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 stroke-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Sisi Kanan Bawah: Pagination Controls (Maksimal 5) --}}
                <div class="flex items-center justify-end">
                    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center gap-1 sm:gap-1.5 text-sm select-none">
                        {{-- Previous Button --}}
                        <button type="button"
                                @click="prevPage()"
                                :disabled="currentPage === 1"
                                :class="currentPage === 1 ? 'text-slate-300 cursor-not-allowed select-none' : 'text-slate-800 hover:text-primary cursor-pointer'"
                                class="inline-flex items-center gap-1.5 transition-colors px-2 py-1.5 font-medium text-sm">
                            <svg class="w-3.5 h-3.5 stroke-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="15 18 9 12 15 6"></polyline>
                            </svg>
                            Previous
                        </button>

                        {{-- Pagination Elements (Maksimal 5) --}}
                        <template x-for="(elem, idx) in paginationElements" :key="idx">
                            <div class="inline-flex items-center">
                                <template x-if="elem === '...'">
                                    <button type="button"
                                            @click="gotoPage(idx === 0 ? Math.max(1, currentPage - 3) : Math.min(totalPages, currentPage + 3))"
                                            class="inline-flex items-center justify-center min-w-[28px] h-9 px-1 text-slate-400 hover:text-slate-700 font-bold select-none text-base tracking-widest cursor-pointer transition-colors">
                                        &hellip;
                                    </button>
                                </template>
                                <template x-if="elem !== '...'">
                                    <button type="button"
                                            @click="gotoPage(elem)"
                                            :class="currentPage === elem ? 'z-10 font-semibold text-slate-900 bg-white border border-slate-200/90 rounded-xl shadow-[0_1px_2px_rgba(0,0,0,0.05)] cursor-default select-none' : 'font-medium text-slate-800 hover:text-primary hover:bg-slate-100/60 rounded-xl cursor-pointer'"
                                            class="inline-flex items-center justify-center min-w-[36px] h-9 px-3 text-sm transition-colors"
                                            x-text="elem">
                                    </button>
                                </template>
                            </div>
                        </template>

                        {{-- Next Button --}}
                        <button type="button"
                                @click="nextPage()"
                                :disabled="currentPage === totalPages || totalEntries === 0"
                                :class="(currentPage === totalPages || totalEntries === 0) ? 'text-slate-300 cursor-not-allowed select-none' : 'text-slate-800 hover:text-primary cursor-pointer'"
                                class="inline-flex items-center gap-1.5 transition-colors px-2 py-1.5 font-medium text-sm">
                            Next
                            <svg class="w-3.5 h-3.5 stroke-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </button>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let aprioriChartInstance = null;

function toggleAprioriView(mode) {
    const chartView = document.getElementById('aprioriChartView');
    const tableView = document.getElementById('aprioriTableView');
    const btnChart  = document.getElementById('btnModeChart');
    const btnTable  = document.getElementById('btnModeTable');

    if (mode === 'chart') {
        chartView?.classList.remove('hidden');
        tableView?.classList.add('hidden');
        btnChart?.classList.add('bg-white', 'shadow-sm', 'text-[#4e73df]', 'font-bold');
        btnChart?.classList.remove('text-slate-600');
        btnTable?.classList.remove('bg-white', 'shadow-sm', 'text-[#4e73df]', 'font-bold');
        btnTable?.classList.add('text-slate-600');

        // Resize chart jika instance tersedia
        if (aprioriChartInstance) {
            setTimeout(() => {
                aprioriChartInstance.resize();
            }, 50);
        }
    } else {
        chartView?.classList.add('hidden');
        tableView?.classList.remove('hidden');
        btnTable?.classList.add('bg-white', 'shadow-sm', 'text-[#4e73df]', 'font-bold');
        btnTable?.classList.remove('text-slate-600');
        btnChart?.classList.remove('bg-white', 'shadow-sm', 'text-[#4e73df]', 'font-bold');
        btnChart?.classList.add('text-slate-600');
    }
}

document.addEventListener("DOMContentLoaded", function() {
    const allRules = @json($rules);
    const chartCanvas = document.getElementById('aprioriChart');
    
    if (!chartCanvas) return;

    if (allRules.length === 0) {
        chartCanvas.parentElement.innerHTML = '<div class="flex items-center justify-center h-full text-sm text-slate-400">Tidak ada data aturan untuk divisualisasikan.</div>';
        return;
    }

    // Filter fokus HANYA pada Top Prioritas yang harus ditangani (Dibatasi 10 Teratas)
    let priorityRules = allRules.filter(r => r.prioritas === 'Prioritas');
    
    // Jika tidak ada yang berkategori 'Prioritas', fallback ke rules dengan Lift tertinggi
    const isOnlyPriority = priorityRules.length > 0;
    let targetRules = isOnlyPriority ? priorityRules : allRules;
    
    // Urutkan berdasarkan lift ratio descending dan batasi menjadi Top 10
    targetRules.sort((a, b) => b.lift - a.lift);
    targetRules = targetRules.slice(0, 10);

    const labels = targetRules.map(r => r.antecedents + ' ➔ ' + r.consequents);
    const dataLift = targetRules.map(r => r.lift);

    // Styling SB Admin 2: Danger (#e74a3b) untuk Prioritas, Warning (#f6c23e), Primary (#4e73df)
    const bgColors = targetRules.map(r => {
        if (r.prioritas === 'Prioritas') return '#e74a3b'; // Danger SB Admin 2
        if (r.prioritas === 'Dipertimbangkan') return '#f6c23e'; // Warning SB Admin 2
        return '#4e73df'; // Primary SB Admin 2
    });

    const hoverColors = targetRules.map(r => {
        if (r.prioritas === 'Prioritas') return '#be2617';
        if (r.prioritas === 'Dipertimbangkan') return '#dfa824';
        return '#2e59d9';
    });

    aprioriChartInstance = new Chart(chartCanvas, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Lift Ratio',
                data: dataLift,
                backgroundColor: bgColors,
                hoverBackgroundColor: hoverColors,
                borderColor: bgColors,
                borderRadius: 4,
                borderSkipped: false,
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
                    backgroundColor: '#ffffff',
                    titleColor: '#6e707e',
                    bodyColor: '#858796',
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    padding: 12,
                    callbacks: {
                        label: function(ctx) {
                            const r = targetRules[ctx.dataIndex];
                            return [
                                ` Status: ${r.prioritas} (Harus Ditangani)`,
                                ` Kasus: ${r.count}x kejadian komplain`,
                                ` Lift Ratio: ${r.lift.toFixed(2)} (${r.kekuatan})`,
                                ` Confidence: ${(r.confidence * 100).toFixed(1)}% | Support: ${(r.support * 100).toFixed(1)}%`,
                                ` Tindakan: ${r.tindak_lanjut ?? 'Tindakan Korektif & Preventif Segera'}`
                            ];
                        }
                    }
                },
                datalabels: {
                    display: true,
                    anchor: 'end',
                    align: 'end',
                    color: '#e74a3b',
                    font: { size: 11, weight: 'bold' },
                    formatter: (v) => v.toFixed(2)
                }
            },
            layout: { padding: { right: 45 } },
            scales: {
                x: { 
                    beginAtZero: true, 
                    title: { 
                        display: true, 
                        text: 'Kekuatan Kaitan Hubungan (Lift Ratio)',
                        color: '#6e707e',
                        font: { size: 12, weight: 'bold' }
                    },
                    grid: { 
                        color: '#eaecf4',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#858796'
                    }
                },
                y: { 
                    grid: { display: false }, 
                    ticks: { 
                        color: '#5a5c69',
                        font: { size: 11, weight: '600' }, 
                        autoSkip: false 
                    }
                }
            }
        }
    });
});
</script>
@endpush
