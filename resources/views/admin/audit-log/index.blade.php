@extends('layouts.app')

@section('title', 'Audit Log')
@section('page-title', 'Audit Log')

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="fade-in space-y-5">

    <h2 class="text-lg font-semibold">Audit Log</h2>

    <form method="GET" class="card flex flex-wrap items-end gap-3 p-4">
        <div class="min-w-[220px] flex-1">
            <label class="label">Cari Deskripsi</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Kata kunci..." class="input">
        </div>
        <div class="w-48">
            <label class="label">Modul</label>
            <select name="module" class="input">
                <option value="">Semua</option>
                @foreach ($modules as $module)
                    <option value="{{ $module }}" @selected(request('module') === $module)>{{ ucfirst($module) }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-40">
            <label class="label">Dari</label>
            <input type="date" name="dari_tanggal" value="{{ request('dari_tanggal') }}" class="input">
        </div>
        <div class="w-40">
            <label class="label">Sampai</label>
            <input type="date" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}" class="input">
        </div>
        <button type="submit" class="btn-secondary">Filter</button>
    </form>

    @if ($logs->isEmpty())
        <div class="card p-10 text-center text-sm text-white/40">Belum ada aktivitas tercatat.</div>
    @else
        <div class="card divide-y divide-white/5">
            @foreach ($logs as $log)
                <div class="flex flex-wrap items-start justify-between gap-2 px-5 py-4 text-sm">
                    <div>
                        <p>
                            <span class="font-medium">{{ $log->user->nama_lengkap ?? 'Sistem' }}</span>
                            <span class="text-white/60">{{ $log->description }}</span>
                        </p>
                        <p class="mt-1 text-xs text-white/30">
                            {{ $log->created_at->translatedFormat('d M Y, H:i') }} · Modul: {{ $log->module }} · Aksi: {{ $log->action }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

        <div>{{ $logs->links() }}</div>
    @endif
</div>
@endsection
