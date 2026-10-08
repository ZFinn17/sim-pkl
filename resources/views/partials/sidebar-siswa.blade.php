@php $active = fn (string $route) => request()->routeIs($route) ? 'sidebar-link-active' : 'sidebar-link'; @endphp

<a href="{{ route('student.dashboard') }}" class="{{ $active('student.dashboard') }}">
    <span>Dashboard</span>
</a>

@if (Route::has('student.perusahaan.index'))
    <a href="{{ route('student.perusahaan.index') }}" class="{{ $active('student.perusahaan.*') }}">Perusahaan</a>
@else
    <span class="sidebar-link opacity-40 cursor-not-allowed">Perusahaan <em class="text-xs">(segera)</em></span>
@endif

@if (Route::has('student.pengajuan.index'))
    <a href="{{ route('student.pengajuan.index') }}" class="{{ $active('student.pengajuan.*') }}">Pengajuan Saya</a>
@else
    <span class="sidebar-link opacity-40 cursor-not-allowed">Pengajuan Saya <em class="text-xs">(segera)</em></span>
@endif

<a href="{{ route('notifikasi.index') }}" class="{{ $active('notifikasi.index') }}">Notifikasi</a>

<a href="{{ route('profile.show') }}" class="{{ $active('profile.show') }}">Profil</a>
