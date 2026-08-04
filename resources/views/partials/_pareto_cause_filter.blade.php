@php
    $namaBulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                 7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
    $pcaf = $paretoCauseFilter ?? ['year'=>null,'month'=>null,'years'=>[],'count'=>0];
    $aktif = $pcaf['year'] || $pcaf['month'];
@endphp
<form method="GET" action="{{ url()->current() }}#paretoCauseCard" class="flex flex-wrap items-center gap-2 mb-4 text-xs">
    {{-- Preserve other filter params --}}
    @foreach (['cs_mode','pareto_year','pareto_month','pareto_detail_year','pareto_detail_month','pareto_cust_year','pareto_cust_month'] as $p)
        @if (request($p))<input type="hidden" name="{{ $p }}" value="{{ request($p) }}">@endif
    @endforeach

    <span class="text-slate-500">Periode:</span>
    <select name="pareto_cause_year" onchange="this.form.submit()"
            class="rounded-lg border border-slate-300 px-2 py-1.5 text-xs focus:ring-sky-500 focus:border-sky-500">
        <option value="">Semua tahun</option>
        @foreach ($pcaf['years'] as $y)
            <option value="{{ $y }}" @selected((int) $pcaf['year'] === (int) $y)>{{ $y }}</option>
        @endforeach
    </select>
    <select name="pareto_cause_month" onchange="this.form.submit()"
            class="rounded-lg border border-slate-300 px-2 py-1.5 text-xs focus:ring-sky-500 focus:border-sky-500">
        <option value="">Semua bulan</option>
        @foreach ($namaBulan as $n => $label)
            <option value="{{ $n }}" @selected((int) $pcaf['month'] === (int) $n)>{{ $label }}</option>
        @endforeach
    </select>
    @if ($aktif)
        <a href="{{ url()->current() }}?{{ http_build_query(request()->except(['pareto_cause_year','pareto_cause_month'])) }}#paretoCauseCard" class="text-slate-400 hover:text-rose-500">reset</a>
        <span class="text-slate-400">· {{ $pcaf['count'] }} complaint</span>
    @endif
</form>
