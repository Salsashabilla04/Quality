@php use Illuminate\Support\Facades\Route; @endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') · Monitoring Complaint & NCR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
    <script>
        // datalabels auto-register secara global; matikan default (sinkron, sebelum chart dibuat),
        // lalu aktifkan per-chart sesuai kebutuhan
        if (window.Chart && window.ChartDataLabels) {
            Chart.defaults.set('plugins.datalabels', { display: false });
        }
    </script>
    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        * {
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
            box-sizing: border-box;
        }
        [x-cloak] { display: none !important; }
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

        .sidebar-logo {
            height: 40px;
            width: auto;
            object-fit: contain;
            filter: brightness(0) saturate(100%) invert(68%) sepia(72%) saturate(1200%) hue-rotate(175deg) brightness(95%);
        }

    </style>
</head>
<body class="bg-slate-100 text-slate-800">
<div x-data="{ mobileMenuOpen: false }" class="flex min-h-screen relative">
    {{-- Mobile Backdrop --}}
    <div x-show="mobileMenuOpen" @click="mobileMenuOpen = false" x-cloak class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-40 md:hidden transition-opacity" x-transition.opacity></div>

    <!-- Sidebar -->
    <aside :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
           class="fixed md:sticky top-0 left-0 z-50 w-64 shrink-0 bg-slate-900 text-slate-200 flex flex-col h-screen overflow-y-auto transition-transform duration-300 ease-in-out shadow-2xl md:shadow-none">
        <div class="px-5 py-5 border-b border-slate-700 flex items-center justify-between">
            <div>
                <img 
                src="{{ asset('images/logowb.png') }}" 
                alt="PT Wahana Bermuda Nusantara" 
                class= "sidebar-logo"
                >
                <div class="mt-1 font-bold text-white leading-tight">Monitoring Complaint & NCR</div>
            </div>
            <button @click="mobileMenuOpen = false" class="md:hidden p-1 text-slate-400 hover:text-white rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        @php
            $role = auth()->user()->role;

            if ($role === 'qa') {
                $nav = [
                    ['dashboard', 'Dashboard QA', 'M3 12l9-9 9 9M4 10v10h5v-6h6v6h5V10'],
                    ['complaints.index', 'Data Complaint & NCR', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2'],
                    ['seven-tools', 'Seven Tools QC', 'M4 6h16M4 12h16M4 18h10'],
                    ['apriori', 'Analisis Apriori', 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
                    ['defect-dictionary.index', 'Kamus Cacat QC 📚', 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                    ['customers.index', 'Daftar Customer', 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
                    ['visit.index', 'Jadwal Visit Supervisor 👁️', 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                ];
            } else {
                // Supervisor QC
                $nav = [
                    ['dashboard', 'Dashboard SPV', 'M3 12l9-9 9 9M4 10v10h5v-6h6v6h5V10'],
                    ['complaints.index', 'Validasi & Approval NCR', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2'],
                    ['seven-tools', 'Seven Tools QC', 'M4 6h16M4 12h16M4 18h10'],
                    ['apriori', 'Analisis Apriori', 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
                    ['laporan', 'Laporan Visual Eksekutif', 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['visit.index', 'Kelola Visit Customer 🚐', 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                ];
            }
        @endphp
        <nav class="flex-1 px-3 py-4 space-y-1">
            @foreach ($nav as [$route, $label, $icon])
                @php 
                    try { $exists = Route::has($route); } catch(\Exception $e) { $exists = false; }
                    if (!$exists) continue;
                    $active = request()->routeIs(str_replace('.index','*',$route)) || request()->routeIs($route); 
                @endphp
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
                <div class="w-8 h-8 rounded-full bg-sky-600 text-white flex items-center justify-center text-xs font-bold shrink-0">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="text-xs font-medium text-white truncate">{{ auth()->user()->name ?? 'User' }}</div>
                    <div class="text-[10px] text-sky-400 font-bold uppercase truncate">{{ auth()->user()->role === 'supervisor' ? 'Supervisor QC' : 'Staff QA' }}</div>
                </div>
            </div>
            <div class="space-y-1.5">
                <a href="{{ route('profile.password') }}" 
                   class="w-full flex items-center justify-center gap-2 text-xs text-slate-300 hover:text-white hover:bg-slate-800 px-3 py-2 rounded-lg border border-slate-700/80 transition {{ request()->routeIs('profile.password') ? 'bg-slate-800 text-white border-sky-500/50 ring-1 ring-sky-500/50' : '' }}">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    Ganti Password
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full flex items-center justify-center gap-2 text-xs text-slate-400 hover:text-rose-300 hover:bg-rose-950/30 px-3 py-2 rounded-lg border border-slate-800 hover:border-rose-900/50 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main -->
    <main class="flex-1 min-w-0">
        <header class="bg-white border-b border-slate-200 px-4 md:px-8 py-3.5 flex items-center justify-between sticky top-0 z-30">
            <div class="flex items-center gap-3">
                <button type="button" @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-xl text-slate-600 hover:bg-slate-100 border border-slate-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    <h1 class="text-base md:text-lg font-bold text-slate-900 leading-tight">@yield('title', 'Dashboard')</h1>
                    <p class="text-[11px] md:text-xs text-slate-500 hidden sm:block">@yield('subtitle', '')</p>
                </div>
            </div>
            <div class="flex items-center gap-2 md:gap-3">
                @php
                    $isQa = auth()->user()->role === 'qa';
                    $pendingSpvNotif = \App\Models\Complaint::where('status', 'Diproses')->where('supervisor_approval', 'Pending')->count();
                    $notifCount = $isQa 
                        ? \App\Models\Complaint::where('supervisor_approval', 'Rejected')->count()
                        : ($pendingSpvNotif + \App\Models\Complaint::where('perlu_visit', true)->count());
                @endphp
                
                {{-- Bell Notification Dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <button type="button" onclick="this.nextElementSibling.classList.toggle('hidden')" class="relative p-2 rounded-lg text-slate-500 hover:bg-slate-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if($notifCount > 0)
                            <span class="absolute top-1 right-1 w-4 h-4 bg-rose-500 text-white rounded-full text-[10px] font-bold flex items-center justify-center animate-pulse">
                                {{ $notifCount }}
                            </span>
                        @endif
                    </button>
                    
                    <div class="hidden absolute right-0 mt-2 w-80 bg-white border border-slate-200 rounded-xl shadow-xl z-30 py-2">
                        <div class="px-4 py-2 border-b border-slate-100 font-bold text-xs text-slate-700 uppercase tracking-wide flex items-center justify-between">
                            <span>Pemberitahuan Sistem</span>
                            <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-mono">{{ $notifCount }} Baru</span>
                        </div>
                        <div class="max-h-64 overflow-y-auto text-xs divide-y divide-slate-100">
                            @if($isQa)
                                @forelse(\App\Models\Complaint::where('supervisor_approval', 'Rejected')->latest()->take(5)->get() as $nc)
                                    <a href="{{ route('complaints.edit', $nc) }}" class="block p-3 hover:bg-rose-50/50 transition">
                                        <div class="font-bold text-rose-800 flex items-center justify-between">
                                            <span>⚠️ Revisi dari SPV</span>
                                            <span class="text-[9px] text-slate-400 font-normal">{{ $nc->no_customer }}</span>
                                        </div>
                                        <div class="text-slate-600 mt-1 truncate">"{{ $nc->catatan_supervisor ?: 'Silakan perbaiki data investigasi' }}"</div>
                                    </a>
                                @empty
                                    <div class="p-4 text-center text-slate-400 text-xs">Tidak ada notifikasi revisi baru.</div>
                                @endforelse
                            @else
                                @forelse(\App\Models\Complaint::where('status', 'Diproses')->where('supervisor_approval', 'Pending')->latest()->take(3)->get() as $np)
                                    <a href="{{ route('complaints.index', ['approval' => 'Pending']) }}" class="block p-3 hover:bg-amber-50/50 transition">
                                        <div class="font-bold text-amber-800 flex items-center justify-between">
                                            <span>⏳ QA Minta Validasi CAPA</span>
                                            <span class="text-[9px] text-slate-400 font-normal">{{ $np->no_customer }}</span>
                                        </div>
                                        <div class="text-slate-600 mt-1 truncate">Customer: {{ $np->nama_customer }} (Diproses QA)</div>
                                    </a>
                                @empty
                                @endforelse

                                @forelse(\App\Models\Complaint::where('perlu_visit', true)->latest()->take(3)->get() as $nv)
                                    <a href="{{ route('visit.index') }}" class="block p-3 hover:bg-purple-50/50 transition">
                                        <div class="font-bold text-purple-800 flex items-center justify-between">
                                            <span>🚐 Visit Scheduled</span>
                                            <span class="text-[9px] text-slate-400 font-normal">{{ optional($nv->tanggal_visit)->format('d/m/Y') }}</span>
                                        </div>
                                        <div class="text-slate-600 mt-1 truncate">{{ $nv->nama_customer }} — {{ $nv->catatan_visit ?: 'Kunjungan lapangan' }}</div>
                                    </a>
                                @empty
                                @endforelse

                                @if($notifCount == 0)
                                    <div class="p-4 text-center text-slate-400 text-xs">Tidak ada notifikasi baru.</div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                @yield('actions')
                @if(!View::hasSection('hide_create_btn'))
                    @can('submit-complaints')
                    <a href="{{ route('complaints.create') }}"
                       class="inline-flex items-center justify-center gap-2 
                       h-10 px-4
                       rounded-xl
                       bg-sky-600 hover:bg-sky-700
                       text-sm font-semibold text-white
                       shadow-sm
                       transition-all duration-200
                       shrink-0
                       whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span class="hidden sm:inline">Input Complaint</span>
                        <span class="sm:hidden">Input</span>
                    </a>
                    @endcan
                @endif
            </div>
        </header>

        <div class="p-4 md:p-8">
            @if (session('success'))
                <div class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm flex items-center gap-2">
                    <span>✅</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-6 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm flex items-center gap-2">
                    <span>⚠️</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            @yield('content')
        </div>
    </main>
</div>
@stack('scripts')
</body>
</html>
