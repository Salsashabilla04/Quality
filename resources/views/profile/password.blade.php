@extends('layouts.app')
@section('title', 'Ganti Password')
@section('subtitle', 'Perbarui kata sandi akun Anda untuk menjaga keamanan akses')
@section('hide_create_btn', true)

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        {{-- Header Card --}}
        <div class="p-6 bg-gradient-to-r from-slate-900 to-slate-800 text-white flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-sky-500/20 border border-sky-400/30 flex items-center justify-center text-sky-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-bold text-white">Kelola Kata Sandi Akun</h2>
                <p class="text-xs text-slate-300 mt-0.5">
                    Akun: <span class="font-semibold text-white">{{ $user->name }}</span> ({{ $user->role === 'supervisor' ? 'Supervisor QC' : 'Staff QA' }}) · <span class="text-sky-300">{{ $user->email }}</span>
                </p>
            </div>
        </div>

        {{-- Form Body --}}
        <form method="POST" action="{{ route('profile.password.update') }}" class="p-6 md:p-8 space-y-6">
            @csrf
            @method('PUT')

            {{-- Error Alerts --}}
            @if ($errors->any())
                <div class="rounded-xl bg-rose-50 border border-rose-200 p-4">
                    <div class="flex items-start gap-3">
                        <span class="text-rose-600 text-lg">⚠️</span>
                        <div class="text-xs text-rose-800 space-y-1">
                            <div class="font-bold">Gagal memperbarui password:</div>
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Password Saat Ini --}}
            <div>
                <label for="current_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Password Saat Ini <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0110 0v4"/>
                        </svg>
                    </div>
                    <input type="password" 
                           name="current_password" 
                           id="current_password" 
                           required 
                           autocomplete="current-password"
                           placeholder="Masukkan kata sandi lama Anda"
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                </div>
            </div>

            <hr class="border-slate-100">

            {{-- Password Baru --}}
            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Password Baru <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <input type="password" 
                           name="password" 
                           id="password" 
                           required 
                           autocomplete="new-password"
                           placeholder="Minimal 6 karakter"
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                </div>
                <p class="text-[11px] text-slate-500 mt-1.5 flex items-center gap-1">
                    <span>💡</span> Gunakan kombinasi huruf dan angka minimal 6 karakter agar lebih aman.
                </p>
            </div>

            {{-- Konfirmasi Password Baru --}}
            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Konfirmasi Password Baru <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <input type="password" 
                           name="password_confirmation" 
                           id="password_confirmation" 
                           required 
                           autocomplete="new-password"
                           placeholder="Ulangi kata sandi baru"
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="pt-4 flex items-center justify-between border-t border-slate-100">
                <a href="{{ route('dashboard') }}" 
                   class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-medium transition">
                    Kembali ke Dashboard
                </a>
                <button type="submit" 
                        class="px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold shadow-sm transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Password Baru
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
