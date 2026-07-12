@php
    $namaBulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                 7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
    $pcf = $paretoCustFilter ?? ['year'=>null,'month'=>null,'years'=>[],'count'=>0];
    $aktif = $pcf['year'] || $pcf['month'];
@endphp
<form method="GET" action="{{ url()->current() }}#paretoCustCard" class="flex flex-wrap items-center gap-2 mb-4 text-xs">
    {{-- Preserve pareto defect filter params --}}
    @if (request('pareto_year'))<input type="hidden" name="pareto_year" value="{{ request('pareto_year') }}">@endif
    @if (request('pareto_month'))<input type="hidden" name="pareto_month" value="{{ request('pareto_month') }}">@endif

    <span class="text-slate-500">Periode:</span>
    <select name="pareto_cust_year" onchange="this.form.submit()"
            class="rounded-lg border border-slate-300 px-2 py-1.5 text-xs focus:ring-sky-500 focus:border-sky-500">
        <option value="">Semua tahun</option>
        @foreach ($pcf['years'] as $y)
            <option value="{{ $y }}" @selected((int) $pcf['year'] === (int) $y)>{{ $y }}</option>
        @endforeach
    </select>
    <select name="pareto_cust_month" onchange="this.form.submit()"
            class="rounded-lg border border-slate-300 px-2 py-1.5 text-xs focus:ring-sky-500 focus:border-sky-500">
        <option value="">Semua bulan</option>
        @foreach ($namaBulan as $n => $label)
            <option value="{{ $n }}" @selected((int) $pcf['month'] === (int) $n)>{{ $label }}</option>
        @endforeach
    </select>
    @if ($aktif)
        <a href="{{ url()->current() }}{{ request('pareto_year') || request('pareto_month') ? '?pareto_year='.request('pareto_year').'&pareto_month='.request('pareto_month') : '' }}#paretoCustCard" class="text-slate-400 hover:text-rose-500">reset</a>
        <span class="text-slate-400">· {{ $pcf['count'] }} complaint</span>
    @endif
</form>
