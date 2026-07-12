<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') · Monitoring Complaint & NCR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
    <script>
        // datalabels auto-register secara global; matikan default (sinkron, sebelum chart dibuat),
        // lalu aktifkan per-chart sesuai kebutuhan
        if (window.Chart && window.ChartDataLabels) {
            Chart.defaults.set('plugins.datalabels', { display: false });
        }
    </script>
    <style>
        body { font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; }
        ::-webkit-scrollbar { height: 8px; width: 8px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        /* Custom dropdown arrow agar tidak mentok ke kanan & konsisten antar-browser */
        select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1.05rem;
            padding-right: 2.5rem !important;
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800">
<div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-64 shrink-0 bg-slate-900 text-slate-200 flex flex-col sticky top-0 h-screen overflow-y-auto">
        <div class="px-5 py-5 border-b border-slate-700">
            <div class="text-xs uppercase tracking-wider text-sky-400 font-semibold">PT Wahana Bermuda Nusantara</div>
            <div class="mt-1 font-bold text-white leading-tight">Monitoring Complaint & NCR</div>
            <div class="mt-1 text-[11px] text-slate-400">Seven Tools QC · Algoritma Apriori</div>
        </div>
        @php
            $nav = [
                ['dashboard', 'Dashboard', 'M3 12l9-9 9 9M4 10v10h5v-6h6v6h5V10'],
                ['seven-tools', 'Seven Tools QC', 'M4 6h16M4 12h16M4 18h10'],
                ['apriori', 'Analisis Apriori', 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
                ['complaints.index', 'Data Complaint', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2'],
            ];
        @endphp
        <nav class="flex-1 px-3 py-4 space-y-1">
            @foreach ($nav as [$route, $label, $icon])
                @php $active = request()->routeIs(str_replace('.index','*',$route)) || request()->routeIs($route); @endphp
                <a href="{{ route($route) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ $active ? 'bg-sky-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
                    </svg>
                    {{ $label }}
                </a>
            @endforeach
        </nav>
        <div class="px-4 py-4 border-t border-slate-700">
            <div class="flex items-center gap-2 px-1 mb-3">
                <div class="w-8 h-8 rounded-full bg-sky-600 text-white flex items-center justify-center text-xs font-bold">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <div class="text-xs font-medium text-white truncate">{{ auth()->user()->name ?? 'Admin' }}</div>
                    <div class="text-[10px] text-slate-400 truncate">{{ auth()->user()->email ?? '' }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full flex items-center justify-center gap-2 text-xs text-slate-300 hover:bg-slate-800 px-3 py-2 rounded-lg border border-slate-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- Main -->
    <main class="flex-1 min-w-0">
        <header class="bg-white border-b border-slate-200 px-8 py-4 flex items-center justify-between sticky top-0 z-10">
            <div>
                <h1 class="text-lg font-semibold text-slate-900">@yield('title', 'Dashboard')</h1>
                <p class="text-xs text-slate-500">@yield('subtitle', '')</p>
            </div>
            <div class="flex items-center gap-2">
                @yield('actions')
                <a href="{{ route('complaints.create') }}"
                   class="inline-flex items-center gap-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Input Complaint
                </a>
            </div>
        </header>

        <div class="p-8">
            @if (session('success'))
                <div class="mb-6 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @yield('content')
        </div>
    </main>
</div>
@stack('scripts')
</body>
</html>
