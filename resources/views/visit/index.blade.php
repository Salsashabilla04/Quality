@extends('layouts.app')

@section('hide_create_btn', true)

@php
    $role = auth()->user()->role;
    $isSpv = $role === 'supervisor';
    $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    // Calendar matrix calculation
    $firstDayOfMonth = sprintf('%04d-%02d-01', $tahun, $bulan);
    $daysInMonth = date('t', strtotime($firstDayOfMonth));
    $dayOfWeek = date('w', strtotime($firstDayOfMonth)); // 0 = Sun, 6 = Sat
    
    $totalTerjadwal  = $totalTerjadwalAll;
    $totalSelesai    = $totalSelesaiAll;
@endphp

@section('title', $isSpv ? 'Kelola Visit Customer' : 'Jadwal Kunjungan Supervisor')
@section('subtitle', $isSpv ? 'Pengelolaan & agenda kunjungan lapangan Supervisor ke lokasi customer' : 'Daftar jadwal kunjungan yang telah diagendakan oleh Supervisor QC')

@section('actions')
    @if($isSpv)
        <button type="button" onclick="document.getElementById('addVisitModal').classList.remove('hidden')"
                class="btn btn-primary btn-sm shadow-sm shrink-0 whitespace-nowrap">
            <i class="fas fa-plus fa-sm"></i>
            <span>Tambah Jadwal Visit</span>
        </button>
    @endif
@endsection

