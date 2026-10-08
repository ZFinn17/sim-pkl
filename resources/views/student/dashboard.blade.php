@extends('layouts.app')

@section('title', 'Dashboard Siswa')
@section('page-title', 'Dashboard')

@section('sidebar')
    @include('partials.sidebar-siswa')
@endsection

@section('content')
<div class="fade-in space-y-6">

    <div>
        <h2 class="text-lg font-semibold">Halo, {{ auth()->user()->nama_lengkap }} 👋</h2>
        <p class="text-sm text-white/50">Berikut ringkasan pengajuan PKL Anda.</p>
    </div>

    {{-- Summary cards --}}
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="card p-5">
            <p class="text-xs uppercase tracking-wide text-white/40">Pengajuan Aktif</p>
            <p class="mt-2 text-2xl font-bold text-[#3B82F6]">{{ $summary['aktif'] }}</p>
        </div>
        <div class="card p-5">
            <p class="text-xs uppercase tracking-wide text-white/40">Diterima</p>
            <p class="mt-2 text-2xl font-bold text-[#22C55E]">{{ $summary['diterima'] }}</p>
        </div>
        <div class="card p-5">
            <p class="text-xs uppercase tracking-wide text-white/40">Ditolak</p>
            <p class="mt-2 text-2xl font-bold text-[#EF4444]">{{ $summary['ditolak'] }}</p>
        </div>
    </div>

    {{-- Pengajuan Saya --}}
    <div>
        <div class="mb-3 flex items-center justify-between">
            <h3 class="font-semibold">Pengajuan Saya</h3>
            @if (Route::has('student.perusahaan.index'))
                <a href="{{ route('student.perusahaan.index') }}" class="text-sm text-[#3B82F6] hover:underline">Ajukan PKL baru</a>
            @endif
        </div>

        @if ($pengajuanAktif->isEmpty())
            <div class="card flex flex-col items-center gap-2 p-10 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white/20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-sm text-white/40">Belum ada pengajuan PKL.</p>
                @if (Route::has('student.perusahaan.index'))
                    <a href="{{ route('student.perusahaan.index') }}" class="btn-primary mt-2">Lihat Katalog Perusahaan</a>
                @endif
            </div>
        @else
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ($pengajuanAktif as $pengajuan)
                    <div class="card card-hover p-5">
                        <div class="mb-2 flex items-start justify-between">
                            <div>
                                <p class="font-semibold">{{ $pengajuan->perusahaan->nama_perusahaan }}</p>
                                <p class="text-xs text-white/40">{{ $pengajuan->kode_pengajuan }}</p>
                            </div>
                            <span class="badge-{{ $pengajuan->status }}">{{ $pengajuan->statusLabel() }}</span>
                        </div>
                        <p class="text-sm text-white/50">
                            {{ ucfirst($pengajuan->jenis_pengajuan) }} ·
                            Diajukan {{ $pengajuan->tgl_pengajuan->translatedFormat('d M Y') }}
                        </p>
                        @if (Route::has('student.pengajuan.show'))
                            <a href="{{ route('student.pengajuan.show', $pengajuan) }}" class="btn-secondary mt-4 w-full">Lihat Detail</a>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
