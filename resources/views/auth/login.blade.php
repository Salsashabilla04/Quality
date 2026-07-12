<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login · Monitoring Complaint & NCR</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-6">
            <div class="text-xs uppercase tracking-wider text-sky-600 font-semibold">PT Wahana Bermuda Nusantara</div>
            <h1 class="text-xl font-bold text-slate-900 mt-1">Monitoring Complaint & NCR</h1>
            <p class="text-xs text-slate-500">Seven Tools QC · Algoritma Apriori</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-7">
            <h2 class="font-semibold text-slate-800 mb-5">Masuk Admin</h2>

            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 px-4 py-2.5 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:ring-sky-500 focus:border-sky-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Password</label>
                    <input type="password" name="password" required
                           class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:ring-sky-500 focus:border-sky-500">
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-sky-600">
                    Ingat saya
                </label>
                <button class="w-full bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold py-2.5 rounded-lg">
                    Masuk
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-slate-400 mt-5">
            Akun default: <b>admin@wbn.test</b> / <b>admin123</b>
        </p>
    </div>
</body>
</html>
