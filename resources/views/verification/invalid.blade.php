@extends('layouts.guest')

@section('title', 'Verifikasi Dokumen')

@section('content')
<div class="flex min-h-screen items-center justify-center px-4">
    <div class="w-full max-w-md fade-in">

        <div class="card p-8 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-[#EF4444]/15">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-[#EF4444]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>

            <h1 class="mb-1 text-lg font-semibold">Dokumen Tidak Dapat Diverifikasi</h1>
            <p class="text-sm text-white/50">
                Tautan verifikasi tidak valid atau dokumen tidak ditemukan dalam sistem kami.
            </p>
        </div>

        <p class="mt-6 text-center text-xs text-white/30">SIM PKL — {{ config('simpkl.sekolah.nama') }}</p>
    </div>
</div>
@endsection
