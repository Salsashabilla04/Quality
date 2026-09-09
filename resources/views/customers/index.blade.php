@extends('layouts.app')
@section('title', 'Daftar Customer')
@section('subtitle', 'Daftar customer / pihak perusahaan yang mengajukan komplain')
@section('hide_create_btn', true)

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="p-6 border-b border-slate-200 flex flex-wrap justify-between items-center gap-4 bg-slate-50/50">
        <h2 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Data Master Customer
            <span class="text-sm font-normal text-slate-500">({{ $customers->total() }} perusahaan)</span>
        </h2>

        <form action="{{ route('customers.index') }}" method="GET" class="relative">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama customer..."
                   class="pl-9 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 w-64 shadow-sm">
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-semibold">
                    <th class="p-4 pl-6 text-center w-12">No</th>
                    <th class="p-4">Nama Customer / Perusahaan</th>
                    <th class="p-4 text-center">Total Kasus</th>
                    <th class="p-4 text-center">Total Qty NG</th>
                    <th class="p-4 text-center">Avg Lead Time</th>
                    <th class="p-4 pr-6 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($customers as $index => $c)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="p-4 pl-6 text-center text-sm text-slate-400">
                        {{ $customers->firstItem() + $index }}
                    </td>
                    <td class="p-4">
                        <div class="font-medium text-slate-800">{{ $c->nama_customer }}</div>
                        @if($c->total_complaint > 5)
                            <span class="inline-flex items-center px-2 py-0.5 mt-1 rounded text-[10px] font-medium bg-rose-100 text-rose-700">High Frequency</span>
                        @endif
                    </td>
                    <td class="p-4 text-center">
                        <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-sm font-semibold {{ $c->total_complaint > 0 ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $c->total_complaint }}
                        </span>
                    </td>
                    <td class="p-4 text-center">
                        <span class="text-sm font-medium {{ $c->total_qty > 0 ? 'text-rose-600' : 'text-slate-500' }}">
                            {{ number_format($c->total_qty, 0, ',', '.') }}
                        </span>
                    </td>
                    <td class="p-4 text-center">
                        @if(is_null($c->avg_lead_time))
                            <span class="text-slate-400 text-sm">-</span>
                        @else
                            <span class="text-sm font-medium {{ $c->avg_lead_time > 14 ? 'text-rose-600' : ($c->avg_lead_time > 7 ? 'text-amber-600' : 'text-teal-600') }}">
                                {{ round($c->avg_lead_time, 1) }} hr
                            </span>
                        @endif
                    </td>
                    <td class="p-4 pr-6 text-center">
                        <a href="{{ route('complaints.index', ['q' => $c->nama_customer]) }}"
                           class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition-colors"
                           title="Filter data komplain customer ini">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Histori Komplain
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-8 text-center text-slate-500">
                        <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <p class="text-base font-medium text-slate-700">Tidak ada data customer</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($customers->hasPages())
    <div class="p-4 border-t border-slate-200 bg-slate-50">
        {{ $customers->links() }}
    </div>
    @endif
</div>
@endsection
