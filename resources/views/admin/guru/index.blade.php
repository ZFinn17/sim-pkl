@extends('layouts.app')

@section('title', 'Kelola Guru Pembimbing')
@section('page-title', 'Guru Pembimbing')

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="fade-in space-y-5">

    <div class="flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-lg font-semibold">Kelola Guru Pembimbing</h2>
        <a href="{{ route('admin.guru.create') }}" class="btn-primary">+ Tambah Guru</a>
    </div>

    <form method="GET" class="card flex flex-wrap items-end gap-3 p-4">
        <div class="min-w-[200px] flex-1">
            <label class="label">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama guru..." class="input">
        </div>
        <button type="submit" class="btn-secondary">Filter</button>
    </form>

    @if ($guru->isEmpty())
        <div class="card p-10 text-center text-sm text-white/40">Belum ada data guru pembimbing.</div>
    @else
        <div class="card hidden overflow-x-auto lg:block">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-white/10 text-xs uppercase text-white/40">
                    <tr>
                        <th class="px-5 py-3">NIP</th>
                        <th class="px-5 py-3">Nama</th>
                        <th class="px-5 py-3">No. HP</th>
                        <th class="px-5 py-3">Email</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach ($guru as $g)
                        <tr class="hover:bg-white/[0.02]">
                            <td class="px-5 py-3">{{ $g->nip }}</td>
                            <td class="px-5 py-3 font-medium">{{ $g->nama_lengkap }}</td>
                            <td class="px-5 py-3">{{ $g->no_hp ?? '-' }}</td>
                            <td class="px-5 py-3 text-white/60">{{ $g->email ?? '-' }}</td>
                            <td class="px-5 py-3">
                                <span class="{{ $g->isAktif() ? 'badge-accepted' : 'badge-cancelled' }}">
                                    {{ $g->isAktif() ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('admin.guru.edit', $g) }}" class="text-[#3B82F6] hover:underline">Edit</a>
                                    <form method="POST" action="{{ route('admin.guru.toggle-active', $g) }}" onsubmit="return confirm('Ubah status {{ $g->nama_lengkap }}?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="{{ $g->isAktif() ? 'text-[#EF4444]' : 'text-[#22C55E]' }} hover:underline">
                                            {{ $g->isAktif() ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="space-y-3 lg:hidden">
            @foreach ($guru as $g)
                <div class="card p-4">
                    <div class="mb-2 flex items-start justify-between">
                        <div>
                            <p class="font-semibold">{{ $g->nama_lengkap }}</p>
                            <p class="text-xs text-white/40">{{ $g->nip }}</p>
                        </div>
                        <span class="{{ $g->isAktif() ? 'badge-accepted' : 'badge-cancelled' }}">
                            {{ $g->isAktif() ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    <p class="mb-3 text-sm text-white/50">{{ $g->email ?? '-' }} · {{ $g->no_hp ?? '-' }}</p>
                    <div class="flex gap-3 text-sm">
                        <a href="{{ route('admin.guru.edit', $g) }}" class="text-[#3B82F6]">Edit</a>
                        <form method="POST" action="{{ route('admin.guru.toggle-active', $g) }}" onsubmit="return confirm('Ubah status?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="{{ $g->isAktif() ? 'text-[#EF4444]' : 'text-[#22C55E]' }}">
                                {{ $g->isAktif() ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div>{{ $guru->links() }}</div>
    @endif
</div>
@endsection
