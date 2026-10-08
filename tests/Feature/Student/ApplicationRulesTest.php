<?php

namespace Tests\Feature\Student;

use App\Models\PengajuanPkl;
use App\Models\Perusahaan;
use App\Models\User;
use App\Services\ApplicationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ApplicationRulesTest extends TestCase
{
    use RefreshDatabase;

    private ApplicationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ApplicationService::class);
    }

    public function test_siswa_can_submit_individual_application_and_quota_is_untouched(): void
    {
        $siswa = User::factory()->siswa()->create();
        $perusahaan = Perusahaan::factory()->create(['kuota_maksimal' => 5, 'kuota_terisi' => 0]);

        $pengajuan = $this->service->createApplication(
            ketua: $siswa,
            perusahaan: $perusahaan,
            jenisPengajuan: PengajuanPkl::JENIS_INDIVIDUAL,
            tglMulai: now()->addWeek()->toDateString(),
            tglSelesai: now()->addMonths(3)->toDateString(),
        );

        $this->assertSame(PengajuanPkl::STATUS_SUBMITTED, $pengajuan->status);
        $this->assertSame(0, $perusahaan->fresh()->kuota_terisi);
    }

    public function test_siswa_cannot_exceed_maximum_active_applications(): void
    {
        $siswa = User::factory()->siswa()->create();
        $perusahaanList = Perusahaan::factory()->count(3)->create(['kuota_maksimal' => 5]);

        $this->service->createApplication($siswa, $perusahaanList[0], PengajuanPkl::JENIS_INDIVIDUAL, now()->addWeek()->toDateString(), now()->addMonths(3)->toDateString());
        $this->service->createApplication($siswa, $perusahaanList[1], PengajuanPkl::JENIS_INDIVIDUAL, now()->addWeek()->toDateString(), now()->addMonths(3)->toDateString());

        $this->expectException(ValidationException::class);
        $this->service->createApplication($siswa, $perusahaanList[2], PengajuanPkl::JENIS_INDIVIDUAL, now()->addWeek()->toDateString(), now()->addMonths(3)->toDateString());
    }

    public function test_group_application_rejects_more_than_three_members_total(): void
    {
        $ketua = User::factory()->siswa()->create();
        $anggota = User::factory()->siswa()->count(3)->create();
        $perusahaan = Perusahaan::factory()->create(['kuota_maksimal' => 5]);

        $this->expectException(ValidationException::class);
        $this->service->createApplication(
            $ketua,
            $perusahaan,
            PengajuanPkl::JENIS_GROUP,
            now()->addWeek()->toDateString(),
            now()->addMonths(3)->toDateString(),
            $anggota->pluck('id')->all(), // 3 anggota + 1 ketua = 4, melebihi batas
        );
    }

    public function test_group_application_rejects_duplicate_member_ids(): void
    {
        $ketua = User::factory()->siswa()->create();
        $anggota = User::factory()->siswa()->create();
        $perusahaan = Perusahaan::factory()->create(['kuota_maksimal' => 5]);

        $this->expectException(ValidationException::class);
        $this->service->createApplication(
            $ketua,
            $perusahaan,
            PengajuanPkl::JENIS_GROUP,
            now()->addWeek()->toDateString(),
            now()->addMonths(3)->toDateString(),
            [$anggota->id, $anggota->id],
        );
    }

    public function test_cannot_apply_to_company_with_zero_quota(): void
    {
        $siswa = User::factory()->siswa()->create();
        $perusahaan = Perusahaan::factory()->kuotaPenuh()->create(['kuota_maksimal' => 2, 'kuota_terisi' => 2]);

        $this->expectException(ValidationException::class);
        $this->service->createApplication($siswa, $perusahaan, PengajuanPkl::JENIS_INDIVIDUAL, now()->addWeek()->toDateString(), now()->addMonths(3)->toDateString());
    }

    public function test_reapplying_to_a_company_that_rejected_is_blocked_until_admin_reopens(): void
    {
        $siswa = User::factory()->siswa()->create();
        $perusahaan = Perusahaan::factory()->create(['kuota_maksimal' => 5]);

        PengajuanPkl::factory()->create([
            'ketua_siswa_id' => $siswa->id,
            'perusahaan_id' => $perusahaan->id,
            'status' => PengajuanPkl::STATUS_REJECTED,
            'rejected_at' => now(),
            'reopened_at' => null,
        ]);

        $this->expectException(ValidationException::class);
        $this->service->createApplication($siswa, $perusahaan, PengajuanPkl::JENIS_INDIVIDUAL, now()->addWeek()->toDateString(), now()->addMonths(3)->toDateString());
    }

    public function test_reapplying_succeeds_once_admin_has_reopened_it(): void
    {
        $siswa = User::factory()->siswa()->create();
        $perusahaan = Perusahaan::factory()->create(['kuota_maksimal' => 5]);

        PengajuanPkl::factory()->create([
            'ketua_siswa_id' => $siswa->id,
            'perusahaan_id' => $perusahaan->id,
            'status' => PengajuanPkl::STATUS_REJECTED,
            'rejected_at' => now(),
            'reopened_at' => now(),
        ]);

        $pengajuan = $this->service->createApplication($siswa, $perusahaan, PengajuanPkl::JENIS_INDIVIDUAL, now()->addWeek()->toDateString(), now()->addMonths(3)->toDateString());

        $this->assertSame(PengajuanPkl::STATUS_SUBMITTED, $pengajuan->status);
    }
}