@section('content')
<div x-data="{ 
    viewMode: 'list', 
    timeFilter: '{{ $activeTab === 'done' ? 'done' : 'upcoming' }}', 
    searchQuery: '{{ addslashes($search) }}',
    matchesFilter(v) {
        if (!this.searchQuery.trim()) return true;
        const q = this.searchQuery.toLowerCase();
        const cust = (v.nama_customer || '').toLowerCase();
        const no = (v.no_customer || '').toLowerCase();
        const note = (v.catatan_visit || '').toLowerCase();
        return cust.includes(q) || no.includes(q) || note.includes(q);
    }
}" class="space-y-6">

    {{-- 1. SB Admin 2 KPI Summary Cards (Hanya Tampil untuk Supervisor) --}}
    @if($isSpv)
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Card 1: Total Kunjungan --}}
        <div class="card-sb border border-[#e3e6f0] border-left-primary rounded shadow-sm p-4 bg-white">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-bold text-[#4e73df] uppercase tracking-wider mb-1">Total Agenda Visit</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $totalAllVisits }}</div>
                    <div class="text-[11px] text-gray-500 mt-0.5">Semua riwayat kunjungan</div>
                </div>
                <div class="w-10 h-10 rounded-full bg-blue-50 text-[#4e73df] flex items-center justify-center text-lg shrink-0">
                    <i class="fas fa-clipboard-list"></i>
                </div>
            </div>
        </div>

        {{-- Card 2: Kunjungan Mendatang --}}
        <div class="card-sb border border-[#e3e6f0] border-left-warning rounded shadow-sm p-4 bg-white">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-bold text-[#f6c23e] uppercase tracking-wider mb-1">Kunjungan Mendatang</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $totalTerjadwal }}</div>
                    <div class="text-[11px] text-gray-500 mt-0.5">Perlu dilaksanakan (Aktif)</div>
                </div>
                <div class="w-10 h-10 rounded-full bg-amber-50 text-[#f6c23e] flex items-center justify-center text-lg shrink-0">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>

        {{-- Card 3: Visit Selesai --}}
        <div class="card-sb border border-[#e3e6f0] border-left-success rounded shadow-sm p-4 bg-white">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-bold text-[#1cc88a] uppercase tracking-wider mb-1">Visit Selesai (Visited)</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $totalSelesai }}</div>
                    <div class="text-[11px] text-gray-500 mt-0.5">Telah selesai dieksekusi</div>
                </div>
                <div class="w-10 h-10 rounded-full bg-emerald-50 text-[#1cc88a] flex items-center justify-center text-lg shrink-0">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        {{-- Card 4: Periode Kalender --}}
        <div class="card-sb border border-[#e3e6f0] border-left-info rounded shadow-sm p-4 bg-white">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-bold text-[#36b9cc] uppercase tracking-wider mb-1">Periode Kalender</div>
                    <div class="text-lg font-bold text-gray-800">{{ $namaBulan[$bulan] }} {{ $tahun }}</div>
                    <div class="text-[11px] text-gray-500 mt-0.5">Bulan acuan aktif</div>
                </div>
                <div class="w-10 h-10 rounded-full bg-cyan-50 text-[#36b9cc] flex items-center justify-center text-lg shrink-0">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- 2. Card SB Container Utama (Clean Look SB Admin 2) --}}
    <div class="card-sb border border-[#e3e6f0] rounded shadow-sm">
        {{-- Card Header: Title + View Switcher --}}
        <div class="card-sb-header py-3 px-4 bg-[#f8f9fc] border-b border-[#e3e6f0] flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <span class="w-7 h-7 rounded bg-[#4e73df] text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs">
                    <i class="fas fa-calendar-check text-xs"></i>
                </span>
                <div>
                    <h6 class="m-0 font-bold text-[#4e73df] text-sm leading-tight">
                        {{ $isSpv ? 'Kelola Agenda Visit Customer' : 'Jadwal Kunjungan Supervisor QC' }}
                    </h6>
                    <small class="text-xs text-[#858796] block mt-0.5">
                        {{ $isSpv ? 'Koordinasi, pemantauan status, dan eksekusi kunjungan ke lokasi customer' : 'Jadwal kunjungan lapangan Supervisor untuk koordinasi tim QA dan pelanggan' }}
                    </small>
                </div>
            </div>

            {{-- View Mode Switcher (List vs Kalender) --}}
            <div class="inline-flex rounded border border-[#d1d3e2] bg-white p-0.5 text-xs font-semibold shrink-0 shadow-2xs">
                <button type="button" @click="viewMode = 'list'"
                        :class="viewMode === 'list' ? 'bg-[#4e73df] text-white font-bold shadow-2xs' : 'text-[#5a5c69] hover:bg-gray-100'"
                        class="px-3 py-1.5 rounded transition flex items-center gap-1.5 cursor-pointer">
                    <i class="fas fa-list fa-sm"></i>
                    <span>Tampilan Tabel</span>
                </button>
                <button type="button" @click="viewMode = 'calendar'"
                        :class="viewMode === 'calendar' ? 'bg-[#4e73df] text-white font-bold shadow-2xs' : 'text-[#5a5c69] hover:bg-gray-100'"
                        class="px-3 py-1.5 rounded transition flex items-center gap-1.5 cursor-pointer">
                    <i class="fas fa-calendar-alt fa-sm"></i>
                    <span>Tampilan Kalender</span>
                </button>
            </div>
        </div>

        {{-- Filter & Search Tabs (List Mode) --}}
        <div x-show="viewMode === 'list'" class="bg-[#f8f9fc] border-b border-[#e3e6f0] px-4 pt-3 flex flex-col md:flex-row md:items-end md:justify-between gap-3">
            <ul class="flex -mb-px text-sm" role="tablist">
                <li>
                    <button type="button" @click="timeFilter = 'upcoming'"
                            :class="timeFilter === 'upcoming'
                                ? 'bg-white border-[#e3e6f0] border-b-white text-[#4e73df] font-bold'
                                : 'border-transparent text-[#858796] hover:text-[#4e73df] hover:border-[#eaecf4]'"
                            class="px-4 py-2 border rounded-t-md transition-colors flex items-center gap-1.5 cursor-pointer text-xs">
                        <span>Terjadwal</span>
                        @if($totalTerjadwal > 0)
                            <span class="w-2 h-2 rounded-full bg-rose-500 inline-block"></span>
                        @endif
                    </button>
                </li>
                <li>
                    <button type="button" @click="timeFilter = 'done'"
                            :class="timeFilter === 'done'
                                ? 'bg-white border-[#e3e6f0] border-b-white text-[#4e73df] font-bold'
                                : 'border-transparent text-[#858796] hover:text-[#4e73df] hover:border-[#eaecf4]'"
                            class="px-4 py-2 border rounded-t-md transition-colors flex items-center gap-1.5 cursor-pointer text-xs">
                        <span>Selesai</span>
                    </button>
                </li>
            </ul>

            <form method="GET" action="{{ route('visit.index') }}" class="relative w-full md:w-72 pb-2.5">
                <input type="hidden" name="tab" :value="timeFilter">
                <input type="text" name="q" value="{{ $search }}" placeholder="Cari customer, no komplain..."
                       class="w-full h-8 pl-3 pr-12 text-xs bg-white border border-[#d1d3e2] rounded focus:outline-none focus:border-[#4e73df] text-gray-700 placeholder-gray-400">
                @if($search)
                    <a href="{{ route('visit.index', ['tab' => $activeTab]) }}" class="absolute right-7 top-2 text-gray-400 hover:text-rose-500 text-xs" title="Reset Pencarian">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
                <button type="submit" class="absolute right-2.5 top-2 text-gray-400 hover:text-[#4e73df] text-xs">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>

        {{-- ================================================================= --}}
        {{-- VIEW 1: DAFTAR KUNJUNGAN (CLEAN LOOK TABLE)                      --}}
        {{-- ================================================================= --}}
        <div x-show="viewMode === 'list'">
            {{-- TAB PANEL 1: AGENDA TERJADWAL --}}
            <div x-show="timeFilter === 'upcoming'" class="overflow-x-auto pb-28">
                <table class="w-full text-xs table-fixed">
                    <colgroup>
                        @if($isSpv)
                            <col style="width: 6%">
                            <col style="width: 17%">
                            <col style="width: 13%">
                            <col style="width: 16%">
                            <col style="width: 24%">
                            <col style="width: 16%">
                            <col style="width: 8%">
                        @else
                            <col style="width: 6%">
                            <col style="width: 18%">
                            <col style="width: 14%">
                            <col style="width: 18%">
                            <col style="width: 26%">
                            <col style="width: 18%">
                        @endif
                    </colgroup>
                    <thead>
                        <tr class="bg-[#f8f9fc] text-[#4e73df] border-b border-[#e3e6f0] text-xs font-bold tracking-wider">
                            <th class="pl-5 pr-2 py-3 text-center">No</th>
                            <th class="px-3 py-3 text-left">Tanggal &amp; Waktu Visit</th>
                            <th class="px-3 py-3 text-left">Customer</th>
                            <th class="px-3 py-3 text-left">Penanggung Jawab</th>
                            <th class="px-3 py-3 text-left">Catatan Kunjungan</th>
                            <th class="px-3 py-3 text-left">Status</th>
                            @if($isSpv)
                                <th class="pl-2 pr-5 py-3 text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e3e6f0] text-gray-700 bg-white">
                        @forelse($terjadwalVisits as $index => $v)
                            <tr x-show="matchesFilter({{ json_encode($v) }})" class="hover:bg-[#f8f9fc] transition-colors">
                                <td class="pl-5 pr-2 py-3 text-center font-bold text-gray-500 align-middle">
                                    {{ $terjadwalVisits->firstItem() ? ($terjadwalVisits->firstItem() + $index) : ($index + 1) }}
                                </td>
                                <td class="px-3 py-3 text-left whitespace-nowrap align-middle">
                                    @if($v->tanggal_visit)
                                        <div class="font-bold text-gray-800">{{ $v->tanggal_visit->format('d') }} {{ $namaBulan[(int) $v->tanggal_visit->format('n')] }} {{ $v->tanggal_visit->format('Y') }}</div>
                                        <div class="text-[11px] text-gray-500 mt-0.5">{{ $v->jam_visit ? substr($v->jam_visit, 0, 5).' WIB' : 'Jam belum ditentukan' }}</div>
                                    @else
                                        <div class="text-gray-500">Belum ditentukan</div>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-left align-middle">
                                    <div class="font-bold text-gray-800 truncate" title="{{ $v->nama_customer }}">{{ $v->nama_customer }}</div>
                                    <div class="text-[11px] font-mono text-gray-500 mt-0.5">{{ $v->no_customer }}</div>
                                </td>
                                <td class="px-3 py-3 text-left whitespace-nowrap align-middle">
                                    <span class="inline-flex items-center gap-1.5 font-semibold text-gray-700">
                                        <i class="fas fa-user-shield text-[#4e73df] text-xs"></i>
                                        <span>Supervisor QC</span>
                                    </span>
                                </td>
                                <td class="px-3 py-3 align-middle text-gray-700 leading-relaxed break-words">
                                    @php
                                        $rawNote = $v->catatan_visit ?: 'Koordinasi & verifikasi langsung penanganan kualitas di lokasi customer';
                                        $hasStamp = preg_match('/^(.*?)(?:[\s\n]*)(\[Selesai Kunjungan pada [^\]]+\])(.*)$/s', $rawNote, $matches);
                                    @endphp
                                    @if($hasStamp)
                                        <div class="whitespace-pre-line text-gray-800">{{ trim($matches[1]) ?: 'Koordinasi & verifikasi langsung penanganan kualitas di lokasi customer' }}</div>
                                        <div class="text-[10px] text-emerald-600 font-semibold mt-1 flex items-center gap-1">
                                            <i class="fas fa-check-circle text-[10px]"></i>
                                            <span class="whitespace-nowrap">Selesai {{ trim(str_replace(['[Selesai Kunjungan pada', ']'], '', $matches[2])) }}</span>
                                        </div>
                                        @if(!empty(trim($matches[3])))
                                             <div class="whitespace-pre-line text-xs text-gray-600 mt-1">{{ trim($matches[3]) }}</div>
                                        @endif
                                    @else
                                        <div class="whitespace-pre-line">{{ $rawNote }}</div>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-left whitespace-nowrap align-middle">
                                    <span class="text-xs font-semibold text-amber-600 inline-flex items-center gap-1.5">
                                        <i class="fas fa-clock text-[11px]"></i> Terjadwal
                                    </span>
                                </td>
                                @if($isSpv)
                                <td class="pl-2 pr-5 py-3 text-center whitespace-nowrap align-middle">
                                    <div class="relative inline-block text-left" x-data="{ open: false }">
                                        <button type="button" @click="open = !open" @click.outside="open = false"
                                                title="Opsi Aksi"
                                                class="w-7 h-7 rounded-full inline-flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition cursor-pointer">
                                            <i class="fas fa-ellipsis-v text-xs"></i>
                                        </button>

                                        <div x-show="open" x-cloak
                                             x-transition:enter="transition ease-out duration-100"
                                             x-transition:enter-start="transform opacity-0 scale-95"
                                             x-transition:enter-end="transform opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-75"
                                             x-transition:leave-start="transform opacity-100 scale-100"
                                             x-transition:leave-end="transform opacity-0 scale-95"
                                             class="absolute right-0 top-full mt-1 w-40 bg-white rounded-lg shadow-xl border border-[#e3e6f0] py-1 z-30 divide-y divide-gray-100 text-left text-xs font-medium">
                                            <div class="py-1">
                                                {{-- 1. Tandai Selesai --}}
                                                <form action="{{ route('visit.done', $v) }}" method="POST" onsubmit="return confirm('Tandai kunjungan lapangan ke {{ $v->nama_customer }} telah selesai?')">
                                                    @csrf @method('PATCH')
                                                    <button type="submit"
                                                            class="w-full text-left px-3 py-1.5 text-gray-700 hover:bg-gray-50 hover:text-emerald-600 flex items-center gap-2.5 transition cursor-pointer">
                                                        <i class="fas fa-check text-emerald-500 w-3 text-center"></i>
                                                        <span>Tandai Selesai</span>
                                                    </button>
                                                </form>

                                                {{-- 2. Edit Jadwal --}}
                                                <button type="button"
                                                        @click="open = false; openEditVisitModal({{ json_encode($v) }})"
                                                        class="w-full text-left px-3 py-1.5 text-gray-700 hover:bg-gray-50 hover:text-[#4e73df] flex items-center gap-2.5 transition cursor-pointer">
                                                    <i class="fas fa-pen text-[#4e73df] w-3 text-center"></i>
                                                    <span>Edit Jadwal</span>
                                                </button>
                                            </div>

                                            {{-- 3. Hapus Jadwal --}}
                                            <div class="py-1">
                                                <form action="{{ route('visit.destroy', $v) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus agenda visit ke {{ $v->nama_customer }}?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                            class="w-full text-left px-3 py-1.5 text-rose-600 hover:bg-rose-50 flex items-center gap-2.5 transition cursor-pointer">
                                                        <i class="fas fa-trash text-rose-500 w-3 text-center"></i>
                                                        <span>Hapus Jadwal</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isSpv ? 7 : 6 }}" class="px-4 py-8 text-center text-gray-400">
                                    <i class="fas fa-check-circle text-3xl text-[#1cc88a] mb-2 block opacity-75"></i>
                                    <span class="text-xs font-semibold text-gray-700 block">Tidak ada agenda visit yang sedang terjadwal.</span>
                                    <span class="text-[11px] text-gray-500">Semua kunjungan telah selesai dilaksanakan atau belum ada agenda baru.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($terjadwalVisits->total() > 0)
                    <div class="px-4 py-3 border-t border-slate-200 bg-white">
                        {{ $terjadwalVisits->appends(['tab' => 'upcoming'])->links() }}
                    </div>
                @endif
            </div>

            {{-- TAB PANEL 2: RIWAYAT KUNJUNGAN SELESAI --}}
            <div x-show="timeFilter === 'done'" x-cloak class="overflow-x-auto pb-28">
                <table class="w-full text-xs table-fixed">
                    <colgroup>
                        @if($isSpv)
                            <col style="width: 6%">
                            <col style="width: 17%">
                            <col style="width: 13%">
                            <col style="width: 16%">
                            <col style="width: 24%">
                            <col style="width: 16%">
                            <col style="width: 8%">
                        @else
                            <col style="width: 6%">
                            <col style="width: 18%">
                            <col style="width: 14%">
                            <col style="width: 18%">
                            <col style="width: 26%">
                            <col style="width: 18%">
                        @endif
                    </colgroup>
                    <thead>
                        <tr class="bg-[#f8f9fc] text-[#4e73df] border-b border-[#e3e6f0] text-xs font-bold tracking-wider">
                            <th class="pl-5 pr-2 py-3 text-center">No</th>
                            <th class="px-3 py-3 text-left">Tanggal &amp; Waktu Visit</th>
                            <th class="px-3 py-3 text-left">Customer</th>
                            <th class="px-3 py-3 text-left">Penanggung Jawab</th>
                            <th class="px-3 py-3 text-left">Catatan Kunjungan</th>
                            <th class="px-3 py-3 text-left">Status</th>
                            @if($isSpv)
                                <th class="pl-2 pr-5 py-3 text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e3e6f0] text-gray-700 bg-white">
                        @forelse($selesaiVisits as $index => $v)
                            <tr x-show="matchesFilter({{ json_encode($v) }})" class="hover:bg-[#f8f9fc] transition-colors">
                                <td class="pl-5 pr-2 py-3 text-center font-bold text-gray-500 align-middle">
                                    {{ $selesaiVisits->firstItem() ? ($selesaiVisits->firstItem() + $index) : ($index + 1) }}
                                </td>
                                <td class="px-3 py-3 text-left whitespace-nowrap align-middle">
                                    @if($v->tanggal_visit)
                                        <div class="font-bold text-gray-800">{{ $v->tanggal_visit->format('d') }} {{ $namaBulan[(int) $v->tanggal_visit->format('n')] }} {{ $v->tanggal_visit->format('Y') }}</div>
                                        <div class="text-[11px] text-gray-500 mt-0.5">{{ $v->jam_visit ? substr($v->jam_visit, 0, 5).' WIB' : 'Jam belum ditentukan' }}</div>
                                    @else
                                        <div class="text-gray-500">Belum ditentukan</div>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-left align-middle">
                                    <div class="font-bold text-gray-800 truncate" title="{{ $v->nama_customer }}">{{ $v->nama_customer }}</div>
                                    <div class="text-[11px] font-mono text-gray-500 mt-0.5">{{ $v->no_customer }}</div>
                                </td>
                                <td class="px-3 py-3 text-left whitespace-nowrap align-middle">
                                    <span class="inline-flex items-center gap-1.5 font-semibold text-gray-700">
                                        <i class="fas fa-user-shield text-[#4e73df] text-xs"></i>
                                        <span>Supervisor QC</span>
                                    </span>
                                </td>
                                <td class="px-3 py-3 align-middle text-gray-700 leading-relaxed break-words">
                                    @php
                                        $rawNote = $v->catatan_visit ?: 'Koordinasi & verifikasi langsung penanganan kualitas di lokasi customer';
                                        $hasStamp = preg_match('/^(.*?)(?:[\s\n]*)(\[Selesai Kunjungan pada [^\]]+\])(.*)$/s', $rawNote, $matches);
                                    @endphp
                                    @if($hasStamp)
                                        <div class="whitespace-pre-line text-gray-800">{{ trim($matches[1]) ?: 'Koordinasi & verifikasi langsung penanganan kualitas di lokasi customer' }}</div>
                                        <div class="text-[10px] text-emerald-600 font-semibold mt-1 flex items-center gap-1">
                                            <i class="fas fa-check-circle text-[10px]"></i>
                                            <span class="whitespace-nowrap">Selesai {{ trim(str_replace(['[Selesai Kunjungan pada', ']'], '', $matches[2])) }}</span>
                                        </div>
                                        @if(!empty(trim($matches[3])))
                                            <div class="whitespace-pre-line text-xs text-gray-600 mt-1">{{ trim($matches[3]) }}</div>
                                        @endif
                                    @else
                                        <div class="whitespace-pre-line">{{ $rawNote }}</div>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-left whitespace-nowrap align-middle">
                                    <span class="text-xs font-semibold text-emerald-600 inline-flex items-center gap-1.5">
                                        <i class="fas fa-check-circle text-[11px]"></i> Selesai
                                    </span>
                                </td>
                                @if($isSpv)
                                <td class="pl-2 pr-5 py-3 text-center whitespace-nowrap align-middle">
                                    <div class="relative inline-block text-left" x-data="{ open: false }">
                                        <button type="button" @click="open = !open" @click.outside="open = false"
                                                title="Opsi Aksi"
                                                class="w-7 h-7 rounded-full inline-flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition cursor-pointer">
                                            <i class="fas fa-ellipsis-v text-xs"></i>
                                        </button>

                                        <div x-show="open" x-cloak
                                             x-transition:enter="transition ease-out duration-100"
                                             x-transition:enter-start="transform opacity-0 scale-95"
                                             x-transition:enter-end="transform opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-75"
                                             x-transition:leave-start="transform opacity-100 scale-100"
                                             x-transition:leave-end="transform opacity-0 scale-95"
                                             class="absolute right-0 top-full mt-1 w-40 bg-white rounded-lg shadow-xl border border-[#e3e6f0] py-1 z-30 divide-y divide-gray-100 text-left text-xs font-medium">
                                            <div class="py-1">
                                                {{-- 1. Edit Jadwal --}}
                                                <button type="button"
                                                        @click="open = false; openEditVisitModal({{ json_encode($v) }})"
                                                        class="w-full text-left px-3 py-1.5 text-gray-700 hover:bg-gray-50 hover:text-[#4e73df] flex items-center gap-2.5 transition cursor-pointer">
                                                    <i class="fas fa-pen text-[#4e73df] w-3 text-center"></i>
                                                    <span>Edit Jadwal</span>
                                                </button>
                                            </div>

                                            {{-- 2. Hapus Jadwal --}}
                                            <div class="py-1">
                                                <form action="{{ route('visit.destroy', $v) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus agenda visit ke {{ $v->nama_customer }}?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                            class="w-full text-left px-3 py-1.5 text-rose-600 hover:bg-rose-50 flex items-center gap-2.5 transition cursor-pointer">
                                                        <i class="fas fa-trash text-rose-500 w-3 text-center"></i>
                                                        <span>Hapus Jadwal</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isSpv ? 7 : 6 }}" class="px-4 py-8 text-center text-gray-400">
                                    <i class="fas fa-history text-3xl text-gray-300 mb-2 block"></i>
                                    <span class="text-xs font-semibold text-gray-700 block">Belum ada riwayat kunjungan yang selesai.</span>
                                    <span class="text-[11px] text-gray-500">Kunjungan yang telah selesai dilaksanakan akan tersimpan di sini.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($selesaiVisits->total() > 0)
                    <div class="px-4 py-3 border-t border-slate-200 bg-white">
                        {{ $selesaiVisits->appends(['tab' => 'done'])->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- ================================================================= --}}
        {{-- VIEW 2: KALENDER GRID (CALENDAR MODE)                             --}}
        {{-- ================================================================= --}}
        <div x-show="viewMode === 'calendar'" x-cloak class="bg-white">
            {{-- Calendar Filter Bar --}}
            <form method="GET" class="px-4 py-3 bg-[#f8f9fc] border-b border-[#e3e6f0] flex flex-wrap items-center justify-between gap-3 text-xs">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-gray-600 font-bold uppercase tracking-wide text-[11px] m-0">Pilih Periode:</span>
                    <select name="tahun" onchange="this.form.submit()"
                            class="rounded border border-[#d1d3e2] px-3 py-1 text-xs font-semibold focus:ring-[#4e73df] focus:border-[#4e73df] bg-white h-8 text-gray-700 cursor-pointer shadow-none">
                        @foreach($years as $y)
                            <option value="{{ $y }}" @selected($y == $tahun)>Tahun {{ $y }}</option>
                        @endforeach
                    </select>

                    <select name="bulan" onchange="this.form.submit()"
                            class="rounded border border-[#d1d3e2] px-3 py-1 text-xs font-semibold focus:ring-[#4e73df] focus:border-[#4e73df] bg-white h-8 text-gray-700 cursor-pointer shadow-none">
                        @foreach(array_slice($namaBulan, 1, 12, true) as $i => $bln)
                            <option value="{{ $i }}" @selected($i == $bulan)>{{ $bln }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="btn btn-primary btn-sm h-8 rounded px-3 text-xs inline-flex items-center gap-1">
                        <i class="fas fa-filter fa-sm"></i>
                        <span>Filter</span>
                    </button>
                </div>

                <div class="flex items-center gap-2">
                    <h6 class="m-0 font-bold text-[#4e73df] text-xs uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fas fa-calendar-day"></i>
                        <span>{{ $namaBulan[$bulan] }} {{ $tahun }}</span>
                    </h6>
                    <span class="badge badge-primary font-bold text-xs px-2.5 py-1">
                        {{ $totalAllVisits }} Total Jadwal
                    </span>
                </div>
            </form>

            {{-- Calendar Days of Week Header --}}
            <div class="grid grid-cols-7 bg-[#f8f9fc] border-b border-[#e3e6f0] text-center text-xs font-bold text-gray-700 uppercase tracking-wider py-2.5">
                <div class="text-[#e74a3b]">Minggu</div>
                <div>Senin</div>
                <div>Selasa</div>
                <div>Rabu</div>
                <div>Kamis</div>
                <div>Jumat</div>
                <div class="text-[#4e73df]">Sabtu</div>
            </div>

            {{-- Calendar Grid Cells --}}
            <div class="grid grid-cols-7 auto-rows-fr divide-x divide-y divide-[#e3e6f0] bg-white">
                {{-- Empty offset cells --}}
                @for ($i = 0; $i < $dayOfWeek; $i++)
                    <div class="min-h-[110px] bg-[#f8f9fc]/60 p-2"></div>
                @endfor

                {{-- Month Days --}}
                @for ($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $currentDate = sprintf('%04d-%02d-%02d', $tahun, $bulan, $day);
                        $dayVisits = $calendarVisits[$currentDate] ?? [];
                        $isToday = $currentDate === date('Y-m-d');
                    @endphp
                    <div class="min-h-[110px] p-2 transition-colors flex flex-col justify-between {{ $isToday ? 'bg-blue-50/50 ring-2 ring-[#4e73df] ring-inset' : 'bg-white hover:bg-[#f8f9fc]' }}">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold {{ $isToday ? 'w-6 h-6 rounded bg-[#4e73df] text-white flex items-center justify-center' : 'text-gray-700' }}">
                                {{ $day }}
                            </span>
                            @if($isToday)
                                <span class="badge badge-primary text-[9px] px-1.5 py-0.5 font-bold">Hari Ini</span>
                            @endif
                        </div>

                        <div class="space-y-1.5 flex-1 overflow-y-auto">
                            @foreach($dayVisits as $v)
                                <div class="p-1.5 rounded border text-[11px] leading-tight {{ $v->perlu_visit ? 'bg-amber-50 border-amber-300 text-amber-900 font-semibold' : 'bg-emerald-50 border-emerald-300 text-emerald-900 font-medium' }}">
                                    <div class="flex items-center justify-between gap-1">
                                        <span class="font-bold truncate" title="{{ $v->nama_customer }}">{{ $v->nama_customer }}</span>
                                        @if(!$v->perlu_visit)
                                            <span class="badge badge-success text-[9px] px-1 py-0 shrink-0 font-bold">
                                                <i class="fas fa-check"></i> Selesai
                                            </span>
                                        @elseif($v->jam_visit)
                                            <span class="badge badge-warning text-dark text-[9px] px-1 py-0 shrink-0 font-bold">
                                                <i class="fas fa-clock"></i> {{ substr($v->jam_visit, 0, 5) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-[10px] text-gray-600 truncate mt-0.5">
                                        {{ $v->catatan_visit ?: 'Kunjungan Lapangan' }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </div>

    {{-- 3. Modal Tambah Jadwal Visit (Khusus SPV) --}}
    @if($isSpv)
    @php
        $activeComplaints = \App\Models\Complaint::orderByDesc('id')->take(50)->get();
    @endphp
    <div id="addVisitModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs p-4">
        <div class="bg-white rounded max-w-lg w-full border border-[#e3e6f0] shadow-2xl overflow-hidden my-8">
            {{-- Modal Header --}}
            <div class="bg-[#f8f9fc] text-[#4e73df] px-4 py-3 border-b border-[#e3e6f0] flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fas fa-calendar-plus text-base"></i>
                    <h6 class="m-0 font-bold text-sm text-[#4e73df]">Tambah Agenda Visit Customer</h6>
                </div>
                <button type="button" onclick="document.getElementById('addVisitModal').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600 text-xl font-bold leading-none">&times;</button>
            </div>

            {{-- Form Content --}}
            <form id="visitScheduleForm" action="" method="POST"
                  onsubmit="
                    const frm = document.getElementById('visitScheduleForm');
                    if (!frm.action || !frm.action.includes('/schedule')) {
                        alert('Pilih data complain terlebih dahulu!');
                        event.preventDefault();
                        return false;
                    }
                  ">
                @csrf
                <div class="p-4 space-y-3.5 text-xs">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">
                            Pilih Data Complain / Customer <span class="text-rose-500">*</span>
                        </label>
                        <select id="visitComplaintSelect" onchange="
                            const sel = this.options[this.selectedIndex];
                            document.getElementById('m_no_cust').value  = sel.dataset.no   || '';
                            document.getElementById('m_nama_cust').value = sel.dataset.nama || '';
                            const id = sel.value;
                            const frm = document.getElementById('visitScheduleForm');
                            frm.action = id ? '/visit/' + id + '/schedule' : '';
                            const btn = document.getElementById('visitSubmitBtn');
                            btn.disabled = !id;
                            btn.classList.toggle('opacity-50', !id);
                            btn.classList.toggle('cursor-not-allowed', !id);
                        " class="w-full text-xs rounded border border-[#d1d3e2] p-2 bg-white text-gray-700 focus:border-[#4e73df] focus:ring-1 focus:ring-[#4e73df]" required>
                            <option value="">-- Pilih dari Complain Aktif --</option>
                            @foreach($activeComplaints as $ac)
                                <option value="{{ $ac->id }}"
                                        data-no="{{ $ac->no_customer }}"
                                        data-nama="{{ $ac->nama_customer }}">
                                    {{ $ac->no_customer }} - {{ $ac->nama_customer }} ({{ optional($ac->tanggal_complain)->format('d/m/Y') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">No. Complain / Customer</label>
                            <input type="text" id="m_no_cust" readonly
                                   class="w-full text-xs rounded border border-[#e3e6f0] p-2 bg-[#eaecf4] text-gray-600 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Nama Customer</label>
                            <input type="text" id="m_nama_cust" readonly
                                   class="w-full text-xs rounded border border-[#e3e6f0] p-2 bg-[#eaecf4] text-gray-600 cursor-not-allowed">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Tanggal Visit <span class="text-rose-500">*</span></label>
                            <input type="date" name="tanggal_visit" min="{{ date('Y-m-d') }}" required
                                   class="w-full text-xs rounded border border-[#d1d3e2] p-2 focus:border-[#4e73df] focus:ring-1 focus:ring-[#4e73df]">
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Jam Visit</label>
                            <input type="time" name="jam_visit"
                                   class="w-full text-xs rounded border border-[#d1d3e2] p-2 focus:border-[#4e73df] focus:ring-1 focus:ring-[#4e73df]">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Catatan Agenda / Tujuan Visit</label>
                        <textarea name="catatan_visit" rows="3"
                                  placeholder="Contoh: Verifikasi sampel kertas dan koordinasi perbaikan di lokasi customer..."
                                  class="w-full text-xs rounded border border-[#d1d3e2] p-2 focus:border-[#4e73df] focus:ring-1 focus:ring-[#4e73df] resize-none"></textarea>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="bg-[#f8f9fc] px-4 py-2.5 border-t border-[#e3e6f0] flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('addVisitModal').classList.add('hidden')"
                            class="btn btn-secondary btn-sm">
                        Batal
                    </button>
                    <button type="submit" id="visitSubmitBtn" disabled
                            class="btn btn-primary btn-sm opacity-50 cursor-not-allowed">
                        <i class="fas fa-calendar-check"></i>
                        <span>Simpan Jadwal Visit</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 4. Modal Edit Jadwal Visit (Khusus SPV) --}}
    <div id="editVisitModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs p-4">
        <div class="bg-white rounded max-w-lg w-full border border-[#e3e6f0] shadow-2xl overflow-hidden my-8">
            {{-- Modal Header --}}
            <div class="bg-[#f8f9fc] text-[#4e73df] px-4 py-3 border-b border-[#e3e6f0] flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fas fa-pen text-base"></i>
                    <h6 class="m-0 font-bold text-sm text-[#4e73df]">Edit Agenda Visit Customer</h6>
                </div>
                <button type="button" onclick="document.getElementById('editVisitModal').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600 text-xl font-bold leading-none">&times;</button>
            </div>

            {{-- Form Content --}}
            <form id="editVisitScheduleForm" action="" method="POST">
                @csrf
                <div class="p-4 space-y-3.5 text-xs">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">No. Complain / Customer</label>
                            <input type="text" id="edit_m_no_cust" readonly
                                   class="w-full text-xs rounded border border-[#e3e6f0] p-2 bg-[#eaecf4] text-gray-600 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Nama Customer</label>
                            <input type="text" id="edit_m_nama_cust" readonly
                                   class="w-full text-xs rounded border border-[#e3e6f0] p-2 bg-[#eaecf4] text-gray-600 cursor-not-allowed">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Tanggal Visit <span class="text-rose-500">*</span></label>
                            <input type="date" name="tanggal_visit" id="edit_m_tanggal_visit" required
                                   class="w-full text-xs rounded border border-[#d1d3e2] p-2 focus:border-[#4e73df] focus:ring-1 focus:ring-[#4e73df]">
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Jam Visit</label>
                            <input type="time" name="jam_visit" id="edit_m_jam_visit"
                                   class="w-full text-xs rounded border border-[#d1d3e2] p-2 focus:border-[#4e73df] focus:ring-1 focus:ring-[#4e73df]">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Catatan Agenda / Tujuan Visit</label>
                        <textarea name="catatan_visit" id="edit_m_catatan_visit" rows="3"
                                  placeholder="Contoh: Verifikasi sampel kertas dan koordinasi perbaikan di lokasi customer..."
                                  class="w-full text-xs rounded border border-[#d1d3e2] p-2 focus:border-[#4e73df] focus:ring-1 focus:ring-[#4e73df] resize-none"></textarea>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="bg-[#f8f9fc] px-4 py-2.5 border-t border-[#e3e6f0] flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('editVisitModal').classList.add('hidden')"
                            class="btn btn-secondary btn-sm">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditVisitModal(v) {
            const frm = document.getElementById('editVisitScheduleForm');
            frm.action = '/visit/' + v.id + '/schedule';
            document.getElementById('edit_m_no_cust').value = v.no_customer || '';
            document.getElementById('edit_m_nama_cust').value = v.nama_customer || '';

            let dateStr = '';
            if (v.tanggal_visit) {
                dateStr = v.tanggal_visit.substring(0, 10);
            }
            document.getElementById('edit_m_tanggal_visit').value = dateStr;

            let timeStr = '';
            if (v.jam_visit) {
                timeStr = v.jam_visit.substring(0, 5);
            }
            document.getElementById('edit_m_jam_visit').value = timeStr;

            document.getElementById('edit_m_catatan_visit').value = v.catatan_visit || '';

            document.getElementById('editVisitModal').classList.remove('hidden');
        }
    </script>
    @endif

</div>
@endsection
