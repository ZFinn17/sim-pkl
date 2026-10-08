@extends('layouts.app')

@section('title', 'Kelola Pengajuan')
@section('page-title', 'Pengajuan')

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="fade-in space-y-5">

    <h2 class="text-lg font-semibold">Kelola Pengajuan PKL</h2>

    <form method="GET" class="card flex flex-wrap items-end gap-3 p-4">
        <div class="min-w-[180px] flex-1">
            <label class="label">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Kode atau nama siswa..." class="input">
        </div>
        <div class="w-48">
            <label class="label">Status</label>
            <select name="status" class="input">
                <option value="">Semua</option>
                @foreach (\App\Models\PengajuanPkl::STATUS_LABELS as $key => $label)
                    <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-48">
            <label class="label">Perusahaan</label>
            <select name="perusahaan_id" class="input">
                <option value="">Semua</option>
                @foreach ($perusahaanList as $p)
                    <option value="{{ $p->id }}" @selected(request('perusahaan_id') == $p->id)>{{ $p->nama_perusahaan }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-secondary">Filter</button>
    </form>

    @if ($pengajuan->isEmpty())
        <div class="card p-10 text-center text-sm text-white/40">Belum ada pengajuan.</div>
    @else
        <div class="card hidden overflow-x-auto lg:block">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-white/10 text-xs uppercase text-white/40">
                    <tr>
                        <th class="px-5 py-3">Kode</th>
                        <th class="px-5 py-3">Ketua</th>
                        <th class="px-5 py-3">Perusahaan</th>
                        <th class="px-5 py-3">Jenis</th>
                        <th class="px-5 py-3">Peserta</th>
                        <th class="px-5 py-3">Tanggal</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach ($pengajuan as $p)
                        <tr class="hover:bg-white/[0.02]">
                            <td class="px-5 py-3 font-medium">{{ $p->kode_pengajuan }}</td>
                            <td class="px-5 py-3">{{ $p->ketua->nama_lengkap }}</td>
                            <td class="px-5 py-3">{{ $p->perusahaan->nama_perusahaan }}</td>
                            <td class="px-5 py-3">{{ ucfirst($p->jenis_pengajuan) }}</td>
                            <td class="px-5 py-3">{{ $p->jumlahPeserta() }}</td>
                            <td class="px-5 py-3">{{ $p->tgl_pengajuan->translatedFormat('d M Y') }}</td>
                            <td class="px-5 py-3"><span class="badge-{{ $p->status }}">{{ $p->statusLabel() }}</span></td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('admin.pengajuan.show', $p) }}" class="text-[#3B82F6] hover:underline">Detail</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="space-y-3 lg:hidden">
            @foreach ($pengajuan as $p)
                <a href="{{ route('admin.pengajuan.show', $p) }}" class="card block p-4">
                    <div class="mb-1 flex items-start justify-between">
                        <p class="font-semibold">{{ $p->kode_pengajuan }}</p>
                        <span class="badge-{{ $p->status }}">{{ $p->statusLabel() }}</span>
                    </div>
                    <p class="text-sm text-white/50">{{ $p->ketua->nama_lengkap }} · {{ $p->perusahaan->nama_perusahaan }}</p>
                    <p class="text-xs text-white/30">{{ $p->tgl_pengajuan->translatedFormat('d M Y') }}</p>
                </a>
            @endforeach
        </div>

        <div>{{ $pengajuan->links() }}</div>
    @endif
</div>
@endsection
