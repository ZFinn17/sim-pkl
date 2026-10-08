@extends('layouts.app')

@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')

@section('sidebar')
    @if (auth()->user()->isAdmin())
        @include('partials.sidebar-admin')
    @elseif (auth()->user()->isPerusahaan())
        @include('partials.sidebar-perusahaan')
    @else
        @include('partials.sidebar-siswa')
    @endif
@endsection

@section('content')
<div class="fade-in space-y-5">

    <div class="flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-lg font-semibold">Notifikasi</h2>
        @if (auth()->user()->unreadNotifications->isNotEmpty())
            <form method="POST" action="{{ route('notifikasi.mark-all-read') }}">
                @csrf
                <button type="submit" class="text-sm text-[#3B82F6] hover:underline">Tandai semua dibaca</button>
            </form>
        @endif
    </div>

    @if ($notifications->isEmpty())
        <div class="card p-10 text-center text-sm text-white/40">Belum ada notifikasi.</div>
    @else
        <div class="card divide-y divide-white/5">
            @foreach ($notifications as $notification)
                <form method="POST" action="{{ route('notifikasi.read', $notification->id) }}">
                    @csrf
                    <button type="submit" class="flex w-full items-start justify-between gap-4 px-5 py-4 text-left text-sm hover:bg-white/[0.02]">
                        <div>
                            <p class="font-medium {{ $notification->read_at ? 'text-white/60' : 'text-white' }}">
                                {{ $notification->data['title'] ?? 'Notifikasi' }}
                            </p>
                            <p class="text-white/50">{{ $notification->data['message'] ?? '' }}</p>
                            <p class="mt-1 text-xs text-white/30">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        @unless ($notification->read_at)
                            <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-[#3B82F6]"></span>
                        @endunless
                    </button>
                </form>
            @endforeach
        </div>

        <div>{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
