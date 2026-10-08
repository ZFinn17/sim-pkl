<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Identitas Sekolah
    |--------------------------------------------------------------------------
    | Dipakai untuk KOP surat pengantar PKL. Nilai default di sini bisa
    | di-override lewat Admin > Pengaturan (lihat tabel settings).
    */
    'sekolah' => [
        'nama' => env('SEKOLAH_NAMA', 'SMK ICB CINTA NIAGA'),
        'alamat' => env('SEKOLAH_ALAMAT', ''),
        'telepon' => env('SEKOLAH_TELEPON', ''),
        'email' => env('SEKOLAH_EMAIL', ''),
        'website' => env('SEKOLAH_WEBSITE', ''),
        'kepala_sekolah' => env('SEKOLAH_KEPALA_SEKOLAH', ''),
        'nip_kepala_sekolah' => env('SEKOLAH_NIP_KEPALA_SEKOLAH', ''),
        'logo_path' => env('SEKOLAH_LOGO_PATH', 'images/logo-sekolah.png'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Format Nomor Surat
    |--------------------------------------------------------------------------
    | Placeholder yang didukung: {sequence} {code} {month} {year}
    */
    'surat' => [
        'kode' => env('SURAT_KODE', 'SIM-PKL'),
        'format' => env('SURAT_FORMAT', '{sequence}/{code}/{month}/{year}'),
    ],

];
