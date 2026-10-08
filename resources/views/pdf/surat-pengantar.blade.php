<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Surat Pengantar PKL - {{ $nomorSurat }}</title>
    <style>
        @page { margin: 90px 60px 70px 60px; }

        body {
            font-family: "Helvetica", "Arial", sans-serif;
            font-size: 12px;
            color: #111111;
            line-height: 1.5;
        }

        .kop-table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        .kop-table td { vertical-align: middle; }
        .kop-logo { width: 70px; }
        .kop-logo img { width: 64px; height: 64px; }
        .kop-text h1 { font-size: 15px; margin: 0 0 2px 0; text-transform: uppercase; }
        .kop-text p { font-size: 10.5px; margin: 0; color: #333333; }
        .kop-line { border-bottom: 2.5px solid #111111; margin: 6px 0 18px 0; }

        .meta-table { width: 100%; margin-bottom: 16px; font-size: 12px; }
        .meta-table td { padding: 1px 0; vertical-align: top; }
        .meta-label { width: 90px; }

        .judul { text-align: center; font-size: 13px; font-weight: bold; text-decoration: underline; margin: 18px 0 4px 0; }
        .nomor { text-align: center; font-size: 11.5px; margin-bottom: 18px; }

        p.isi { text-align: justify; margin: 0 0 10px 0; }

        table.data-siswa { width: 100%; border-collapse: collapse; margin: 10px 0 14px 0; font-size: 11.5px; }
        table.data-siswa th, table.data-siswa td { border: 1px solid #999999; padding: 5px 8px; text-align: left; }
        table.data-siswa th { background-color: #f0f0f0; }

        .ttd-table { width: 100%; margin-top: 50px; font-size: 12px; }
        .ttd-table td { width: 50%; text-align: center; vertical-align: top; }
        .ttd-space { height: 70px; }
        .ttd-placeholder {
            border: 1px dashed #999999;
            color: #999999;
            font-size: 9.5px;
            padding: 18px 6px;
            margin: 6px 30px 6px 30px;
        }
        .ttd-nama { font-weight: bold; text-decoration: underline; }

        .qr-section { margin-top: 24px; text-align: center; }
        .qr-image { width: 110px; height: 110px; }
        .qr-caption { font-size: 9px; color: #555555; margin-top: 4px; }

        .footer-note { margin-top: 30px; font-size: 9px; color: #777777; text-align: center; }
    </style>
</head>
<body>

    <table class="kop-table">
        <tr>
            @if ($sekolah['logo_path'] && file_exists(public_path($sekolah['logo_path'])))
                <td class="kop-logo">
                    <img src="{{ public_path($sekolah['logo_path']) }}" alt="Logo">
                </td>
            @endif
            <td class="kop-text">
                <h1>{{ $sekolah['nama'] }}</h1>
                <p>{{ $sekolah['alamat'] ?: 'Alamat sekolah belum diisi di pengaturan.' }}</p>
                <p>
                    @if ($sekolah['telepon']) Telp: {{ $sekolah['telepon'] }} @endif
                    @if ($sekolah['email']) &nbsp;|&nbsp; Email: {{ $sekolah['email'] }} @endif
                    @if ($sekolah['website']) &nbsp;|&nbsp; {{ $sekolah['website'] }} @endif
                </p>
            </td>
        </tr>
    </table>
    <div class="kop-line"></div>

    <div class="judul">SURAT PENGANTAR PRAKTIK KERJA LAPANGAN (PKL)</div>
    <div class="nomor">Nomor: {{ $nomorSurat }}</div>

    <p class="isi">
        Yang bertanda tangan di bawah ini, pihak {{ $sekolah['nama'] }}, dengan hormat mengantarkan
        siswa/siswi kami untuk melaksanakan Praktik Kerja Lapangan (PKL) di perusahaan Bapak/Ibu, dengan data sebagai berikut:
    </p>

    <table class="data-siswa">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th>Nama</th>
                <th style="width: 110px;">NIS</th>
                <th style="width: 90px;">Kelas</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>{{ $pengajuan->ketua->nama_lengkap }} (Ketua)</td>
                <td>{{ $pengajuan->ketua->nis_nip }}</td>
                <td>{{ $pengajuan->ketua->kelas }}</td>
            </tr>
            @foreach ($pengajuan->anggotaKelompok as $i => $anggota)
                <tr>
                    <td>{{ $i + 2 }}</td>
                    <td>{{ $anggota->siswa->nama_lengkap }}</td>
                    <td>{{ $anggota->siswa->nis_nip }}</td>
                    <td>{{ $anggota->siswa->kelas }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="meta-table">
        <tr>
            <td class="meta-label">Tujuan Perusahaan</td>
            <td>: {{ $pengajuan->perusahaan->nama_perusahaan }}</td>
        </tr>
        <tr>
            <td class="meta-label">Alamat</td>
            <td>: {{ $pengajuan->perusahaan->alamat_lengkap }}</td>
        </tr>
        <tr>
            <td class="meta-label">Periode PKL</td>
            <td>:
                {{ $pengajuan->tgl_mulai_pkl->translatedFormat('d F Y') }}
                s.d.
                {{ $pengajuan->tgl_selesai_pkl->translatedFormat('d F Y') }}
            </td>
        </tr>
        <tr>
            <td class="meta-label">Guru Pembimbing</td>
            <td>: {{ $pengajuan->guruPembimbing->nama_lengkap }} (NIP. {{ $pengajuan->guruPembimbing->nip }})</td>
        </tr>
    </table>

    <p class="isi">
        Demikian surat pengantar ini kami sampaikan. Atas perhatian dan kerja sama Bapak/Ibu, kami ucapkan terima kasih.
    </p>

    <table class="ttd-table">
        <tr>
            <td>&nbsp;</td>
            <td>
                {{ $sekolah['nama'] }}, {{ \Illuminate\Support\Carbon::parse($tanggalSurat)->translatedFormat('d F Y') }}<br>
                Kepala Sekolah
                <div class="ttd-placeholder">
                    Area tanda tangan &amp; stempel resmi<br>
                    (ditempel/ditandatangani secara fisik atau via asset resmi yang diunggah Admin)
                </div>
                <div class="ttd-nama">{{ $sekolah['kepala_sekolah'] ?: '(Nama Kepala Sekolah belum diisi)' }}</div>
                @if ($sekolah['nip_kepala_sekolah'])
                    <div>NIP. {{ $sekolah['nip_kepala_sekolah'] }}</div>
                @endif
            </td>
        </tr>
    </table>

    <div class="qr-section">
        <img class="qr-image" src="data:image/svg+xml;base64,{{ base64_encode($qrSvg) }}" alt="QR Code Verifikasi Surat">
        <div class="qr-caption">Scan untuk verifikasi dokumen</div>
    </div>

    <div class="footer-note">
        Dokumen ini diterbitkan otomatis oleh SIM PKL — {{ $sekolah['nama'] }}. Keaslian dapat diverifikasi melalui QR code di atas.
    </div>

</body>
</html>
