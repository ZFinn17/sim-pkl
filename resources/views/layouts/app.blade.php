<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIM PKL') - SMK ICB Cinta Niaga</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#0A0A0A] text-white">

    <div class="flex min-h-screen">

        {{-- Mobile drawer overlay --}}
        <div data-drawer-overlay class="fixed inset-0 z-40 hidden bg-black/60 lg:hidden"></div>

        {{-- Sidebar --}}
        <aside
            data-drawer
            class="fixed inset-y-0 left-0 z-50 w-64 -translate-x-full transform border-r border-white/10
                   bg-[#0A0A0A] transition-transform duration-200 lg:static lg:translate-x-0"
        >
            <div class="flex h-16 items-center gap-2 border-b border-white/10 px-6">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#2563EB] text-sm font-bold">P</div>
                <span class="text-lg font-semibold tracking-tight">SIM PKL</span>
            </div>

            <nav class="space-y-1 px-3 py-4">
                @yield('sidebar')
            </nav>
        </aside>

        {{-- Main content --}}
        <div class="flex flex-1 flex-col lg:pl-0">

            {{-- Top navbar --}}
            <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-white/10 bg-[#0A0A0A]/95 px-4 backdrop-blur lg:px-8">
                <button data-drawer-toggle class="rounded-lg p-2 text-white/70 hover:bg-white/5 lg:hidden" aria-label="Buka menu">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <h1 class="hidden text-base font-semibold text-white/90 lg:block">@yield('page-title', 'Dashboard')</h1>

                @php
                    $navUnreadCount = auth()->user()?->unreadNotifications->count() ?? 0;
                    $navRecentNotifications = auth()->user()?->notifications()->latest()->take(5)->get() ?? collect();
                @endphp

                <div class="flex items-center gap-3">
                    <div class="relative">
                        <button data-dropdown-toggle="notif-menu" class="relative rounded-lg p-2 text-white/70 hover:bg-white/5" aria-label="Notifikasi">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            @if ($navUnreadCount > 0)
                                <span class="absolute -right-0.5 -top-0.5 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-[#EF4444] px-1 text-[10px] font-bold leading-none">
                                    {{ $navUnreadCount > 9 ? '9+' : $navUnreadCount }}
                                </span>
                            @endif
                        </button>
                        <div id="notif-menu" data-dropdown-menu class="hidden absolute right-0 top-12 w-80 rounded-xl border border-white/10 bg-[#171717] p-2 shadow-lg">
                            @forelse ($navRecentNotifications as $notification)
                                <a href="{{ $notification->data['url'] ?? route('notifikasi.index') }}" class="block rounded-lg px-3 py-2.5 text-sm hover:bg-white/5">
                                    <p class="font-medium {{ $notification->read_at ? 'text-white/60' : 'text-white' }}">{{ $notification->data['title'] ?? 'Notifikasi' }}</p>
                                    <p class="truncate text-xs text-white/40">{{ $notification->data['message'] ?? '' }}</p>
                                </a>
                            @empty
                                <p class="px-2 py-3 text-center text-sm text-white/40">Belum ada notifikasi</p>
                            @endforelse
                            <a href="{{ route('notifikasi.index') }}" class="mt-1 block rounded-lg px-3 py-2 text-center text-xs text-[#3B82F6] hover:bg-white/5">
                                Lihat semua notifikasi
                            </a>
                        </div>
                    </div>

                    <div class="relative">
                        <button data-dropdown-toggle="profile-menu" class="flex items-center gap-2 rounded-lg px-2 py-1.5 hover:bg-white/5">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-white/10 text-sm font-semibold">
                                {{ auth()->check() ? strtoupper(substr(auth()->user()->nama_lengkap, 0, 1)) : '?' }}
                            </div>
                            <span class="hidden text-sm font-medium text-white/80 sm:block">
                                {{ auth()->user()->nama_lengkap ?? 'Pengguna' }}
                            </span>
                        </button>
                        <div id="profile-menu" data-dropdown-menu class="hidden absolute right-0 top-12 w-48 rounded-xl border border-white/10 bg-[#171717] p-2 shadow-lg">
                            <a href="{{ route('profile.show') }}" class="block rounded-lg px-3 py-2 text-sm text-white/80 hover:bg-white/5">Profil Saya</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-sm text-red-400 hover:bg-white/5">
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Flash messages --}}
            <div class="px-4 pt-4 lg:px-8">
                @if (session('success'))
                    <div data-flash class="fade-in mb-4 rounded-xl border border-green-500/20 bg-green-500/10 px-4 py-3 text-sm text-green-400">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div data-flash class="fade-in mb-4 rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-sm text-red-400">
                        {{ session('error') }}
                    </div>
                @endif
            </div>

            <main class="flex-1 px-4 py-6 lg:px-8">
                @yield('content')
            </main>
        </div>
    </div>

</body>
</html>
