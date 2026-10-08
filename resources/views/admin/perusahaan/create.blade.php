@extends('layouts.app')

@section('title', 'Tambah Perusahaan')
@section('page-title', 'Perusahaan')

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="fade-in mx-auto max-w-2xl space-y-5">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.perusahaan.index') }}" class="text-white/40 hover:text-white">&larr;</a>
        <h2 class="text-lg font-semibold">Tambah Perusahaan</h2>
    </div>

    <form method="POST" action="{{ route('admin.perusahaan.store') }}" class="card space-y-4 p-6" data-loading-text="Menyimpan...">
        @csrf

        <div>
            <label for="nama_perusahaan" class="label">Nama Perusahaan</label>
            <input id="nama_perusahaan" name="nama_perusahaan" value="{{ old('nama_perusahaan') }}" class="input" required>
            @error('nama_perusahaan') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="alamat_lengkap" class="label">Alamat Lengkap</label>
            <textarea id="alamat_lengkap" name="alamat_lengkap" rows="2" class="input">{{ old('alamat_lengkap') }}</textarea>
            @error('alamat_lengkap') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="nama_pic" class="label">Nama PIC</label>
                <input id="nama_pic" name="nama_pic" value="{{ old('nama_pic') }}" class="input" required>
                @error('nama_pic') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="kontak_hrd" class="label">Kontak HRD</label>
                <input id="kontak_hrd" name="kontak_hrd" value="{{ old('kontak_hrd') }}" class="input" required>
                @error('kontak_hrd') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="email_hrd" class="label">Email HRD</label>
            <input id="email_hrd" type="email" name="email_hrd" value="{{ old('email_hrd') }}" class="input" required>
            @error('email_hrd') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="kuota_maksimal" class="label">Kuota Maksimal</label>
                <input id="kuota_maksimal" type="number" min="0" name="kuota_maksimal" value="{{ old('kuota_maksimal', 0) }}" class="input" required>
                @error('kuota_maksimal') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="status_mitra" class="label">Status Mitra</label>
                <select id="status_mitra" name="status_mitra" class="input" required>
                    <option value="aktif" @selected(old('status_mitra', 'aktif') === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected(old('status_mitra') === 'nonaktif')>Nonaktif</option>
                </select>
            </div>
        </div>

        <div>
            <label for="deskripsi" class="label">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" rows="3" class="input">{{ old('deskripsi') }}</textarea>
        </div>

        <div class="border-t border-white/10 pt-4">
            <label for="email_login" class="label">Email Login Akun Perusahaan</label>
            <input id="email_login" type="email" name="email_login" value="{{ old('email_login') }}" class="input" required>
            @error('email_login') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            <p class="mt-1 text-xs text-white/40">Password sementara akan dibuat otomatis dan ditampilkan setelah disimpan.</p>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('admin.perusahaan.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">Simpan Perusahaan</button>
        </div>
    </form>
</div>
@endsection
