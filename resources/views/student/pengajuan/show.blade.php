@extends('layouts.app')

@section('title', $pengajuan->kode_pengajuan)
@section('page-title', 'Detail Pengajuan')

@section('sidebar')
    @include('partials.sidebar-siswa')
@endsection

@php
    $steps = [
        \App\Models\PengajuanPkl::STATUS_SUBMITTED => 'Pengajuan Dikirim',
        \App\Models\PengajuanPkl::STATUS_HUBIN_APPROVED => 'Diproses Hubin',
        \App\Models\PengajuanPkl::STATUS_WAITING_COMPANY => 'Menunggu Perusahaan',
        \App\Models\PengajuanPkl::STATUS_ACCEPTED => 'Diterima',
    ];
    $isRejected = $pengajuan->status === \App\Models\PengajuanPkl::STATUS_REJECTED;
    $isCancelled = $pengajuan->status === \App\Models\PengajuanPkl::STATUS_CANCELLED;
    $stepKeys = array_keys($steps);
    $currentIndex = array_search($pengajuan->status, $stepKeys, true);
@endphp

@section('content')
<div class="fade-in mx-auto max-w-2xl space-y-5">

    <div class="flex items-center gap-3">
        <a href="{{ route('student.pengajuan.index') }}" class="text-white/40 hover:text-white">&larr;</a>
        <h2 class="text-lg font-semibold">{{ $pengajuan->kode_pengajuan }}</h2>
        <span class="badge-{{ $pengajuan->status }}">{{ $pengajuan->statusLabel() }}</span>
    </div>

    {{-- Info card --}}
    <div class="card p-6">
        <dl class="divide-y divide-white/5 text-sm">
            <div class="flex justify-between py-3">
                <dt class="text-white/40">Perusahaan</dt>
                <dd class="font-medium">{{ $pengajuan->perusahaan->nama_perusahaan }}</dd>
            </div>
            <div class="flex justify-between py-3">
                <dt class="text-white/40">Jenis Pengajuan</dt>
                <dd>{{ ucfirst($pengajuan->jenis_pengajuan) }} ({{ $pengajuan->jumlahPeserta() }} peserta)</dd>
            </div>
            @if ($pengajuan->isGroup())
                <div class="flex justify-between py-3">
                    <dt class="text-white/40">Anggota</dt>
                    <dd class="text-right">
                        @foreach ($pengajuan->anggotaKelompok as $a)
                            <div>{{ $a->siswa->nama_lengkap }}</div>
                        @endforeach
                    </dd>
                </div>
            @endif
            <div class="flex justify-between py-3">
                <dt class="text-white/40">Periode PKL</dt>
                <dd>{{ $pengajuan->tgl_mulai_pkl->translatedFormat('d M Y') }} - {{ $pengajuan->tgl_selesai_pkl->translatedFormat('d M Y') }}</dd>
            </div>
            <div class="flex justify-between py-3">
                <dt class="text-white/40">Guru Pembimbing</dt>
                <dd>{{ $pengajuan->guruPembimbing?->nama_lengkap ?? 'Belum ditentukan' }}</dd>
            </div>
        </dl>
    </div>

    {{-- Timeline --}}
    <div class="card p-6">
        <h3 class="mb-5 font-semibold">Status Pengajuan</h3>

        @if ($isCancelled)
            <div class="flex items-center gap-3 text-sm text-white/50">
                <span class="h-2.5 w-2.5 rounded-full bg-white/30"></span>
                Pengajuan ini telah dibatalkan.
            </div>
        @else
            <ol class="relative ml-3 space-y-6 border-l border-white/10 pl-6">
                @foreach ($steps as $key => $label)
                    @php
                        $stepIndex = array_search($key, $stepKeys, true);
                        $isDone = ! $isRejected && $currentIndex !== false && $stepIndex <= $currentIndex;
                        $isCurrent = $key === $pengajuan->status;
                    @endphp
                    <li class="relative">
                        <span class="absolute -left-[31px] flex h-4 w-4 items-center justify-center rounded-full
                            {{ $isDone ? 'bg-[#2563EB]' : 'bg-white/10' }}"></span>
                        <p class="text-sm font-medium {{ $isDone ? 'text-white' : 'text-white/40' }}">{{ $label }}</p>
                        @if ($isCurrent && $key === 'submitted')
                            <p class="text-xs text-white/40">{{ $pengajuan->tgl_pengajuan->translatedFormat('d M Y') }}</p>
                        @endif
                        @if ($key === 'hubin_approved' && $pengajuan->approved_at)
                            <p class="text-xs text-white/40">{{ $pengajuan->approved_at->translatedFormat('d M Y, H:i') }}</p>
                        @endif
                        @if ($key === 'accepted' && $pengajuan->accepted_at)
                            <p class="text-xs text-white/40">{{ $pengajuan->accepted_at->translatedFormat('d M Y, H:i') }}</p>
                        @endif
                    </li>
                @endforeach

                @if ($isRejected)
                    <li class="relative">
                        <span class="absolute -left-[31px] flex h-4 w-4 items-center justify-center rounded-full bg-[#EF4444]"></span>
                        <p class="text-sm font-medium text-[#EF4444]">Ditolak</p>
                        <p class="text-xs text-white/40">{{ $pengajuan->rejected_at?->translatedFormat('d M Y, H:i') }}</p>
                    </li>
                @endif
            </ol>

            @if ($isRejected && $pengajuan->alasan_penolakan)
                <div class="mt-5 rounded-xl border border-red-500/20 bg-red-500/10 p-4 text-sm text-red-400">
                    <p class="mb-1 font-medium">Alasan Penolakan:</p>
                    <p>{{ $pengajuan->alasan_penolakan }}</p>
                </div>
            @endif
        @endif
    </div>

    {{-- Surat pengantar --}}
    @if ($pengajuan->suratPengantar)
        <div class="card p-6">
            <h3 class="mb-3 font-semibold">Surat Pengantar PKL</h3>
            <p class="mb-4 text-sm text-white/50">Nomor: {{ $pengajuan->suratPengantar->nomor_surat }}</p>
            @if (Route::has('surat.download'))
                <a href="{{ route('surat.download', $pengajuan->suratPengantar) }}" class="btn-primary">Download PDF</a>
            @endif
        </div>
    @endif

    {{-- Surat balasan upload --}}
    @if ($pengajuan->status === \App\Models\PengajuanPkl::STATUS_ACCEPTED)
        <div class="card p-6">
            <h3 class="mb-3 font-semibold">Surat Balasan Perusahaan</h3>

            @forelse ($pengajuan->suratBalasan as $balasan)
                <div class="mb-3 flex items-center justify-between rounded-xl border border-white/10 p-3 text-sm">
                    <div>
                        <p class="font-medium">{{ $balasan->original_filename }}</p>
                        <p class="text-xs text-white/40">{{ $balasan->fileSizeReadable() }} · {{ $balasan->uploaded_at->translatedFormat('d M Y') }}</p>
                    </div>
                    <a href="{{ route('surat-balasan.download', $balasan) }}" class="text-[#3B82F6] hover:underline shrink-0">Download</a>
                </div>
            @empty
                <p class="mb-3 text-sm text-white/40">Belum ada surat balasan yang diupload.</p>
            @endforelse

            <form method="POST" action="{{ route('student.pengajuan.surat-balasan', $pengajuan) }}" enctype="multipart/form-data" data-loading-text="Mengupload...">
                @csrf
                <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" class="input mb-3" required>
                <p class="mb-3 text-xs text-white/40">Format PDF, JPG, atau PNG. Maksimal 5 MB.</p>
                <button type="submit" class="btn-secondary">Upload Surat Balasan</button>
            </form>
        </div>
    @endif

    {{-- Cancel action --}}
    @if ($pengajuan->canBeCancelledByStudent())
        <form method="POST" action="{{ route('student.pengajuan.cancel', $pengajuan) }}" onsubmit="return confirm('Batalkan pengajuan ini?')">
            @csrf
            <button type="submit" class="btn-danger w-full">Batalkan Pengajuan</button>
        </form>
    @endif
</div>
@endsection
