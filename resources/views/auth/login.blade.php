@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
<div class="flex min-h-screen items-center justify-center px-4">
    <div class="w-full max-w-sm fade-in">

        <div class="mb-8 flex flex-col items-center text-center">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-[#2563EB] text-lg font-bold">P</div>
            <h1 class="text-xl font-semibold">SIM PKL</h1>
            <p class="mt-1 text-sm text-white/50">SMK ICB Cinta Niaga</p>
        </div>

        <div class="card p-6">
            @if ($errors->any())
                <div class="mb-4 rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-sm text-red-400">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" data-loading-text="Memproses...">
                @csrf

                <div class="mb-4">
                    <label for="email" class="label">Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="username"
                        class="input"
                        placeholder="nama@simpkl.test"
                    >
                </div>

                <div class="mb-5">
                    <label for="password" class="label">Password</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        class="input"
                        placeholder="••••••••"
                    >
                </div>

                <label class="mb-5 flex items-center gap-2 text-sm text-white/60">
                    <input type="checkbox" name="remember" class="rounded border-white/20 bg-white/5 text-[#2563EB] focus:ring-[#2563EB]">
                    Ingat saya
                </label>

                <button type="submit" class="btn-primary w-full">Masuk</button>
            </form>
        </div>

        <p class="mt-6 text-center text-xs text-white/30">
            &copy; {{ date('Y') }} SMK ICB Cinta Niaga — SIM PKL
        </p>
    </div>
</div>
@endsection
