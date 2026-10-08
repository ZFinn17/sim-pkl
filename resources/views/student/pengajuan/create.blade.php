@extends('layouts.app')

@section('title', 'Ajukan PKL')
@section('page-title', 'Pengajuan Baru')

@section('sidebar')
    @include('partials.sidebar-siswa')
@endsection

@section('content')
<div class="fade-in mx-auto max-w-2xl space-y-5">

    <div class="flex items-center gap-3">
        <a href="{{ route('student.perusahaan.index') }}" class="text-white/40 hover:text-white">&larr;</a>
        <h2 class="text-lg font-semibold">Ajukan PKL</h2>
    </div>

    @if ($errors->any())
        <div class="rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-sm text-red-400">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('student.pengajuan.store') }}" id="form-pengajuan">
        @csrf

        <div class="card space-y-5 p-6">
            <div>
                <label for="perusahaan_id" class="label">Perusahaan</label>
                <select id="perusahaan_id" name="perusahaan_id" class="input" required>
                    <option value="">-- Pilih Perusahaan --</option>
                    @foreach ($perusahaanAktif as $p)
                        <option value="{{ $p->id }}" @selected(old('perusahaan_id', $perusahaanTerpilih?->id) == $p->id)>
                            {{ $p->nama_perusahaan }} (kuota tersedia: {{ $p->kuota_tersedia }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="label">Jenis Pengajuan</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="card cursor-pointer p-4 text-center text-sm font-medium has-[:checked]:border-[#2563EB] has-[:checked]:bg-[#2563EB]/10">
                        <input type="radio" name="jenis_pengajuan" value="individual" class="peer sr-only" id="jenis-individual" {{ old('jenis_pengajuan', 'individual') === 'individual' ? 'checked' : '' }}>
                        Individu
                    </label>
                    <label class="card cursor-pointer p-4 text-center text-sm font-medium has-[:checked]:border-[#2563EB] has-[:checked]:bg-[#2563EB]/10">
                        <input type="radio" name="jenis_pengajuan" value="group" class="peer sr-only" id="jenis-group" {{ old('jenis_pengajuan') === 'group' ? 'checked' : '' }}>
                        Kelompok
                    </label>
                </div>
            </div>

            <div id="bagian-anggota" class="hidden">
                <label class="label">Pilih Anggota Kelompok (maksimal 2, total 3 dengan ketua)</label>
                <div class="max-h-48 space-y-1 overflow-y-auto rounded-xl border border-white/10 p-3">
                    @forelse ($calonAnggota as $siswa)
                        <label class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm hover:bg-white/5">
                            <input type="checkbox" name="anggota[]" value="{{ $siswa->id }}" class="anggota-checkbox rounded border-white/20 bg-white/5 text-[#2563EB] focus:ring-[#2563EB]"
                                @checked(collect(old('anggota', []))->contains($siswa->id))>
                            {{ $siswa->nama_lengkap }} <span class="text-white/40">({{ $siswa->nis_nip }} · {{ $siswa->kelas }})</span>
                        </label>
                    @empty
                        <p class="px-2 py-3 text-sm text-white/40">Tidak ada siswa lain yang tersedia.</p>
                    @endforelse
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="tgl_mulai_pkl" class="label">Tanggal Mulai PKL</label>
                    <input id="tgl_mulai_pkl" type="date" name="tgl_mulai_pkl" value="{{ old('tgl_mulai_pkl') }}" class="input" required>
                </div>
                <div>
                    <label for="tgl_selesai_pkl" class="label">Tanggal Selesai PKL</label>
                    <input id="tgl_selesai_pkl" type="date" name="tgl_selesai_pkl" value="{{ old('tgl_selesai_pkl') }}" class="input" required>
                </div>
            </div>
        </div>

        <div class="mt-5 flex justify-end gap-3">
            <a href="{{ route('student.perusahaan.index') }}" class="btn-secondary">Batal</a>
            <button type="button" data-modal-open="modal-konfirmasi" class="btn-primary">Kirim Pengajuan</button>
        </div>
    </form>

    {{-- Confirmation modal --}}
    <div id="modal-konfirmasi" data-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4">
        <div class="card w-full max-w-sm p-6">
            <h3 class="mb-2 font-semibold">Konfirmasi Pengajuan</h3>
            <p class="mb-5 text-sm text-white/60">
                Apakah Anda yakin ingin mengajukan PKL ke perusahaan ini? Setelah dikirim, data pengajuan akan diproses oleh Admin Hubin.
            </p>
            <div class="flex justify-end gap-3">
                <button type="button" data-modal-close class="btn-secondary">Batal</button>
                <button type="submit" form="form-pengajuan" class="btn-primary" data-loading-text="Mengirim...">Kirim Pengajuan</button>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const radioIndividual = document.getElementById('jenis-individual');
        const radioGroup = document.getElementById('jenis-group');
        const bagianAnggota = document.getElementById('bagian-anggota');
        const checkboxes = document.querySelectorAll('.anggota-checkbox');

        function toggleAnggota() {
            const isGroup = radioGroup.checked;
            bagianAnggota.classList.toggle('hidden', !isGroup);
            if (!isGroup) {
                checkboxes.forEach((cb) => { cb.checked = false; });
            }
        }

        function limitAnggota() {
            const checked = document.querySelectorAll('.anggota-checkbox:checked');
            checkboxes.forEach((cb) => {
                if (!cb.checked) cb.disabled = checked.length >= 2;
            });
        }

        radioIndividual.addEventListener('change', toggleAnggota);
        radioGroup.addEventListener('change', toggleAnggota);
        checkboxes.forEach((cb) => cb.addEventListener('change', limitAnggota));

        toggleAnggota();
        limitAnggota();
    })();
</script>
@endsection
