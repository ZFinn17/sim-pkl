@php $active = fn (string $route) => request()->routeIs($route) ? 'sidebar-link-active' : 'sidebar-link'; @endphp

<a href="{{ route('company.dashboard') }}" class="{{ $active('company.dashboard') }}">Dashboard</a>

@if (Route::has('company.pengajuan.index'))
    <a href="{{ route('company.pengajuan.index') }}" class="{{ $active('company.pengajuan.*') }}">Pengajuan Masuk</a>
@else
    <span class="sidebar-link opacity-40 cursor-not-allowed">Pengajuan Masuk <em class="text-xs">(segera)</em></span>
@endif

<a href="{{ route('notifikasi.index') }}" class="{{ $active('notifikasi.index') }}">Notifikasi</a>

<a href="{{ route('profile.show') }}" class="{{ $active('profile.show') }}">Profil</a>
