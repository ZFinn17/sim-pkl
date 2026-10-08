@extends('layouts.app')

@section('title', $pengajuan->kode_pengajuan)
@section('page-title', 'Detail Pengajuan')

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="fade-in mx-auto max-w-2xl space-y-5">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.pengajuan.index') }}" class="text-white/40 hover:text-white">&larr;</a>
        <h2 class="text-lg font-semibold">{{ $pengajuan->kode_pengajuan }}</h2>
        <span class="badge-{{ $pengajuan->status }}">{{ $pengajuan->statusLabel() }}</span>
    </div>

    <div class="card p-6">
        <h3 class="mb-3 text-sm font-semibold text-white/60">Data Siswa</h3>
        <dl class="mb-5 divide-y divide-white/5 text-sm">
            <div class="flex justify-between py-2.5">
                <dt class="text-white/40">Ketua</dt>
                <dd>{{ $pengajuan->ketua->nama_lengkap }} ({{ $pengajuan->ketua->nis_nip }})</dd>
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

        <h3 class="mb-3 text-sm font-semibold text-white/60">Data Pengajuan</h3>
        <dl class="mb-5 divide-y divide-white/5 text-sm">
            <div class="flex justify-between py-2.5">
                <dt class="text-white/40">Perusahaan</dt>
                <dd>{{ $pengajuan->perusahaan->nama_perusahaan }}</dd>
            </div>
            <div class="flex justify-between py-2.5">
                <dt class="text-white/40">Jenis</dt>
                <dd>{{ ucfirst($pengajuan->jenis_pengajuan) }} ({{ $pengajuan->jumlahPeserta() }} peserta)</dd>
            </div>
            <div class="flex justify-between py-2.5">
                <dt class="text-white/40">Periode PKL</dt>
                <dd>{{ $pengajuan->tgl_mulai_pkl->translatedFormat('d M Y') }} - {{ $pengajuan->tgl_selesai_pkl->translatedFormat('d M Y') }}</dd>
            </div>
            <div class="flex justify-between py-2.5">
                <dt class="text-white/40">Tanggal Pengajuan</dt>
                <dd>{{ $pengajuan->tgl_pengajuan->translatedFormat('d M Y') }}</dd>
            </div>
            <div class="flex justify-between py-2.5">
                <dt class="text-white/40">Guru Pembimbing</dt>
                <dd>{{ $pengajuan->guruPembimbing?->nama_lengkap ?? 'Belum ditentukan' }}</dd>
            </div>
            @if ($pengajuan->approvedBy)
                <div class="flex justify-between py-2.5">
                    <dt class="text-white/40">Divalidasi oleh</dt>
                    <dd>{{ $pengajuan->approvedBy->nama_lengkap }} · {{ $pengajuan->approved_at->translatedFormat('d M Y, H:i') }}</dd>
                </div>
            @endif
        </dl>

        @if ($pengajuan->status === \App\Models\PengajuanPkl::STATUS_REJECTED)
            <div class="mb-5 rounded-xl border border-red-500/20 bg-red-500/10 p-4 text-sm text-red-400">
                <p class="mb-1 font-medium">Alasan Penolakan:</p>
                <p>{{ $pengajuan->alasan_penolakan }}</p>
                @if ($pengajuan->reopened_at)
                    <p class="mt-2 text-xs text-white/50">Sudah dibuka kembali pada {{ $pengajuan->reopened_at->translatedFormat('d M Y, H:i') }} — siswa dapat mengajukan ulang ke perusahaan ini.</p>
                @endif
            </div>
        @endif

        @if ($pengajuan->suratPengantar)
            <div class="mb-5 rounded-xl border border-green-500/20 bg-green-500/10 p-4 text-sm text-green-400">
                <p class="mb-1 font-medium">Surat Pengantar Terbit: {{ $pengajuan->suratPengantar->nomor_surat }}</p>
                <p class="mb-2 text-white/50">Diterbitkan {{ $pengajuan->suratPengantar->generated_at->translatedFormat('d M Y, H:i') }}</p>
                <a href="{{ route('surat.download', $pengajuan->suratPengantar) }}" class="btn-secondary">Download PDF</a>
            </div>
        @endif

        @if ($pengajuan->suratBalasan->isNotEmpty())
            <div class="mb-5">
                <h3 class="mb-2 text-sm font-semibold text-white/60">Surat Balasan Perusahaan</h3>
                <div class="space-y-2">
                    @foreach ($pengajuan->suratBalasan as $balasan)
                        <div class="flex items-center justify-between rounded-xl border border-white/10 p-3 text-sm">
                            <div>
                                <p class="font-medium">{{ $balasan->original_filename }}</p>
                                <p class="text-xs text-white/40">
                                    {{ $balasan->fileSizeReadable() }} · diupload {{ $balasan->uploader->nama_lengkap }} ·
                                    {{ $balasan->uploaded_at->translatedFormat('d M Y, H:i') }}
                                </p>
                            </div>
                            <a href="{{ route('surat-balasan.download', $balasan) }}" class="text-[#3B82F6] hover:underline shrink-0">Download</a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($pengajuan->status === \App\Models\PengajuanPkl::STATUS_HUBIN_APPROVED && ! $pengajuan->suratPengantar)
            <div class="mb-5 rounded-xl border border-yellow-500/20 bg-yellow-500/10 p-4 text-sm text-yellow-300">
                Pengajuan tervalidasi namun surat pengantar belum berhasil dibuat. Coba proses Approve kembali.
            </div>
        @endif

        {{-- Actions --}}
        <div class="flex flex-wrap gap-3">
            @if ($pengajuan->status === \App\Models\PengajuanPkl::STATUS_SUBMITTED)
                <button type="button" data-modal-open="modal-approve" class="btn-primary">Approve & Terbitkan Surat</button>
            @endif

            @if (in_array($pengajuan->status, ['submitted', 'hubin_approved']))
                <button type="button" data-modal-open="modal-tolak" class="btn-danger">Tolak</button>
            @endif

            @if ($pengajuan->status === \App\Models\PengajuanPkl::STATUS_REJECTED && ! $pengajuan->reopened_at)
                <form method="POST" action="{{ route('admin.pengajuan.reopen', $pengajuan) }}" onsubmit="return confirm('Buka kembali kesempatan mengajukan ulang ke perusahaan ini?')">
                    @csrf
                    <button type="submit" class="btn-secondary">Buka Kembali (Reopen)</button>
                </form>
            @endif

            @if ($pengajuan->status === \App\Models\PengajuanPkl::STATUS_ACCEPTED)
                <form method="POST" action="{{ route('admin.pengajuan.cancel-accepted', $pengajuan) }}" onsubmit="return confirm('Batalkan pengajuan yang sudah diterima ini? Kuota perusahaan akan dikembalikan.')">
                    @csrf
                    <button type="submit" class="btn-danger">Batalkan (Kuota Dikembalikan)</button>
                </form>
            @endif
        </div>
    </div>

    {{-- Approve modal --}}
    <div id="modal-approve" data-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4">
        <div class="card w-full max-w-sm p-6">
            <h3 class="mb-2 font-semibold">Approve & Terbitkan Surat Pengantar</h3>
            <p class="mb-4 text-sm text-white/50">
                Menetapkan guru pembimbing dan langsung menerbitkan surat pengantar PKL (PDF + QR verification).
            </p>
            <form method="POST" action="{{ route('admin.pengajuan.approve', $pengajuan) }}" data-loading-text="Memproses & membuat surat...">
                @csrf
                <label for="guru_pembimbing_id" class="label">Guru Pembimbing</label>
                <select id="guru_pembimbing_id" name="guru_pembimbing_id" class="input mb-4" required>
                    <option value="">-- Pilih Guru Pembimbing --</option>
                    @forelse ($guruAktif as $g)
                        <option value="{{ $g->id }}">{{ $g->nama_lengkap }} ({{ $g->nip }})</option>
                    @empty
                        <option value="" disabled>Belum ada guru pembimbing aktif</option>
                    @endforelse
                </select>
                <div class="flex justify-end gap-3">
                    <button type="button" data-modal-close class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary" @if($guruAktif->isEmpty()) disabled @endif>Approve & Buat Surat</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Reject modal --}}
    <div id="modal-tolak" data-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4">
        <div class="card w-full max-w-sm p-6">
            <h3 class="mb-3 font-semibold">Tolak Pengajuan</h3>
            <form method="POST" action="{{ route('admin.pengajuan.reject', $pengajuan) }}">
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
