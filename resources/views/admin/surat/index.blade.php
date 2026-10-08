@extends('layouts.app')

@section('title', 'Surat Pengantar')
@section('page-title', 'Surat Pengantar')

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="fade-in space-y-5">

    <h2 class="text-lg font-semibold">Surat Pengantar PKL</h2>

    <form method="GET" class="card flex flex-wrap items-end gap-3 p-4">
        <div class="min-w-[220px] flex-1">
            <label class="label">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nomor surat atau kode pengajuan..." class="input">
        </div>
        <button type="submit" class="btn-secondary">Cari</button>
    </form>

    @if ($surat->isEmpty())
        <div class="card p-10 text-center text-sm text-white/40">Belum ada surat pengantar yang diterbitkan.</div>
    @else
        <div class="card hidden overflow-x-auto lg:block">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-white/10 text-xs uppercase text-white/40">
                    <tr>
                        <th class="px-5 py-3">Nomor Surat</th>
                        <th class="px-5 py-3">Kode Pengajuan</th>
                        <th class="px-5 py-3">Siswa</th>
                        <th class="px-5 py-3">Perusahaan</th>
                        <th class="px-5 py-3">Diterbitkan</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach ($surat as $s)
                        <tr class="hover:bg-white/[0.02]">
                            <td class="px-5 py-3 font-medium">{{ $s->nomor_surat }}</td>
                            <td class="px-5 py-3">
                                <a href="{{ route('admin.pengajuan.show', $s->pengajuan) }}" class="text-[#3B82F6] hover:underline">{{ $s->pengajuan->kode_pengajuan }}</a>
                            </td>
                            <td class="px-5 py-3">{{ $s->pengajuan->ketua->nama_lengkap }}</td>
                            <td class="px-5 py-3">{{ $s->pengajuan->perusahaan->nama_perusahaan }}</td>
                            <td class="px-5 py-3">{{ $s->generated_at->translatedFormat('d M Y, H:i') }}</td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('surat.download', $s) }}" class="text-[#3B82F6] hover:underline">Download</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="space-y-3 lg:hidden">
            @foreach ($surat as $s)
                <div class="card p-4">
                    <p class="font-semibold">{{ $s->nomor_surat }}</p>
                    <p class="text-sm text-white/50">{{ $s->pengajuan->ketua->nama_lengkap }} · {{ $s->pengajuan->perusahaan->nama_perusahaan }}</p>
                    <p class="mb-3 text-xs text-white/30">{{ $s->generated_at->translatedFormat('d M Y, H:i') }}</p>
                    <div class="flex gap-3 text-sm">
                        <a href="{{ route('admin.pengajuan.show', $s->pengajuan) }}" class="text-[#3B82F6]">Lihat Pengajuan</a>
                        <a href="{{ route('surat.download', $s) }}" class="text-[#3B82F6]">Download</a>
                    </div>
                </div>
            @endforeach
        </div>

        <div>{{ $surat->links() }}</div>
    @endif
</div>
@endsection
