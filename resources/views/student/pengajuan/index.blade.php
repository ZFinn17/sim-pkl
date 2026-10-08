@extends('layouts.app')

@section('title', 'Pengajuan Saya')
@section('page-title', 'Pengajuan Saya')

@section('sidebar')
    @include('partials.sidebar-siswa')
@endsection

@section('content')
<div class="fade-in space-y-5">

    <div class="flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-lg font-semibold">Pengajuan Saya</h2>
        <a href="{{ route('student.perusahaan.index') }}" class="btn-primary">+ Ajukan PKL Baru</a>
    </div>

    @if ($pengajuan->isEmpty())
        <div class="card flex flex-col items-center gap-2 p-10 text-center">
            <p class="text-sm text-white/40">Belum ada pengajuan.</p>
            <a href="{{ route('student.perusahaan.index') }}" class="btn-primary mt-2">Lihat Katalog Perusahaan</a>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($pengajuan as $p)
                <a href="{{ route('student.pengajuan.show', $p) }}" class="card card-hover flex items-center justify-between gap-4 p-5">
                    <div>
                        <p class="font-semibold">{{ $p->perusahaan->nama_perusahaan }}</p>
                        <p class="text-xs text-white/40">
                            {{ $p->kode_pengajuan }} · {{ ucfirst($p->jenis_pengajuan) }} ·
                            Diajukan {{ $p->tgl_pengajuan->translatedFormat('d M Y') }}
                        </p>
                    </div>
                    <span class="badge-{{ $p->status }} shrink-0">{{ $p->statusLabel() }}</span>
                </a>
            @endforeach
        </div>

        <div>{{ $pengajuan->links() }}</div>
    @endif
</div>
@endsection
