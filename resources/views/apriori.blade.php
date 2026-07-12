@extends('layouts.app')
@section('title', 'Analisis Algoritma Apriori')
@section('subtitle', 'Association rule mining antara jenis ketidaksesuaian dan penyebab complaint')

@section('actions')
    <a href="{{ route('export.apriori.excel') }}"
       class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
        Excel
    </a>
    <a href="{{ route('export.laporan.pdf') }}"
       class="inline-flex items-center gap-2 bg-rose-600 hover:bg-rose-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
        PDF
    </a>
@endsection

@section('content')

<!-- Parameter -->
<form method="GET" class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 mb-6">
    <div class="flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Minimum Support (%)</label>
            <input type="number" name="min_support" value="{{ $minSupport }}" step="1" min="1" max="100"
                   class="w-32 rounded-lg border-slate-300 text-sm focus:ring-sky-500 focus:border-sky-500 border px-3 py-2">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Minimum Confidence (%)</label>
            <input type="number" name="min_confidence" value="{{ $minConfidence }}" step="1" min="1" max="100"
                   class="w-32 rounded-lg border-slate-300 text-sm focus:ring-sky-500 focus:border-sky-500 border px-3 py-2">
        </div>
        <button class="bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium px-5 py-2 rounded-lg">Hitung Ulang</button>
        <div class="ml-auto text-sm text-slate-500">
            Total transaksi: <span class="font-bold text-slate-800">{{ $totalTrx }}</span>
        </div>
    </div>
    <p class="text-xs text-slate-400 mt-3">
        Item dibentuk dari kolom <em>Apriori Ketidaksesuaian</em> &amp; <em>Apriori Penyebab</em>.
        Setiap complaint = 1 transaksi. Mengubah parameter atau menambah data akan menghitung ulang secara otomatis.
    </p>
</form>

<div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
    <!-- Frequent Itemsets -->
    <section class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <h2 class="font-semibold text-slate-800 mb-1">Frequent Itemsets</h2>
        <p class="text-xs text-slate-500 mb-4">Item / kombinasi item yang memenuhi minimum support.</p>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-slate-500 border-b border-slate-200">
                        <th class="py-2">Itemset</th>
                        <th class="py-2 text-center">Size</th>
                        <th class="py-2 text-right">Support</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($frequent as $f)
                        <tr>
                            <td class="py-2 pr-2">
                                @foreach ($f['items'] as $it)
                                    <span class="inline-block bg-slate-100 text-slate-700 text-xs px-2 py-0.5 rounded mr-1 mb-1">
                                        {{ str_replace(['Ketidaksesuaian=','Penyebab='], ['🔧 ','⚠ '], $it) }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="py-2 text-center text-xs text-slate-500">{{ $f['size'] }}</td>
                            <td class="py-2 text-right font-medium">{{ number_format($f['support']*100,1) }}%
                                <span class="block text-[10px] text-slate-400">{{ $f['count'] }}x</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="py-4 text-center text-slate-400 text-sm">Tidak ada itemset yang memenuhi support {{ $minSupport }}%.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <!-- Association Rules -->
    <section class="lg:col-span-3 bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <h2 class="font-semibold text-slate-800 mb-1">Association Rules</h2>
        <p class="text-xs text-slate-500 mb-4">Aturan "jika–maka" yang memenuhi minimum confidence, diurutkan dari lift tertinggi.</p>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-slate-500 border-b border-slate-200">
                        <th class="py-2">Jika (antecedent)</th>
                        <th class="py-2">Maka (consequent)</th>
                        <th class="py-2 text-right">Support</th>
                        <th class="py-2 text-right">Confidence</th>
                        <th class="py-2 text-right">Lift</th>
                        <th class="py-2 text-center">Kekuatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($rules as $r)
                        <tr class="hover:bg-slate-50">
                            <td class="py-2 font-medium text-slate-700">{{ $r['antecedents'] }}</td>
                            <td class="py-2 font-medium text-sky-700">{{ $r['consequents'] }}</td>
                            <td class="py-2 text-right">{{ number_format($r['support']*100,1) }}%</td>
                            <td class="py-2 text-right">{{ number_format($r['confidence']*100,1) }}%</td>
                            <td class="py-2 text-right font-bold">{{ number_format($r['lift'],2) }}</td>
                            <td class="py-2 text-center">
                                <span class="text-[11px] px-2 py-0.5 rounded-full
                                    {{ $r['kekuatan']==='Sangat Kuat' ? 'bg-rose-100 text-rose-700' : ($r['kekuatan']==='Kuat' ? 'bg-amber-100 text-amber-700' : ($r['kekuatan']==='Sedang' ? 'bg-sky-100 text-sky-700' : 'bg-slate-200 text-slate-600')) }}">
                                    {{ $r['kekuatan'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-4 text-center text-slate-400 text-sm">Tidak ada rule yang memenuhi confidence {{ $minConfidence }}%.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if (count($rules))
            <div class="mt-5 space-y-2">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Interpretasi</h3>
                @foreach ($rules as $r)
                    <p class="text-xs text-slate-600 leading-relaxed">• {{ $r['interpretasi'] }}</p>
                @endforeach
            </div>
        @endif
    </section>
</div>

<div class="mt-6 bg-sky-50 border border-sky-200 rounded-xl p-5 text-sm text-slate-700">
    <h3 class="font-semibold text-sky-800 mb-2">Cara membaca metrik</h3>
    <ul class="list-disc pl-5 space-y-1 text-xs">
        <li><b>Support</b> — seberapa sering kombinasi muncul dari seluruh transaksi.</li>
        <li><b>Confidence</b> — dari semua kasus antecedent, berapa persen yang juga mengandung consequent.</li>
        <li><b>Lift</b> — kekuatan kaitan. &gt;1 berarti berkaitan positif; semakin besar semakin kuat (≥5 sangat kuat, ≥3 kuat, ≥1.5 sedang).</li>
    </ul>
</div>
@endsection
