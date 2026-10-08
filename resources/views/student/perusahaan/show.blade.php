@extends('layouts.app')

@section('title', $perusahaan->nama_perusahaan)
@section('page-title', 'Detail Perusahaan')

@section('sidebar')
    @include('partials.sidebar-siswa')
@endsection

@section('content')
<div class="fade-in mx-auto max-w-2xl space-y-5">

    <div class="flex items-center gap-3">
        <a href="{{ route('student.perusahaan.index') }}" class="text-white/40 hover:text-white">&larr;</a>
        <h2 class="text-lg font-semibold">Detail Perusahaan</h2>
    </div>

    <div class="card p-6">
        <div class="mb-4 flex items-start justify-between">
            <h3 class="text-xl font-bold">{{ $perusahaan->nama_perusahaan }}</h3>
            <span class="badge-accepted">Mitra Aktif</span>
        </div>

        <dl class="divide-y divide-white/5 text-sm">
            <div class="flex justify-between py-3">
                <dt class="text-white/40">Alamat</dt>
                <dd class="max-w-xs text-right">{{ $perusahaan->alamat_lengkap }}</dd>
            </div>
            <div class="flex justify-between py-3">
                <dt class="text-white/40">PIC</dt>
                <dd>{{ $perusahaan->nama_pic }}</dd>
            </div>
            <div class="flex justify-between py-3">
                <dt class="text-white/40">Kontak HRD</dt>
                <dd>{{ $perusahaan->kontak_hrd }}</dd>
            </div>
            <div class="flex justify-between py-3">
                <dt class="text-white/40">Kuota Tersedia</dt>
                <dd class="font-semibold {{ $perusahaan->kuota_tersedia > 0 ? 'text-[#22C55E]' : 'text-[#EF4444]' }}">
                    {{ $perusahaan->kuota_tersedia }} / {{ $perusahaan->kuota_maksimal }}
                </dd>
            </div>
        </dl>

        @if ($perusahaan->deskripsi)
            <div class="mt-4 border-t border-white/10 pt-4">
                <p class="mb-1 text-xs uppercase tracking-wide text-white/40">Deskripsi</p>
                <p class="text-sm text-white/70">{{ $perusahaan->deskripsi }}</p>
            </div>
        @endif

        <div class="mt-6">
            @if ($perusahaan->kuota_tersedia > 0)
                <a href="{{ route('student.pengajuan.create', ['perusahaan' => $perusahaan->id]) }}" class="btn-primary w-full">
                    Ajukan PKL ke Perusahaan Ini
                </a>
            @else
                <button type="button" disabled class="btn-primary w-full">Kuota Penuh</button>
            @endif
        </div>
    </div>
</div>
@endsection
