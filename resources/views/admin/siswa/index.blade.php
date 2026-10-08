@extends('layouts.app')

@section('title', 'Kelola Siswa')
@section('page-title', 'Siswa')

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="fade-in space-y-5">

    <div class="flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-lg font-semibold">Kelola Siswa</h2>
        <a href="{{ route('admin.siswa.create') }}" class="btn-primary">+ Tambah Siswa</a>
    </div>

    {{-- Filter --}}
    <form method="GET" class="card flex flex-wrap items-end gap-3 p-4">
        <div class="min-w-[200px] flex-1">
            <label class="label">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama atau NIS..." class="input">
        </div>
        <div class="w-40">
            <label class="label">Kelas</label>
            <select name="kelas" class="input">
                <option value="">Semua</option>
                @foreach ($daftarKelas as $kelas)
                    <option value="{{ $kelas }}" @selected(request('kelas') === $kelas)>{{ $kelas }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-40">
            <label class="label">Status</label>
            <select name="status" class="input">
                <option value="">Semua</option>
                <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
            </select>
        </div>
        <button type="submit" class="btn-secondary">Filter</button>
    </form>

    @if ($siswa->isEmpty())
        <div class="card p-10 text-center text-sm text-white/40">Belum ada data siswa.</div>
    @else
        {{-- Desktop table --}}
        <div class="card hidden overflow-x-auto lg:block">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-white/10 text-xs uppercase text-white/40">
                    <tr>
                        <th class="px-5 py-3">NIS</th>
                        <th class="px-5 py-3">Nama</th>
                        <th class="px-5 py-3">Kelas</th>
                        <th class="px-5 py-3">Email</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach ($siswa as $s)
                        <tr class="hover:bg-white/[0.02]">
                            <td class="px-5 py-3">{{ $s->nis_nip }}</td>
                            <td class="px-5 py-3 font-medium">{{ $s->nama_lengkap }}</td>
                            <td class="px-5 py-3">{{ $s->kelas }}</td>
                            <td class="px-5 py-3 text-white/60">{{ $s->email }}</td>
                            <td class="px-5 py-3">
                                <span class="{{ $s->is_active ? 'badge-accepted' : 'badge-cancelled' }}">
                                    {{ $s->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.siswa.edit', $s) }}" class="text-[#3B82F6] hover:underline">Edit</a>
                                    <form method="POST" action="{{ route('admin.siswa.reset-password', $s) }}" onsubmit="return confirm('Reset password {{ $s->nama_lengkap }}?')">
                                        @csrf
                                        <button type="submit" class="text-white/50 hover:underline">Reset PW</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.siswa.toggle-active', $s) }}" onsubmit="return confirm('{{ $s->is_active ? 'Nonaktifkan' : 'Aktifkan' }} {{ $s->nama_lengkap }}?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="{{ $s->is_active ? 'text-[#EF4444]' : 'text-[#22C55E]' }} hover:underline">
                                            {{ $s->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile cards --}}
        <div class="space-y-3 lg:hidden">
            @foreach ($siswa as $s)
                <div class="card p-4">
                    <div class="mb-2 flex items-start justify-between">
                        <div>
                            <p class="font-semibold">{{ $s->nama_lengkap }}</p>
                            <p class="text-xs text-white/40">{{ $s->nis_nip }} · {{ $s->kelas }}</p>
                        </div>
                        <span class="{{ $s->is_active ? 'badge-accepted' : 'badge-cancelled' }}">
                            {{ $s->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    <p class="mb-3 text-sm text-white/50">{{ $s->email }}</p>
                    <div class="flex flex-wrap gap-3 text-sm">
                        <a href="{{ route('admin.siswa.edit', $s) }}" class="text-[#3B82F6]">Edit</a>
                        <form method="POST" action="{{ route('admin.siswa.reset-password', $s) }}" onsubmit="return confirm('Reset password?')">
                            @csrf
                            <button type="submit" class="text-white/50">Reset PW</button>
                        </form>
                        <form method="POST" action="{{ route('admin.siswa.toggle-active', $s) }}" onsubmit="return confirm('Ubah status?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="{{ $s->is_active ? 'text-[#EF4444]' : 'text-[#22C55E]' }}">
                                {{ $s->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div>{{ $siswa->links() }}</div>
    @endif
</div>
@endsection
