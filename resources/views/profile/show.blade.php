@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page-title', 'Profil')

@section('sidebar')
    @if ($user->isAdmin())
        @include('partials.sidebar-admin')
    @elseif ($user->isPerusahaan())
        @include('partials.sidebar-perusahaan')
    @else
        @include('partials.sidebar-siswa')
    @endif
@endsection

@section('content')
<div class="fade-in mx-auto max-w-2xl space-y-5">

    <h2 class="text-lg font-semibold">Profil Saya</h2>

    <div class="card p-6">
        <div class="mb-6 flex items-center gap-4">
            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-white/10 text-xl font-semibold">
                {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
            </div>
            <div>
                <p class="text-base font-semibold">{{ $user->nama_lengkap }}</p>
                <p class="text-sm text-white/40">{{ ucfirst($user->role) }}</p>
            </div>
        </div>

        <dl class="divide-y divide-white/5 text-sm">
            @if ($user->isSiswa())
                <div class="flex justify-between py-3">
                    <dt class="text-white/40">NIS</dt>
                    <dd>{{ $user->nis_nip }}</dd>
                </div>
                <div class="flex justify-between py-3">
                    <dt class="text-white/40">Kelas</dt>
                    <dd>{{ $user->kelas }}</dd>
                </div>
            @endif

            @if ($user->isPerusahaan() && $user->perusahaan)
                <div class="flex justify-between py-3">
                    <dt class="text-white/40">Nama Perusahaan</dt>
                    <dd>{{ $user->perusahaan->nama_perusahaan }}</dd>
                </div>
                <div class="flex justify-between py-3">
                    <dt class="text-white/40">Alamat</dt>
                    <dd class="text-right">{{ $user->perusahaan->alamat_lengkap }}</dd>
                </div>
                <div class="flex justify-between py-3">
                    <dt class="text-white/40">PIC</dt>
                    <dd>{{ $user->perusahaan->nama_pic }}</dd>
                </div>
                <div class="flex justify-between py-3">
                    <dt class="text-white/40">Kuota</dt>
                    <dd>{{ $user->perusahaan->kuota_tersedia }} / {{ $user->perusahaan->kuota_maksimal }} tersedia</dd>
                </div>
            @endif

            <div class="flex justify-between py-3">
                <dt class="text-white/40">Email</dt>
                <dd>{{ $user->email }}</dd>
            </div>
            <div class="flex justify-between py-3">
                <dt class="text-white/40">No. HP</dt>
                <dd>{{ $user->no_hp ?? '-' }}</dd>
            </div>
        </dl>
    </div>
</div>
@endsection
