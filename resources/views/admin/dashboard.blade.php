@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="fade-in space-y-6">

    <div>
        <h2 class="text-lg font-semibold">Dashboard Admin Hubin</h2>
        <p class="text-sm text-white/50">Ringkasan data SIM PKL saat ini.</p>
    </div>

    {{-- Summary cards --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="card p-5">
            <p class="text-xs uppercase tracking-wide text-white/40">Total Siswa</p>
            <p class="mt-2 text-2xl font-bold">{{ $summary['total_siswa'] }}</p>
        </div>
        <div class="card p-5">
            <p class="text-xs uppercase tracking-wide text-white/40">Total Perusahaan</p>
            <p class="mt-2 text-2xl font-bold">{{ $summary['total_perusahaan'] }}</p>
            <p class="mt-1 text-xs text-white/40">{{ $summary['perusahaan_aktif'] }} aktif</p>
        </div>
        <div class="card p-5">
            <p class="text-xs uppercase tracking-wide text-white/40">Menunggu Validasi</p>
            <p class="mt-2 text-2xl font-bold text-[#EAB308]">{{ $summary['menunggu_validasi'] }}</p>
        </div>
        <div class="card p-5">
            <p class="text-xs uppercase tracking-wide text-white/40">Menunggu Perusahaan</p>
            <p class="mt-2 text-2xl font-bold text-[#3B82F6]">{{ $summary['menunggu_perusahaan'] }}</p>
        </div>
        <div class="card p-5">
            <p class="text-xs uppercase tracking-wide text-white/40">Total Guru Pembimbing</p>
            <p class="mt-2 text-2xl font-bold">{{ $summary['total_guru'] }}</p>
        </div>
        <div class="card p-5">
            <p class="text-xs uppercase tracking-wide text-white/40">Pengajuan Diterima</p>
            <p class="mt-2 text-2xl font-bold text-[#22C55E]">{{ $summary['diterima'] }}</p>
        </div>
        <div class="card p-5">
            <p class="text-xs uppercase tracking-wide text-white/40">Pengajuan Ditolak</p>
            <p class="mt-2 text-2xl font-bold text-[#EF4444]">{{ $summary['ditolak'] }}</p>
        </div>
    </div>

    {{-- Quick actions --}}
    <div>
        <h3 class="mb-3 font-semibold">Aksi Cepat</h3>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <a href="{{ route('admin.perusahaan.create') }}" class="card card-hover p-5 text-sm font-medium">+ Tambah Perusahaan</a>
            <a href="{{ route('admin.guru.create') }}" class="card card-hover p-5 text-sm font-medium">+ Tambah Guru</a>
            <a href="{{ route('admin.siswa.create') }}" class="card card-hover p-5 text-sm font-medium">+ Tambah Siswa</a>
            @if (Route::has('admin.pengajuan.index'))
                <a href="{{ route('admin.pengajuan.index') }}" class="card card-hover p-5 text-sm font-medium">Lihat Pengajuan</a>
            @endif
        </div>
    </div>

    {{-- Recent activity --}}
    <div>
        <h3 class="mb-3 font-semibold">Aktivitas Terbaru</h3>
        <div class="card divide-y divide-white/5">
            @forelse ($recentActivity as $log)
                <div class="flex items-start justify-between gap-4 px-5 py-3.5 text-sm">
                    <div>
                        <span class="font-medium">{{ $log->user->nama_lengkap ?? 'Sistem' }}</span>
                        <span class="text-white/60">{{ $log->description }}</span>
                    </div>
                    <span class="shrink-0 text-xs text-white/30">{{ $log->created_at->diffForHumans() }}</span>
                </div>
            @empty
                <p class="px-5 py-8 text-center text-sm text-white/40">Belum ada aktivitas.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
