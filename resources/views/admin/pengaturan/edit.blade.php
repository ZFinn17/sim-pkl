@extends('layouts.app')

@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan')

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="fade-in mx-auto max-w-2xl space-y-5">

    <h2 class="text-lg font-semibold">Pengaturan Sistem</h2>

    <form method="POST" action="{{ route('admin.pengaturan.update') }}" class="space-y-5" data-loading-text="Menyimpan...">
        @csrf
        @method('PUT')

        <div class="card space-y-4 p-6">
            <h3 class="text-sm font-semibold text-white/60">Identitas Sekolah (untuk KOP Surat)</h3>

            <div>
                <label for="sekolah_nama" class="label">Nama Sekolah</label>
                <input id="sekolah_nama" name="sekolah_nama" value="{{ old('sekolah_nama', $settings['sekolah_nama']) }}" class="input" required>
                @error('sekolah_nama') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="sekolah_alamat" class="label">Alamat</label>
                <textarea id="sekolah_alamat" name="sekolah_alamat" rows="2" class="input">{{ old('sekolah_alamat', $settings['sekolah_alamat']) }}</textarea>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="sekolah_telepon" class="label">Telepon</label>
                    <input id="sekolah_telepon" name="sekolah_telepon" value="{{ old('sekolah_telepon', $settings['sekolah_telepon']) }}" class="input">
                </div>
                <div>
                    <label for="sekolah_email" class="label">Email</label>
                    <input id="sekolah_email" type="email" name="sekolah_email" value="{{ old('sekolah_email', $settings['sekolah_email']) }}" class="input">
                    @error('sekolah_email') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="sekolah_website" class="label">Website</label>
                <input id="sekolah_website" name="sekolah_website" value="{{ old('sekolah_website', $settings['sekolah_website']) }}" class="input">
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="sekolah_kepala_sekolah" class="label">Nama Kepala Sekolah</label>
                    <input id="sekolah_kepala_sekolah" name="sekolah_kepala_sekolah" value="{{ old('sekolah_kepala_sekolah', $settings['sekolah_kepala_sekolah']) }}" class="input">
                </div>
                <div>
                    <label for="sekolah_nip_kepala_sekolah" class="label">NIP Kepala Sekolah</label>
                    <input id="sekolah_nip_kepala_sekolah" name="sekolah_nip_kepala_sekolah" value="{{ old('sekolah_nip_kepala_sekolah', $settings['sekolah_nip_kepala_sekolah']) }}" class="input">
                </div>
            </div>

            <p class="text-xs text-white/40">
                Logo sekolah diambil dari file di <code class="text-white/60">public/images/logo-sekolah.png</code> (atau path lain via <code class="text-white/60">SEKOLAH_LOGO_PATH</code> di .env). Upload file logo langsung ke folder tersebut melalui Laragon/File Explorer.
            </p>
        </div>

        <div class="card space-y-4 p-6">
            <h3 class="text-sm font-semibold text-white/60">Format Nomor Surat</h3>

            <div>
                <label for="surat_kode" class="label">Kode Surat</label>
                <input id="surat_kode" name="surat_kode" value="{{ old('surat_kode', $settings['surat_kode']) }}" class="input" required>
            </div>

            <div>
                <label for="surat_format" class="label">Format</label>
                <input id="surat_format" name="surat_format" value="{{ old('surat_format', $settings['surat_format']) }}" class="input" required>
                <p class="mt-1 text-xs text-white/40">
                    Placeholder yang tersedia: <code class="text-white/60">{sequence}</code> <code class="text-white/60">{code}</code>
                    <code class="text-white/60">{month}</code> <code class="text-white/60">{year}</code>.
                    Nomor surat yang sudah terbit tidak akan berubah; perubahan hanya berlaku untuk surat berikutnya.
                </p>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn-primary">Simpan Pengaturan</button>
        </div>
    </form>
</div>
@endsection
