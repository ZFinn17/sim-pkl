@extends('layouts.app')

@section('title', 'Katalog Perusahaan')
@section('page-title', 'Perusahaan')

@section('sidebar')
    @include('partials.sidebar-siswa')
@endsection

@section('content')
<div class="fade-in space-y-5">

    <div>
        <h2 class="text-lg font-semibold">Katalog Perusahaan</h2>
        <p class="text-sm text-white/50">Pilih perusahaan mitra untuk mengajukan PKL.</p>
    </div>

    <form method="GET" class="card flex flex-wrap items-end gap-3 p-4">
        <div class="min-w-[220px] flex-1">
            <label class="label">Cari Perusahaan</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama perusahaan..." class="input">
        </div>
        <label class="flex items-center gap-2 pb-2.5 text-sm text-white/60">
            <input type="checkbox" name="hanya_tersedia" value="1" @checked(request('hanya_tersedia')) class="rounded border-white/20 bg-white/5 text-[#2563EB] focus:ring-[#2563EB]">
            Hanya yang masih ada kuota
        </label>
        <button type="submit" class="btn-secondary">Filter</button>
    </form>

    @if ($perusahaan->isEmpty())
        <div class="card p-10 text-center text-sm text-white/40">Belum ada perusahaan yang tersedia.</div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($perusahaan as $p)
                <div class="card card-hover flex flex-col p-5">
                    <p class="font-semibold">{{ $p->nama_perusahaan }}</p>
                    <p class="mt-1 line-clamp-2 text-sm text-white/50">{{ $p->deskripsi ?? 'Tidak ada deskripsi.' }}</p>
                    <div class="mt-4 flex items-center justify-between text-sm">
                        <span class="text-white/40">Kuota tersedia</span>
                        <span class="font-semibold {{ $p->kuota_tersedia > 0 ? 'text-[#22C55E]' : 'text-[#EF4444]' }}">
                            {{ $p->kuota_tersedia }} / {{ $p->kuota_maksimal }}
                        </span>
                    </div>
                    <a href="{{ route('student.perusahaan.show', $p) }}" class="btn-secondary mt-4">Lihat Detail</a>
                </div>
            @endforeach
        </div>

        <div>{{ $perusahaan->links() }}</div>
    @endif
</div>
@endsection
