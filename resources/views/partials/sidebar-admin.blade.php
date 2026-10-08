@php $active = fn (string $route) => request()->routeIs($route) ? 'sidebar-link-active' : 'sidebar-link'; @endphp

<a href="{{ route('admin.dashboard') }}" class="{{ $active('admin.dashboard') }}">Dashboard</a>

@if (Route::has('admin.pengajuan.index'))
    <a href="{{ route('admin.pengajuan.index') }}" class="{{ $active('admin.pengajuan.*') }}">Pengajuan</a>
@else
    <span class="sidebar-link opacity-40 cursor-not-allowed">Pengajuan <em class="text-xs">(segera)</em></span>
@endif

<a href="{{ route('admin.siswa.index') }}" class="{{ $active('admin.siswa.*') }}">Siswa</a>
<a href="{{ route('admin.perusahaan.index') }}" class="{{ $active('admin.perusahaan.*') }}">Perusahaan</a>
<a href="{{ route('admin.guru.index') }}" class="{{ $active('admin.guru.*') }}">Guru Pembimbing</a>

<a href="{{ route('admin.surat.index') }}" class="{{ $active('admin.surat.*') }}">Surat</a>
<a href="{{ route('notifikasi.index') }}" class="{{ $active('notifikasi.index') }}">Notifikasi</a>
<a href="{{ route('admin.audit-log.index') }}" class="{{ $active('admin.audit-log.*') }}">Audit Log</a>
<a href="{{ route('admin.pengaturan.edit') }}" class="{{ $active('admin.pengaturan.*') }}">Pengaturan</a>

<div class="my-2 border-t border-white/10"></div>
<a href="{{ route('profile.show') }}" class="{{ $active('profile.show') }}">Profil</a>
