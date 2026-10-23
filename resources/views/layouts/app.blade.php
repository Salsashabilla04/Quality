@php use Illuminate\Support\Facades\Route; @endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', 'Dashboard') · QC Monitoring & Apriori</title>

    <!-- Google Font: Nunito (SB Admin 2 standard font) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">

    <!-- Font Awesome Free 6.5.1 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" referrerpolicy="no-referrer" />

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#4e73df',
                            hover: '#2e59d9',
                            50: '#f0f3fd',
                            100: '#e1e7fb',
                            200: '#c5d2f8',
                            300: '#9cb5f3',
                            400: '#6c91ec',
                            500: '#4e73df',
                            600: '#3256ca',
                            700: '#224abe',
                        },
                        success: {
                            DEFAULT: '#1cc88a',
                            hover: '#17a673',
                        },
                        info: {
                            DEFAULT: '#36b9cc',
                            hover: '#2c9faf',
                        },
                        warning: {
                            DEFAULT: '#f6c23e',
                            hover: '#dda20a',
                        },
                        danger: {
                            DEFAULT: '#e74a3b',
                            hover: '#be2617',
                        },
                        secondary: '#858796',
                        light: '#f8f9fc',
                        dark: '#5a5c69',
                    },
                    fontFamily: {
                        sans: ['Nunito', 'ui-sans-serif', 'system-ui', '-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'Roboto', '"Helvetica Neue"', 'Arial', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- AlpineJS -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- Chart.js 4.4.1 & DataLabels -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
    <script>
        // Konfigurasi Standar SB Admin 2 untuk semua Chart.js
        if (window.Chart) {
            Chart.defaults.font.family = "'Nunito', -apple-system, system-ui, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif";
            Chart.defaults.font.size = 12;
            Chart.defaults.color = '#858796';

            if (window.ChartDataLabels) {
                Chart.defaults.plugins.datalabels = { display: false };
            }

            if (Chart.defaults.plugins && Chart.defaults.plugins.legend) {
                Chart.defaults.plugins.legend.labels.font = {
                    family: "'Nunito', -apple-system, system-ui, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif",
                    size: 12
                };
                Chart.defaults.plugins.legend.labels.color = '#858796';
            }

            if (Chart.defaults.plugins && Chart.defaults.plugins.tooltip) {
                Chart.defaults.plugins.tooltip.backgroundColor = 'rgb(255,255,255)';
                Chart.defaults.plugins.tooltip.bodyColor = '#858796';
                Chart.defaults.plugins.tooltip.titleMarginBottom = 8;
                Chart.defaults.plugins.tooltip.titleColor = '#6e707e';
                Chart.defaults.plugins.tooltip.titleFont = {
                    family: "'Nunito', -apple-system, system-ui, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif",
                    size: 14,
                    weight: 'bold'
                };
                Chart.defaults.plugins.tooltip.bodyFont = {
                    family: "'Nunito', -apple-system, system-ui, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif",
                    size: 12
                };
                Chart.defaults.plugins.tooltip.borderColor = '#dddfeb';
                Chart.defaults.plugins.tooltip.borderWidth = 1;
                Chart.defaults.plugins.tooltip.padding = 12;
                Chart.defaults.plugins.tooltip.displayColors = false;
                Chart.defaults.plugins.tooltip.caretPadding = 8;
            }
        }
    </script>

    <style>
        * {
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            box-sizing: border-box;
        }
        [x-cloak] { display: none !important; }
        body {
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f8f9fc;
            color: #5a5c69;
        }
        ::-webkit-scrollbar { height: 6px; width: 6px; }
        ::-webkit-scrollbar-thumb { background: #d1d3e2; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #b7b9cc; }

        /* SB Admin 2 Custom Utility Classes */
        .bg-gradient-primary {
            background-color: #4e73df;
            background-image: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
            background-size: cover;
        }
        .sidebar-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            margin: 0.75rem 1rem;
        }
        .sidebar-heading {
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.13rem;
            padding: 0.5rem 1rem 0.25rem 1rem;
        }
        .border-left-primary { border-left: 0.25rem solid #4e73df !important; }
        .border-left-success { border-left: 0.25rem solid #1cc88a !important; }
        .border-left-info { border-left: 0.25rem solid #36b9cc !important; }
        .border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
        .border-left-danger { border-left: 0.25rem solid #e74a3b !important; }
        .border-left-purple { border-left: 0.25rem solid #6f42c1 !important; }
        .border-left-indigo { border-left: 0.25rem solid #4e73df !important; }
        .border-left-orange { border-left: 0.25rem solid #fd7e14 !important; }

        .card-sb {
            background-color: #ffffff;
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.12);
        }
        .card-sb-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            padding: 0.75rem 1.25rem;
            font-weight: 700;
            color: #4e73df;
        }

        /* SB Admin 2 Standardized Badge System */
        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
            padding: 0.35em 0.65em;
            font-size: 0.75rem;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.35rem;
            transition: all 0.15s ease-in-out;
            letter-spacing: 0.02em;
        }
        .badge-pill {
            padding-right: 0.75em;
            padding-left: 0.75em;
            border-radius: 50rem;
        }
        .badge-primary   { color: #ffffff !important; background-color: #4e73df !important; }
        .badge-secondary { color: #ffffff !important; background-color: #858796 !important; }
        .badge-success   { color: #ffffff !important; background-color: #1cc88a !important; }
        .badge-info      { color: #ffffff !important; background-color: #36b9cc !important; }
        .badge-warning   { color: #212529 !important; background-color: #f6c23e !important; }
        .badge-danger    { color: #ffffff !important; background-color: #e74a3b !important; }
        .badge-purple    { color: #ffffff !important; background-color: #6f42c1 !important; }
        .badge-light     { color: #3a3b45 !important; background-color: #f8f9fc !important; border: 1px solid #dddfeb; }
        /* Custom Select No Native Arrow */
        select.no-native-arrow {
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            appearance: none !important;
            background-image: none !important;
        }
        select.no-native-arrow::-ms-expand {
            display: none !important;
        }

        /* SB Admin 2 Standardized Pagination */
        .pagination {
            display: inline-flex;
            padding-left: 0;
            list-style: none;
            border-radius: 0.35rem;
            margin: 0;
        }
        .page-item {
            display: inline;
        }
        .page-item:first-child .page-link {
            border-top-left-radius: 0.35rem;
            border-bottom-left-radius: 0.35rem;
            margin-left: 0;
        }
        .page-item:last-child .page-link {
            border-top-right-radius: 0.35rem;
            border-bottom-right-radius: 0.35rem;
        }
        .page-link {
            position: relative;
            display: block;
            padding: 0.375rem 0.75rem;
            margin-left: -1px;
            font-size: 0.8125rem;
            font-weight: 600;
            line-height: 1.25;
            color: #4e73df;
            background-color: #ffffff;
            border: 1px solid #dddfeb;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            user-select: none;
        }
        .page-link:hover {
            z-index: 2;
            color: #224abe;
            background-color: #eaecf4;
            border-color: #dddfeb;
        }
        .page-item.active .page-link {
            z-index: 3;
            color: #ffffff !important;
            background-color: #4e73df !important;
            border-color: #4e73df !important;
            font-weight: 700;
        }
        .page-item.disabled .page-link {
            color: #b7b9cc !important;
            pointer-events: none;
            cursor: not-allowed;
            background-color: #ffffff !important;
            border-color: #dddfeb !important;
            opacity: 0.65;
        }

        /* SB Admin 2 Standardized Button Design System */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-weight: 700;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            user-select: none;
            border: 1px solid transparent;
            height: 2.25rem; /* 36px (h-9) */
            padding: 0 1rem;
            font-size: 0.8125rem; /* 13px */
            line-height: 1.25rem;
            border-radius: 0.375rem; /* 6px (rounded-md) - konsisten di semua layout */
            transition: all 0.15s ease-in-out;
            cursor: pointer;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            text-decoration: none;
        }
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.12);
        }
        .btn:active {
            transform: translateY(0);
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .btn:disabled, .btn[disabled] {
            opacity: 0.65;
            pointer-events: none;
            cursor: not-allowed;
            box-shadow: none;
            transform: none;
        }

        /* Button Sizes */
        .btn-sm {
            height: 2rem; /* 32px (h-8) - konsisten dan pas dengan tabel & input */
            padding: 0 0.75rem;
            font-size: 0.75rem; /* 12px */
            line-height: 1rem;
            border-radius: 0.375rem; /* 6px (rounded-md) - seragam dengan tombol sebelahnya */
            gap: 0.375rem;
        }
        .btn-lg {
            height: 2.75rem; /* 44px */
            padding: 0 1.25rem;
            font-size: 0.9375rem; /* 15px */
            line-height: 1.5rem;
            border-radius: 0.375rem; /* 6px */
            gap: 0.625rem;
        }

        /* Button Color Variants */
        .btn-primary {
            color: #ffffff !important;
            background-color: #4e73df !important;
            border-color: #4e73df !important;
        }
        .btn-primary:hover {
            background-color: #2e59d9 !important;
            border-color: #2653d4 !important;
            color: #ffffff !important;
        }

        .btn-success {
            color: #ffffff !important;
            background-color: #1cc88a !important;
            border-color: #1cc88a !important;
        }
        .btn-success:hover {
            background-color: #17a673 !important;
            border-color: #169b6b !important;
            color: #ffffff !important;
        }

        .btn-info {
            color: #ffffff !important;
            background-color: #36b9cc !important;
            border-color: #36b9cc !important;
        }
        .btn-info:hover {
            background-color: #2c9faf !important;
            border-color: #2a96a5 !important;
            color: #ffffff !important;
        }

        .btn-warning {
            color: #ffffff !important;
            background-color: #f6c23e !important;
            border-color: #f6c23e !important;
        }
        .btn-warning:hover {
            background-color: #dda20a !important;
            border-color: #d09708 !important;
            color: #ffffff !important;
        }

        .btn-danger {
            color: #ffffff !important;
            background-color: #e74a3b !important;
            border-color: #e74a3b !important;
        }
        .btn-danger:hover {
            background-color: #be2617 !important;
            border-color: #b22415 !important;
            color: #ffffff !important;
        }

        .btn-secondary {
            color: #ffffff !important;
            background-color: #858796 !important;
            border-color: #858796 !important;
        }
        .btn-secondary:hover {
            background-color: #717384 !important;
            border-color: #6b6d7d !important;
            color: #ffffff !important;
        }

        .btn-light {
            color: #3a3b45 !important;
            background-color: #f8f9fc !important;
            border-color: #dddfeb !important;
        }
        .btn-light:hover {
            background-color: #eaecf4 !important;
            border-color: #d1d3e2 !important;
            color: #3a3b45 !important;
        }

        /* Outline Buttons */
        .btn-outline-primary {
            color: #4e73df !important;
            background-color: transparent !important;
            border-color: #4e73df !important;
        }
        .btn-outline-primary:hover {
            color: #ffffff !important;
            background-color: #4e73df !important;
        }

        .btn-outline-secondary {
            color: #6e707e !important;
            background-color: transparent !important;
            border-color: #d1d3e2 !important;
        }
        .btn-outline-secondary:hover {
            color: #3a3b45 !important;
            background-color: #eaecf4 !important;
            border-color: #b7b9cc !important;
        }

        .btn-outline-danger {
            color: #e74a3b !important;
            background-color: transparent !important;
            border-color: #e74a3b !important;
        }
        .btn-outline-danger:hover {
            color: #ffffff !important;
            background-color: #e74a3b !important;
        }

        /* Custom dropdown arrow agar konsisten */
        select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236e707e' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1rem;
            padding-right: 2.25rem !important;
        }
    </style>
</head>
<body class="bg-[#f8f9fc] text-[#5a5c69]">
<div x-data="{ mobileMenuOpen: false, userMenuOpen: false, notifOpen: false }" class="flex min-h-screen relative">
    {{-- Mobile Backdrop --}}
    <div x-show="mobileMenuOpen" @click="mobileMenuOpen = false" x-cloak class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-40 md:hidden transition-opacity" x-transition.opacity></div>

    <!-- Sidebar (SB Admin 2 Gradient) -->
    <aside :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
           class="fixed md:sticky top-0 left-0 z-50 w-64 shrink-0 bg-gradient-primary text-white flex flex-col h-screen overflow-y-auto transition-transform duration-300 ease-in-out shadow-xl md:shadow-none">
        
        <!-- Sidebar Brand -->
        <div class="px-4 py-3.5 flex items-center justify-between border-b border-white/15">
            <a href="{{ route('dashboard') }}" class="flex flex-col gap-1">
                <img src="{{ asset('images/logowb.png') }}" alt="WberPack" class="h-12 w-auto max-w-[200px] object-contain brightness-0 invert drop-shadow-xs">
            </a>
            <button @click="mobileMenuOpen = false" class="md:hidden p-1 text-white/70 hover:text-white rounded-lg">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        @php
            $role = auth()->user()->role;

            if ($role === 'qa') {
                $navSections = [
                    [
                        'heading' => 'CORE',
                        'items' => [
                            ['dashboard', 'Dashboard QA', 'fa-tachometer-alt'],
                        ]
                    ],
                    [
                        'heading' => 'QUALITY ASSURANCE',
                        'items' => [
                            ['complaints.index', 'Data Complaint & NCR', 'fa-clipboard-list'],
                            ['seven-tools', 'Seven Tools QC', 'fa-tools'],
                            ['apriori', 'Analisis Apriori', 'fa-project-diagram'],
                        ]
                    ],
                    [
                        'heading' => 'MASTER & VISIT',
                        'items' => [
                            ['defect-dictionary.index', 'Kamus Cacat QC', 'fa-book'],
                            ['customers.index', 'Daftar Customer', 'fa-users'],
                            ['visit.index', 'Jadwal Visit Supervisor', 'fa-calendar-alt'],
                        ]
                    ],
                ];
            } else {
                // Supervisor QC
                $navSections = [
                    [
                        'heading' => 'CORE',
                        'items' => [
                            ['dashboard', 'Dashboard SPV', 'fa-tachometer-alt'],
                        ]
                    ],
                    [
                        'heading' => 'VALIDASI & ANALISIS',
                        'items' => [
                            ['complaints.index', 'Validasi & Approval NCR', 'fa-clipboard-check'],
                            ['seven-tools', 'Seven Tools QC', 'fa-tools'],
                            ['apriori', 'Analisis Apriori', 'fa-project-diagram'],
                        ]
                    ],
                    [
                        'heading' => 'MANAJEMEN VISIT',
                        'items' => [
                            ['visit.index', 'Kelola Visit Customer', 'fa-truck'],
                        ]
                    ],
                ];
            }
        @endphp

        <!-- Sidebar Navigation Items -->
        <nav class="flex-1 py-2">
            @foreach ($navSections as $secIndex => $section)
                @if ($secIndex > 0)
                    <div class="sidebar-divider"></div>
                @endif
                <div class="sidebar-heading">{{ $section['heading'] }}</div>

                <div class="space-y-0.5 px-2">
                    @foreach ($section['items'] as [$route, $label, $icon])
                        @php 
                            try { $exists = Route::has($route); } catch(\Exception $e) { $exists = false; }
                            if (!$exists) continue;
                            $active = request()->routeIs(str_replace('.index','*',$route)) || request()->routeIs($route); 
                        @endphp
                        <a href="{{ route($route) }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded text-xs md:text-sm font-semibold transition-all duration-150
                                  {{ $active ? 'bg-white/20 text-white font-extrabold shadow-sm' : 'text-white/80 hover:text-white hover:bg-white/10' }}">
                            <i class="fas {{ $icon }} fa-fw text-sm {{ $active ? 'text-white' : 'text-white/70' }}"></i>
                            <span class="truncate">{{ $label }}</span>
                        </a>
                    @endforeach
                </div>
            @endforeach
            <div class="sidebar-divider"></div>
        </nav>

    </aside>

    <!-- Main Content Wrapper -->
    <main class="flex-1 min-w-0 flex flex-col min-h-screen">
        <!-- Topbar Navbar (SB Admin 2 White Topbar) -->
        <header class="bg-white shadow mb-4 sticky top-0 z-30 px-4 md:px-8 py-3 flex items-center justify-between border-b border-gray-200">
            <div class="flex items-center gap-3">
                <button type="button" @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition">
                    <i class="fas fa-bars text-base"></i>
                </button>
                <div>
                    <h1 class="text-base md:text-xl font-bold text-gray-800 leading-tight">@yield('title', 'Dashboard')</h1>
                    <p class="text-[11px] md:text-xs text-gray-500 hidden sm:block">@yield('subtitle', '')</p>
                </div>
            </div>

            <div class="flex items-center gap-2 md:gap-4">
                @php
                    $isQa = auth()->user()->role === 'qa';
                    $pendingSpvNotif = \App\Models\Complaint::where('status', 'Diproses')->where('supervisor_approval', 'Pending')->count();
                    $notifCount = $isQa 
                        ? \App\Models\Complaint::where('supervisor_approval', 'Rejected')->count()
                        : ($pendingSpvNotif + \App\Models\Complaint::where('perlu_visit', true)->count());
                @endphp
                
                {{-- SB Admin 2 Notification Center (Bell) --}}
                <div class="relative" x-data="{ open: false }">
                    <button type="button" @click="open = !open" @click.away="open = false" class="relative p-2 text-gray-400 hover:text-gray-600 transition focus:outline-none">
                        <i class="fas fa-bell text-lg"></i>
                        @if($notifCount > 0)
                            <span class="absolute top-0 right-0 bg-[#e74a3b] text-white rounded-full text-[9px] font-extrabold px-1.5 py-0.5 leading-none shadow-sm animate-pulse">
                                {{ $notifCount }}
                            </span>
                        @endif
                    </button>
                    
                    <div x-show="open" x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded-lg shadow-xl z-50 overflow-hidden">
                        
                        <div class="bg-[#4e73df] px-4 py-2.5 font-extrabold text-xs text-white uppercase tracking-wider flex items-center justify-between">
                            <span>ALERTS CENTER</span>
                            <span class="text-[10px] bg-white/20 text-white px-2 py-0.5 rounded font-mono">{{ $notifCount }} Baru</span>
                        </div>
                        
                        <div class="max-h-64 overflow-y-auto text-xs divide-y divide-gray-100">
                            @if($isQa)
                                @forelse(\App\Models\Complaint::where('supervisor_approval', 'Rejected')->latest()->take(5)->get() as $nc)
                                    <a href="{{ route('complaints.edit', $nc) }}" class="flex items-center gap-3 p-3 hover:bg-rose-50/50 transition">
                                        <div class="w-8 h-8 rounded-full bg-[#e74a3b] text-white flex items-center justify-center shrink-0">
                                            <i class="fas fa-undo-alt text-xs"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="text-[10px] text-gray-400 font-semibold">{{ $nc->no_customer }}</div>
                                            <div class="font-bold text-gray-800 truncate">Revisi dari SPV</div>
                                            <div class="text-gray-500 text-[11px] truncate">"{{ $nc->catatan_supervisor ?: 'Silakan perbaiki data investigasi' }}"</div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="p-4 text-center text-gray-400 text-xs">Tidak ada notifikasi revisi baru.</div>
                                @endforelse
                            @else
                                @forelse(\App\Models\Complaint::where('status', 'Diproses')->where('supervisor_approval', 'Pending')->latest()->take(3)->get() as $np)
                                    <a href="{{ route('complaints.index', ['approval' => 'Pending']) }}" class="flex items-center gap-3 p-3 hover:bg-amber-50/50 transition">
                                        <div class="w-8 h-8 rounded-full bg-[#f6c23e] text-white flex items-center justify-center shrink-0">
                                            <i class="fas fa-clock text-xs"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="text-[10px] text-gray-400 font-semibold">{{ $np->no_customer }}</div>
                                            <div class="font-bold text-gray-800 truncate">QA Minta Validasi CAPA</div>
                                            <div class="text-gray-500 text-[11px] truncate">{{ $np->nama_customer }} (Diproses QA)</div>
                                        </div>
                                    </a>
                                @empty
                                @endforelse

                                @forelse(\App\Models\Complaint::where('perlu_visit', true)->latest()->take(3)->get() as $nv)
                                    <a href="{{ route('visit.index') }}" class="flex items-center gap-3 p-3 hover:bg-indigo-50/50 transition">
                                        <div class="w-8 h-8 rounded-full bg-[#36b9cc] text-white flex items-center justify-center shrink-0">
                                            <i class="fas fa-truck text-xs"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="text-[10px] text-gray-400 font-semibold">{{ optional($nv->tanggal_visit)->format('d/m/Y') }}</div>
                                            <div class="font-bold text-gray-800 truncate">Visit Scheduled</div>
                                            <div class="text-gray-500 text-[11px] truncate">{{ $nv->nama_customer }}</div>
                                        </div>
                                    </a>
                                @empty
                                @endforelse

                                @if($notifCount == 0)
                                    <div class="p-4 text-center text-gray-400 text-xs">Tidak ada notifikasi baru.</div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Action Buttons Group (Export/Import + Input Complaint) --}}
                <div class="flex items-center gap-2">
                    @yield('actions')

                    {{-- Input Complaint Button --}}
                    @if(!View::hasSection('hide_create_btn'))
                        @can('submit-complaints')
                        <a href="{{ route('complaints.create') }}" class="btn btn-primary btn-sm shrink-0 whitespace-nowrap">
                            <i class="fas fa-plus fa-sm"></i>
                            <span class="hidden sm:inline">Input Complaint</span>
                            <span class="sm:hidden">Input</span>
                        </a>
                        @endcan
                    @endif
                </div>

                {{-- Vertical Topbar Divider --}}
                <div class="h-8 border-l border-gray-200 mx-1 hidden sm:block"></div>

                {{-- User Profile Dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <button type="button" @click="open = !open" @click.away="open = false" class="flex items-center gap-2.5 focus:outline-none">
                        <div class="text-right hidden sm:block leading-tight">
                            <div class="text-xs font-bold text-gray-700">{{ auth()->user()->name ?? 'User' }}</div>
                            <div class="text-[10px] font-semibold text-gray-400 uppercase">{{ auth()->user()->role === 'supervisor' ? 'Supervisor QC' : 'Staff QA' }}</div>
                        </div>
                        <div class="w-9 h-9 rounded-full bg-[#4e73df] text-white flex items-center justify-center font-bold text-sm shadow-sm ring-2 ring-blue-100">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </div>
                    </button>

                    <div x-show="open" x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-xl z-50 py-1 text-xs">
                        
                        <div class="px-4 py-2 border-b border-gray-100 text-gray-500 font-semibold">
                            <div class="font-bold text-gray-800">{{ auth()->user()->name ?? 'User' }}</div>
                            <div class="text-[10px] text-[#4e73df] uppercase font-bold">{{ auth()->user()->role === 'supervisor' ? 'Supervisor QC' : 'Staff QA' }}</div>
                        </div>

                        <a href="{{ route('profile.password') }}" class="flex items-center gap-2 px-4 py-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition">
                            <i class="fas fa-key fa-sm fa-fw text-gray-400"></i>
                            <span>Ganti Password</span>
                        </a>

                        <div class="border-t border-gray-100 my-1"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="w-full text-left flex items-center gap-2 px-4 py-2 text-[#e74a3b] hover:bg-rose-50 transition">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw text-[#e74a3b]"></i>
                                <span>Keluar</span>
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </header>

        <!-- Main Content Area -->
        <div class="flex-1 px-4 md:px-8 pb-8">
            @if (session('success'))
                <div class="mb-4 rounded bg-white border-l-4 border-l-[#1cc88a] shadow-sm text-gray-700 px-4 py-3 text-sm flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <i class="fas fa-check-circle text-[#1cc88a] text-lg"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 rounded bg-white border-l-4 border-l-[#e74a3b] shadow-sm text-gray-700 px-4 py-3 text-sm flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <i class="fas fa-exclamation-triangle text-[#e74a3b] text-lg"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @yield('content')
        </div>

        <!-- Sticky Footer (SB Admin 2) -->
        <footer class="bg-white py-4 mt-auto border-t border-gray-200">
            <div class="container mx-auto px-4 text-center text-xs text-gray-500 font-semibold">
                <span>Copyright &copy; PT Wahana Bermuda Nusantara {{ date('Y') }}</span>
            </div>
        </footer>
    </main>
</div>
@stack('scripts')
</body>
</html>
