<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSettingController extends Controller
{
    /**
     * Pasangan [nama field form (flat) => setting key (dotted, dipakai Setting & config)].
     */
    private const FIELD_MAP = [
        'sekolah_nama' => 'sekolah.nama',
        'sekolah_alamat' => 'sekolah.alamat',
        'sekolah_telepon' => 'sekolah.telepon',
        'sekolah_email' => 'sekolah.email',
        'sekolah_website' => 'sekolah.website',
        'sekolah_kepala_sekolah' => 'sekolah.kepala_sekolah',
        'sekolah_nip_kepala_sekolah' => 'sekolah.nip_kepala_sekolah',
        'surat_kode' => 'surat.kode',
        'surat_format' => 'surat.format',
    ];

    public function edit(): View
    {
        $settings = [];
        foreach (self::FIELD_MAP as $field => $settingKey) {
            [$group, $item] = explode('.', $settingKey);
            $settings[$field] = Setting::get($settingKey, config("simpkl.{$group}.{$item}"));
        }

        return view('admin.pengaturan.edit', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sekolah_nama' => ['required', 'string', 'max:255'],
            'sekolah_alamat' => ['nullable', 'string', 'max:500'],
            'sekolah_telepon' => ['nullable', 'string', 'max:50'],
            'sekolah_email' => ['nullable', 'email', 'max:255'],
            'sekolah_website' => ['nullable', 'string', 'max:255'],
            'sekolah_kepala_sekolah' => ['nullable', 'string', 'max:255'],
            'sekolah_nip_kepala_sekolah' => ['nullable', 'string', 'max:50'],
            'surat_kode' => ['required', 'string', 'max:50'],
            'surat_format' => ['required', 'string', 'max:255'],
        ]);

        foreach (self::FIELD_MAP as $field => $settingKey) {
            Setting::set($settingKey, $validated[$field] ?? null);
        }

        AuditLog::record(
            userId: $request->user()->id,
            action: 'update_settings',
            module: 'pengaturan',
            description: "Admin {$request->user()->nama_lengkap} memperbarui pengaturan identitas sekolah / format nomor surat.",
        );

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
