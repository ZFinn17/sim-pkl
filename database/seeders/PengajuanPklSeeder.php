<?php

namespace Database\Seeders;

use App\Models\AnggotaKelompok;
use App\Models\GuruPembimbing;
use App\Models\PengajuanPkl;
use App\Models\Perusahaan;
use App\Models\User;
use Illuminate\Database\Seeder;

class PengajuanPklSeeder extends Seeder
{
    /**
     * Data demo lintas status, supaya alur admin dan perusahaan bisa
     * langsung diuji. Guru pembimbing pada pengajuan waiting_company/accepted
     * di-set manual di sini; pada alur normal itu ditentukan lewat proses Approve.
     */
    public function run(): void
    {
        $admin = User::role(User::ROLE_ADMIN)->first();

        $siswa1 = User::where('email', 'siswa1@simpkl.test')->first();
        $siswa2 = User::where('email', 'siswa2@simpkl.test')->first();
        $siswa3 = User::where('email', 'siswa3@simpkl.test')->first();
        $siswa4 = User::where('email', 'siswa4@simpkl.test')->first();
        $siswa5 = User::where('email', 'siswa5@simpkl.test')->first();

        $perusahaan = Perusahaan::orderBy('id')->get();
        $perusahaan1 = $perusahaan->get(0);
        $perusahaan2 = $perusahaan->get(1);
        $perusahaan3 = $perusahaan->get(2);

        $guru = GuruPembimbing::orderBy('id')->first();

        if (! $admin || ! $siswa1 || ! $perusahaan1 || ! $guru) {
            // Data prasyarat belum lengkap (mis. seeder lain belum jalan) - lewati saja.
            return;
        }

        // Menunggu validasi Admin.
        PengajuanPkl::create([
            'kode_pengajuan' => 'PKL-' . now()->year . '-0001',
            'jenis_pengajuan' => PengajuanPkl::JENIS_INDIVIDUAL,
            'ketua_siswa_id' => $siswa1->id,
            'perusahaan_id' => $perusahaan1->id,
            'tgl_pengajuan' => now()->subDays(3)->toDateString(),
            'tgl_mulai_pkl' => now()->addWeeks(2)->toDateString(),
            'tgl_selesai_pkl' => now()->addMonths(3)->toDateString(),
            'status' => PengajuanPkl::STATUS_SUBMITTED,
        ]);

        // Sudah divalidasi, menunggu penentuan guru & surat.
        PengajuanPkl::create([
            'kode_pengajuan' => 'PKL-' . now()->year . '-0002',
            'jenis_pengajuan' => PengajuanPkl::JENIS_INDIVIDUAL,
            'ketua_siswa_id' => $siswa2->id,
            'perusahaan_id' => $perusahaan2->id,
            'tgl_pengajuan' => now()->subDays(5)->toDateString(),
            'tgl_mulai_pkl' => now()->addWeeks(2)->toDateString(),
            'tgl_selesai_pkl' => now()->addMonths(3)->toDateString(),
            'status' => PengajuanPkl::STATUS_HUBIN_APPROVED,
            'approved_by' => $admin->id,
            'approved_at' => now()->subDays(4),
        ]);

        // Kelompok 2 siswa, sudah ada guru, siap diuji accept/reject perusahaan.
        $pengajuanGroup = PengajuanPkl::create([
            'kode_pengajuan' => 'PKL-' . now()->year . '-0003',
            'jenis_pengajuan' => PengajuanPkl::JENIS_GROUP,
            'ketua_siswa_id' => $siswa3->id,
            'perusahaan_id' => $perusahaan1->id,
            'guru_pembimbing_id' => $guru->id,
            'tgl_pengajuan' => now()->subDays(7)->toDateString(),
            'tgl_mulai_pkl' => now()->addWeeks(3)->toDateString(),
            'tgl_selesai_pkl' => now()->addMonths(3)->toDateString(),
            'status' => PengajuanPkl::STATUS_WAITING_COMPANY,
            'approved_by' => $admin->id,
            'approved_at' => now()->subDays(6),
        ]);

        if ($siswa4) {
            AnggotaKelompok::create([
                'pengajuan_id' => $pengajuanGroup->id,
                'siswa_id' => $siswa4->id,
            ]);
        }

        // kuota_terisi perusahaan3 disesuaikan agar tetap konsisten.
        if ($siswa5 && $perusahaan3) {
            PengajuanPkl::create([
                'kode_pengajuan' => 'PKL-' . now()->year . '-0004',
                'jenis_pengajuan' => PengajuanPkl::JENIS_INDIVIDUAL,
                'ketua_siswa_id' => $siswa5->id,
                'perusahaan_id' => $perusahaan3->id,
                'guru_pembimbing_id' => $guru->id,
                'tgl_pengajuan' => now()->subDays(20)->toDateString(),
                'tgl_mulai_pkl' => now()->subDays(5)->toDateString(),
                'tgl_selesai_pkl' => now()->addMonths(2)->toDateString(),
                'status' => PengajuanPkl::STATUS_ACCEPTED,
                'approved_by' => $admin->id,
                'approved_at' => now()->subDays(19),
                'accepted_at' => now()->subDays(15),
            ]);

            $perusahaan3->increment('kuota_terisi', 1);
        }

        // Riwayat: siswa1 pernah ditolak perusahaan3 sebelumnya.
        if ($perusahaan3) {
            PengajuanPkl::create([
                'kode_pengajuan' => 'PKL-' . now()->year . '-0005',
                'jenis_pengajuan' => PengajuanPkl::JENIS_INDIVIDUAL,
                'ketua_siswa_id' => $siswa1->id,
                'perusahaan_id' => $perusahaan3->id,
                'tgl_pengajuan' => now()->subDays(25)->toDateString(),
                'tgl_mulai_pkl' => now()->subDays(10)->toDateString(),
                'tgl_selesai_pkl' => now()->addMonths(2)->toDateString(),
                'status' => PengajuanPkl::STATUS_REJECTED,
                'alasan_penolakan' => 'Kuota untuk periode yang diajukan sudah terisi oleh siswa lain.',
                'rejected_at' => now()->subDays(23),
            ]);
        }
    }
}
