@extends('layouts.guest')

@section('title', 'Beranda')

@section('content')
<div class="min-h-screen">

    {{-- Navbar --}}
    <header class="border-b border-white/10">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
            <div class="flex items-center gap-2">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#2563EB] text-sm font-bold">P</div>
                <span class="text-lg font-semibold">SIM PKL</span>
            </div>
            <a href="{{ route('login') }}" class="btn-primary">Masuk</a>
        </div>
    </header>

    {{-- Hero --}}
    <section class="mx-auto max-w-4xl px-6 py-20 text-center fade-in">
        <p class="mb-3 text-sm font-medium uppercase tracking-widest text-[#3B82F6]">SMK ICB Cinta Niaga · Bandung</p>
        <h1 class="text-4xl font-bold leading-tight sm:text-5xl">
            SIM PKL
            <span class="block text-white/60">Portal Pengajuan &amp; Matching PKL</span>
        </h1>
        <p class="mx-auto mt-6 max-w-2xl text-white/60">
            Ajukan, pantau, dan kelola Praktik Kerja Lapangan secara digital — mulai dari memilih
            perusahaan, validasi Hubin, surat pengantar otomatis, hingga konfirmasi dari perusahaan.
        </p>
        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('login') }}" class="btn-primary">Masuk ke SIM PKL</a>
            <a href="#fitur" class="btn-secondary">Lihat Fitur</a>
        </div>
    </section>

    {{-- Cara kerja --}}
    <section class="border-t border-white/10 bg-white/[0.02] py-16">
        <div class="mx-auto max-w-5xl px-6">
            <h2 class="text-center text-2xl font-semibold">Bagaimana SIM PKL bekerja?</h2>
            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-5">
                @foreach ([
                    ['1', 'Pilih Perusahaan', 'Telusuri katalog perusahaan mitra beserta kuota yang tersedia.'],
                    ['2', 'Ajukan PKL', 'Ajukan secara individu atau berkelompok (maksimal 3 siswa).'],
                    ['3', 'Tunggu Validasi', 'Admin Hubin memeriksa dan memvalidasi pengajuan Anda.'],
                    ['4', 'Dapatkan Surat', 'Surat pengantar PKL dibuat otomatis lengkap dengan QR verifikasi.'],
                    ['5', 'Konfirmasi Perusahaan', 'Perusahaan meninjau dan memberikan keputusan akhir.'],
                ] as [$num, $title, $desc])
                    <div class="card card-hover p-5">
                        <div class="mb-3 flex h-8 w-8 items-center justify-center rounded-lg bg-[#2563EB]/15 text-sm font-bold text-[#3B82F6]">{{ $num }}</div>
                        <h3 class="mb-1 font-semibold">{{ $title }}</h3>
                        <p class="text-sm text-white/50">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Fitur --}}
    <section id="fitur" class="py-16">
        <div class="mx-auto max-w-5xl px-6">
            <h2 class="text-center text-2xl font-semibold">Fitur Utama</h2>
            <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ([
                    'Katalog perusahaan mitra dengan kuota real-time',
                    'Tracking status pengajuan dengan timeline',
                    'Surat pengantar PDF otomatis',
                    'Verifikasi surat via QR Code',
                    'Notifikasi dalam aplikasi',
                    'Manajemen kuota perusahaan',
                ] as $fitur)
                    <div class="card p-5 text-sm text-white/70">{{ $fitur }}</div>
                @endforeach
            </div>
        </div>
    </section>

    <footer class="border-t border-white/10 py-8 text-center text-sm text-white/40">
        &copy; {{ date('Y') }} SMK ICB Cinta Niaga — SIM PKL
    </footer>
</div>
@endsection
