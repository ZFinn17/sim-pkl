@extends('layouts.app')

@section('title', 'Tambah Siswa')
@section('page-title', 'Siswa')

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="fade-in mx-auto max-w-2xl space-y-5">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.siswa.index') }}" class="text-white/40 hover:text-white">&larr;</a>
        <h2 class="text-lg font-semibold">Tambah Siswa</h2>
    </div>

    <form method="POST" action="{{ route('admin.siswa.store') }}" class="card space-y-4 p-6" data-loading-text="Menyimpan...">
        @csrf

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="nis_nip" class="label">NIS</label>
                <input id="nis_nip" name="nis_nip" value="{{ old('nis_nip') }}" class="input" required>
                @error('nis_nip') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="kelas" class="label">Kelas</label>
                <input id="kelas" name="kelas" value="{{ old('kelas') }}" placeholder="XI RPL 1" class="input" required>
                @error('kelas') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="nama_lengkap" class="label">Nama Lengkap</label>
            <input id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" class="input" required>
            @error('nama_lengkap') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="email" class="label">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" class="input" required>
                @error('email') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="no_hp" class="label">No. HP</label>
                <input id="no_hp" name="no_hp" value="{{ old('no_hp') }}" class="input">
                @error('no_hp') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="password" class="label">Password</label>
                <input id="password" type="password" name="password" class="input" required>
                @error('password') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password_confirmation" class="label">Konfirmasi Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" class="input" required>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('admin.siswa.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">Simpan Siswa</button>
        </div>
    </form>
</div>
@endsection
