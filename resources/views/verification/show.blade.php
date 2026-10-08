@extends('layouts.guest')

@section('title', 'Verifikasi Dokumen')

@section('content')
<div class="flex min-h-screen items-center justify-center px-4">
    <div class="w-full max-w-md fade-in">

        <div class="card p-8 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-[#22C55E]/15">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-[#22C55E]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <h1 class="mb-1 text-lg font-semibold">Dokumen Terverifikasi</h1>
            <p class="mb-6 text-sm text-white/50">Surat pengantar PKL ini sah diterbitkan oleh sistem.</p>

            <dl class="divide-y divide-white/5 text-left text-sm">
                <div class="flex justify-between py-3">
                    <dt class="text-white/40">Nomor Surat</dt>
                    <dd class="font-medium">{{ $surat->nomor_surat }}</dd>
                </div>
                <div class="flex justify-between py-3">
                    <dt class="text-white/40">Sekolah</dt>
                    <dd>{{ config('simpkl.sekolah.nama') }}</dd>
                </div>
                <div class="flex justify-between py-3">
                    <dt class="text-white/40">Perusahaan</dt>
                    <dd>{{ $pengajuan->perusahaan->nama_perusahaan }}</dd>
                </div>
                <div class="flex justify-between py-3">
                    <dt class="text-white/40">Periode PKL</dt>
                    <dd class="text-right">
                        {{ $pengajuan->tgl_mulai_pkl->translatedFormat('d M Y') }} -
                        {{ $pengajuan->tgl_selesai_pkl->translatedFormat('d M Y') }}
                    </dd>
                </div>
                <div class="flex justify-between py-3">
                    <dt class="text-white/40">Tanggal Surat</dt>
                    <dd>{{ $surat->tanggal_surat->translatedFormat('d M Y') }}</dd>
                </div>
                <div class="flex justify-between py-3">
                    <dt class="text-white/40">Status</dt>
                    <dd class="font-medium text-[#22C55E]">Valid</dd>
                </div>
            </dl>
        </div>

        <p class="mt-6 text-center text-xs text-white/30">SIM PKL — {{ config('simpkl.sekolah.nama') }}</p>
    </div>
</div>
@endsection
