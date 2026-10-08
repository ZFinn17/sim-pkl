@extends('layouts.app')

@section('title', 'Dashboard Perusahaan')
@section('page-title', 'Dashboard')

@section('sidebar')
    @include('partials.sidebar-perusahaan')
@endsection

@section('content')
<div class="fade-in space-y-6">

    @if (! $perusahaan)
        <div class="card p-10 text-center text-sm text-white/40">
            Akun Anda belum terhubung dengan profil perusahaan. Silakan hubungi Admin Hubin.
        </div>
    @else
        <div>
            <h2 class="text-lg font-semibold">{{ $perusahaan->nama_perusahaan }}</h2>
            <p class="text-sm text-white/50">Ringkasan pengajuan PKL yang masuk ke perusahaan Anda.</p>
        </div>

        {{-- Quota --}}
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="card p-5">
                <p class="text-xs uppercase tracking-wide text-white/40">Kuota Maksimal</p>
                <p class="mt-2 text-2xl font-bold">{{ $perusahaan->kuota_maksimal }}</p>
            </div>
            <div class="card p-5">
                <p class="text-xs uppercase tracking-wide text-white/40">Kuota Terisi</p>
                <p class="mt-2 text-2xl font-bold text-[#3B82F6]">{{ $perusahaan->kuota_terisi }}</p>
            </div>
            <div class="card p-5">
                <p class="text-xs uppercase tracking-wide text-white/40">Kuota Tersedia</p>
                <p class="mt-2 text-2xl font-bold text-[#22C55E]">{{ $perusahaan->kuota_tersedia }}</p>
            </div>
        </div>

        {{-- Applications summary --}}
        <div class="grid gap-4 sm:grid-cols-4">
            <div class="card p-5">
                <p class="text-xs uppercase tracking-wide text-white/40">Pengajuan Masuk</p>
                <p class="mt-2 text-2xl font-bold">{{ $summary['masuk'] }}</p>
            </div>
            <div class="card p-5">
                <p class="text-xs uppercase tracking-wide text-white/40">Menunggu Keputusan</p>
                <p class="mt-2 text-2xl font-bold text-[#EAB308]">{{ $summary['menunggu'] }}</p>
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

        {{-- Recent applications --}}
        <div>
            <h3 class="mb-3 font-semibold">Pengajuan Terbaru</h3>
            @if ($recentApplications->isEmpty())
                <div class="card p-10 text-center text-sm text-white/40">Belum ada pengajuan yang masuk.</div>
            @else
                <div class="card divide-y divide-white/5">
                    @foreach ($recentApplications as $pengajuan)
                        <div class="flex items-center justify-between gap-4 px-5 py-3.5 text-sm">
                            <div>
                                <p class="font-medium">{{ $pengajuan->kode_pengajuan }}</p>
                                <p class="text-xs text-white/40">{{ $pengajuan->tgl_pengajuan->translatedFormat('d M Y') }}</p>
                            </div>
                            <span class="badge-{{ $pengajuan->status }}">{{ $pengajuan->statusLabel() }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
