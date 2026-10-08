# SIM PKL

Sistem informasi untuk mengelola pengajuan, validasi, dan administrasi Praktik Kerja Lapangan
(PKL) siswa SMK ICB Cinta Niaga — mulai dari pemilihan perusahaan mitra, pengajuan individu/kelompok,
validasi oleh Hubin, penerbitan surat pengantar, hingga keputusan penerimaan oleh perusahaan.

## Fitur

- Autentikasi dan tiga role pengguna: Siswa, Admin (Hubin), dan Perusahaan
- Katalog perusahaan mitra dengan info kuota real-time
- Pengajuan PKL individu maupun kelompok (maksimal 3 siswa)
- Validasi pengajuan oleh Admin, termasuk penentuan guru pembimbing
- Surat pengantar PKL otomatis dalam format PDF, dilengkapi QR code verifikasi
- Verifikasi keaslian surat secara publik melalui QR code
- Keputusan penerimaan/penolakan oleh perusahaan dengan manajemen kuota otomatis
- Upload dan pengelolaan surat balasan dari perusahaan
- Notifikasi dalam aplikasi dan log audit untuk aktivitas penting
- Manajemen data siswa, perusahaan, dan guru pembimbing oleh Admin
- Pengaturan identitas sekolah dan format penomoran surat

## Teknologi

- Laravel 13, PHP 8.3+
- MySQL
- Tailwind CSS 4
- JavaScript (vanilla)
- DomPDF untuk pembuatan dokumen PDF
- QR Code untuk verifikasi dokumen

## Requirements

- PHP >= 8.3
- Composer >= 2.10
- MySQL >= 8.0
- Node.js >= 22, npm >= 10

## Installation

```bash
composer install
npm install
```

## Configuration

Salin file environment dan buat application key:

```bash
cp .env.example .env
php artisan key:generate
```

Sesuaikan koneksi database dan identitas sekolah di `.env`:

```
DB_DATABASE=sim_pkl
DB_USERNAME=root
DB_PASSWORD=

SEKOLAH_NAMA="SMK ICB CINTA NIAGA"
SEKOLAH_ALAMAT=
SEKOLAH_TELEPON=
SEKOLAH_EMAIL=
```

Identitas sekolah dan format nomor surat juga dapat diatur langsung dari aplikasi melalui
menu **Admin > Pengaturan** setelah login.

Jika memiliki logo resmi sekolah, letakkan di `public/images/logo-sekolah.png` agar tampil pada
kop surat PDF.

## Database

```bash
php artisan migrate --seed
php artisan storage:link
```

Seeder akan membuat data awal (akun demo serta beberapa contoh pengajuan) agar aplikasi dapat
langsung dicoba.

## Development

```bash
npm run dev
php artisan serve
```

## Testing

```bash
php artisan test
```

## Build

```bash
npm run build
```

## Demo Account

| Role       | Email               | Password |
|------------|---------------------|----------|
| Admin      | admin@simpkl.test   | password |
| Siswa      | siswa1@simpkl.test  | password |
| Perusahaan | perusahaan1@simpkl.test | password |

