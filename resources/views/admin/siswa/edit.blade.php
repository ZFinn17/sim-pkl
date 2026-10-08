@extends('layouts.app')

@section('title', 'Edit Siswa')
@section('page-title', 'Siswa')

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="fade-in mx-auto max-w-2xl space-y-5">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.siswa.index') }}" class="text-white/40 hover:text-white">&larr;</a>
        <h2 class="text-lg font-semibold">Edit Siswa</h2>
    </div>

    <form method="POST" action="{{ route('admin.siswa.update', $siswa) }}" class="card space-y-4 p-6" data-loading-text="Menyimpan...">
        @csrf
        @method('PUT')

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="nis_nip" class="label">NIS</label>
                <input id="nis_nip" name="nis_nip" value="{{ old('nis_nip', $siswa->nis_nip) }}" class="input" required>
                @error('nis_nip') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="kelas" class="label">Kelas</label>
                <input id="kelas" name="kelas" value="{{ old('kelas', $siswa->kelas) }}" class="input" required>
                @error('kelas') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="nama_lengkap" class="label">Nama Lengkap</label>
            <input id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $siswa->nama_lengkap) }}" class="input" required>
            @error('nama_lengkap') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="email" class="label">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $siswa->email) }}" class="input" required>
                @error('email') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="no_hp" class="label">No. HP</label>
                <input id="no_hp" name="no_hp" value="{{ old('no_hp', $siswa->no_hp) }}" class="input">
                @error('no_hp') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>
        </div>

        <p class="text-xs text-white/40">
            Untuk mengganti password siswa, gunakan tombol "Reset PW" di halaman daftar siswa.
        </p>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('admin.siswa.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
