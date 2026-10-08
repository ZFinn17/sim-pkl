@extends('layouts.app')

@section('title', 'Pengajuan Masuk')
@section('page-title', 'Pengajuan Masuk')

@section('sidebar')
    @include('partials.sidebar-perusahaan')
@endsection

@section('content')
<div class="fade-in space-y-5">

    <div>
        <h2 class="text-lg font-semibold">Pengajuan Masuk</h2>
        <p class="text-sm text-white/50">Kuota tersedia: {{ $perusahaan->kuota_tersedia }} / {{ $perusahaan->kuota_maksimal }}</p>
    </div>

    <form method="GET" class="card flex flex-wrap items-end gap-3 p-4">
        <div class="w-56">
            <label class="label">Status</label>
            <select name="status" class="input">
                <option value="">Semua</option>
                @foreach (\App\Models\PengajuanPkl::STATUS_LABELS as $key => $label)
                    <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-secondary">Filter</button>
    </form>

    @if ($pengajuan->isEmpty())
        <div class="card p-10 text-center text-sm text-white/40">Belum ada pengajuan yang masuk.</div>
    @else
        <div class="space-y-3">
            @foreach ($pengajuan as $p)
                <a href="{{ route('company.pengajuan.show', $p) }}" class="card card-hover flex items-center justify-between gap-4 p-5">
                    <div>
                        <p class="font-semibold">{{ $p->ketua->nama_lengkap }}</p>
                        <p class="text-xs text-white/40">
                            {{ $p->kode_pengajuan }} · {{ ucfirst($p->jenis_pengajuan) }} ({{ $p->jumlahPeserta() }} peserta) ·
                            {{ $p->tgl_mulai_pkl->translatedFormat('d M Y') }} - {{ $p->tgl_selesai_pkl->translatedFormat('d M Y') }}
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
