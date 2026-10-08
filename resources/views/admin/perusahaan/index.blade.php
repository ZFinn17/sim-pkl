@extends('layouts.app')

@section('title', 'Kelola Perusahaan')
@section('page-title', 'Perusahaan')

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="fade-in space-y-5">

    <div class="flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-lg font-semibold">Kelola Perusahaan</h2>
        <a href="{{ route('admin.perusahaan.create') }}" class="btn-primary">+ Tambah Perusahaan</a>
    </div>

    <form method="GET" class="card flex flex-wrap items-end gap-3 p-4">
        <div class="min-w-[200px] flex-1">
            <label class="label">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama perusahaan..." class="input">
        </div>
        <div class="w-44">
            <label class="label">Status Mitra</label>
            <select name="status" class="input">
                <option value="">Semua</option>
                <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
            </select>
        </div>
        <button type="submit" class="btn-secondary">Filter</button>
    </form>

    @if ($perusahaan->isEmpty())
        <div class="card p-10 text-center text-sm text-white/40">Belum ada perusahaan yang tersedia.</div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($perusahaan as $p)
                <div class="card p-5">
                    <div class="mb-2 flex items-start justify-between">
                        <p class="font-semibold">{{ $p->nama_perusahaan }}</p>
                        <span class="{{ $p->isAktif() ? 'badge-accepted' : 'badge-cancelled' }}">
                            {{ $p->isAktif() ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    <p class="mb-1 text-sm text-white/50">PIC: {{ $p->nama_pic }}</p>
                    <p class="mb-4 text-sm text-white/50">
                        Kuota: <span class="font-medium text-white">{{ $p->kuota_tersedia }}</span> / {{ $p->kuota_maksimal }} tersedia
                    </p>
                    <div class="flex gap-3 text-sm">
                        <a href="{{ route('admin.perusahaan.edit', $p) }}" class="text-[#3B82F6] hover:underline">Edit</a>
                        <form method="POST" action="{{ route('admin.perusahaan.toggle-active', $p) }}" onsubmit="return confirm('Ubah status mitra {{ $p->nama_perusahaan }}?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="{{ $p->isAktif() ? 'text-[#EF4444]' : 'text-[#22C55E]' }} hover:underline">
                                {{ $p->isAktif() ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div>{{ $perusahaan->links() }}</div>
    @endif
</div>
@endsection
