@extends('layouts.app')

@section('title', $pengajuan->kode_pengajuan)
@section('page-title', 'Detail Pengajuan')

@section('sidebar')
    @include('partials.sidebar-perusahaan')
@endsection

@section('content')
<div class="fade-in mx-auto max-w-2xl space-y-5">

    <div class="flex items-center gap-3">
        <a href="{{ route('company.pengajuan.index') }}" class="text-white/40 hover:text-white">&larr;</a>
        <h2 class="text-lg font-semibold">{{ $pengajuan->kode_pengajuan }}</h2>
        <span class="badge-{{ $pengajuan->status }}">{{ $pengajuan->statusLabel() }}</span>
    </div>

    <div class="card p-6">
        <h3 class="mb-3 text-sm font-semibold text-white/60">Data Siswa</h3>
        <dl class="mb-5 divide-y divide-white/5 text-sm">
            <div class="flex justify-between py-2.5">
                <dt class="text-white/40">Nama Ketua</dt>
                <dd>{{ $pengajuan->ketua->nama_lengkap }}</dd>
            </div>
            <div class="flex justify-between py-2.5">
                <dt class="text-white/40">NIS</dt>
                <dd>{{ $pengajuan->ketua->nis_nip }}</dd>
            </div>
            <div class="flex justify-between py-2.5">
                <dt class="text-white/40">Kelas</dt>
                <dd>{{ $pengajuan->ketua->kelas }}</dd>
            </div>
            @if ($pengajuan->isGroup())
                <div class="flex justify-between py-2.5">
                    <dt class="text-white/40">Anggota</dt>
                    <dd class="text-right">
                        @foreach ($pengajuan->anggotaKelompok as $a)
                            <div>{{ $a->siswa->nama_lengkap }} ({{ $a->siswa->nis_nip }})</div>
                        @endforeach
                    </dd>
                </div>
            @endif
        </dl>

        <h3 class="mb-3 text-sm font-semibold text-white/60">Periode & Status</h3>
        <dl class="mb-5 divide-y divide-white/5 text-sm">
            <div class="flex justify-between py-2.5">
                <dt class="text-white/40">Periode PKL</dt>
                <dd>{{ $pengajuan->tgl_mulai_pkl->translatedFormat('d M Y') }} - {{ $pengajuan->tgl_selesai_pkl->translatedFormat('d M Y') }}</dd>
            </div>
            <div class="flex justify-between py-2.5">
                <dt class="text-white/40">Tanggal Pengajuan</dt>
                <dd>{{ $pengajuan->tgl_pengajuan->translatedFormat('d M Y') }}</dd>
            </div>
            <div class="flex justify-between py-2.5">
                <dt class="text-white/40">Jumlah Peserta</dt>
                <dd>{{ $pengajuan->jumlahPeserta() }}</dd>
            </div>
        </dl>

        @if ($pengajuan->status === \App\Models\PengajuanPkl::STATUS_REJECTED && $pengajuan->alasan_penolakan)
            <div class="mb-5 rounded-xl border border-red-500/20 bg-red-500/10 p-4 text-sm text-red-400">
                <p class="mb-1 font-medium">Alasan Penolakan Anda:</p>
                <p>{{ $pengajuan->alasan_penolakan }}</p>
            </div>
        @endif

        @if ($pengajuan->status === \App\Models\PengajuanPkl::STATUS_WAITING_COMPANY)
            <div class="flex flex-wrap gap-3">
                <form method="POST" action="{{ route('company.pengajuan.accept', $pengajuan) }}" onsubmit="return confirm('Terima pengajuan ini?')">
                    @csrf
                    <button type="submit" class="btn-primary">Terima</button>
                </form>
                <button type="button" data-modal-open="modal-tolak" class="btn-danger">Tolak</button>
            </div>
        @endif
    </div>

    {{-- Reject modal --}}
    <div id="modal-tolak" data-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4">
        <div class="card w-full max-w-sm p-6">
            <h3 class="mb-3 font-semibold">Tolak Pengajuan</h3>
            <form method="POST" action="{{ route('company.pengajuan.reject', $pengajuan) }}">
                @csrf
                <label for="alasan_penolakan" class="label">Alasan Penolakan</label>
                <textarea id="alasan_penolakan" name="alasan_penolakan" rows="3" class="input mb-4" required></textarea>
                <div class="flex justify-end gap-3">
                    <button type="button" data-modal-close class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-danger">Tolak Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
