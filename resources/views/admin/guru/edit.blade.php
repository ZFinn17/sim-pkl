@extends('layouts.app')

@section('title', 'Edit Guru Pembimbing')
@section('page-title', 'Guru Pembimbing')

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="fade-in mx-auto max-w-2xl space-y-5">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.guru.index') }}" class="text-white/40 hover:text-white">&larr;</a>
        <h2 class="text-lg font-semibold">Edit Guru Pembimbing</h2>
    </div>

    <form method="POST" action="{{ route('admin.guru.update', $guru) }}" class="card space-y-4 p-6" data-loading-text="Menyimpan...">
        @csrf
        @method('PUT')

        <div>
            <label for="nip" class="label">NIP</label>
            <input id="nip" name="nip" value="{{ old('nip', $guru->nip) }}" class="input" required>
            @error('nip') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="nama_lengkap" class="label">Nama Lengkap</label>
            <input id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $guru->nama_lengkap) }}" class="input" required>
            @error('nama_lengkap') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="no_hp" class="label">No. HP</label>
                <input id="no_hp" name="no_hp" value="{{ old('no_hp', $guru->no_hp) }}" class="input">
                @error('no_hp') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="email" class="label">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $guru->email) }}" class="input">
                @error('email') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="status" class="label">Status</label>
            <select id="status" name="status" class="input" required>
                <option value="aktif" @selected(old('status', $guru->status) === 'aktif')>Aktif</option>
                <option value="nonaktif" @selected(old('status', $guru->status) === 'nonaktif')>Nonaktif</option>
            </select>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('admin.guru.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
