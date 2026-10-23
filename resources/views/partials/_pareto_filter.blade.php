@php
    $namaBulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                 7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
    $pf = $paretoFilter ?? ['year'=>null,'month'=>null,'years'=>[],'count'=>0];
    $aktif = $pf['year'] || $pf['month'];
@endphp
<form method="GET" action="{{ url()->current() }}#paretoCard" class="flex flex-wrap items-center gap-2 mb-3 text-xs">
    {{-- Preserve other filter params --}}
    @foreach (['cs_mode','pareto_detail_year','pareto_detail_month','pareto_cust_year','pareto_cust_month','pareto_cause_year','pareto_cause_month'] as $p)
        @if (request($p))<input type="hidden" name="{{ $p }}" value="{{ request($p) }}">@endif
    @endforeach

    <span class="text-[#858796] font-semibold"><i class="fas fa-filter text-[#858796] mr-1"></i>Periode:</span>
    <select name="pareto_year" onchange="this.form.submit()"
            class="rounded border border-[#d1d3e2] text-[#5a5c69] px-2.5 h-8 text-xs font-semibold focus:border-[#4e73df] bg-white cursor-pointer shadow-none">
        <option value="">Semua tahun</option>
        @foreach ($pf['years'] as $y)
            <option value="{{ $y }}" @selected((int) $pf['year'] === (int) $y)>{{ $y }}</option>
        @endforeach
    </select>
    <select name="pareto_month" onchange="this.form.submit()"
            class="rounded border border-[#d1d3e2] text-[#5a5c69] px-2.5 h-8 text-xs font-semibold focus:border-[#4e73df] bg-white cursor-pointer shadow-none">
        <option value="">Semua bulan</option>
        @foreach ($namaBulan as $n => $label)
            <option value="{{ $n }}" @selected((int) $pf['month'] === (int) $n)>{{ $label }}</option>
        @endforeach
    </select>

    @if ($aktif)
        <a href="{{ url()->current() }}?{{ http_build_query(request()->except(['pareto_year','pareto_month'])) }}#paretoCard" class="btn btn-outline-danger btn-sm h-8 rounded px-2.5 text-xs inline-flex items-center gap-1"><i class="fas fa-undo"></i> Reset</a>
        <span class="badge badge-light border text-[#858796] text-[11px] font-semibold">{{ $pf['count'] }} complaint</span>
    @endif
</form>
