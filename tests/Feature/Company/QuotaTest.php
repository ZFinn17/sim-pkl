<?php

namespace Tests\Feature\Company;

use App\Models\PengajuanPkl;
use App\Models\Perusahaan;
use App\Models\User;
use App\Services\QuotaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class QuotaTest extends TestCase
{
    use RefreshDatabase;

    private QuotaService $quotaService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->quotaService = app(QuotaService::class);
    }

    public function test_quota_decreases_when_company_accepts_application(): void
    {
        $perusahaan = Perusahaan::factory()->create(['kuota_maksimal' => 5, 'kuota_terisi' => 0]);
        $perusahaanUser = User::factory()->perusahaanAccount()->create();
        $perusahaan->update(['user_id' => $perusahaanUser->id]);

        $pengajuan = PengajuanPkl::factory()->waitingCompany()->create(['perusahaan_id' => $perusahaan->id]);

        $this->quotaService->acceptApplication($pengajuan, $perusahaanUser);

        $this->assertSame(1, $perusahaan->fresh()->kuota_terisi);
        $this->assertSame(PengajuanPkl::STATUS_ACCEPTED, $pengajuan->fresh()->status);
    }

    public function test_company_cannot_accept_when_quota_is_insufficient_for_group_size(): void
    {
        $perusahaan = Perusahaan::factory()->create(['kuota_maksimal' => 2, 'kuota_terisi' => 2]);
        $perusahaanUser = User::factory()->perusahaanAccount()->create();

        $pengajuan = PengajuanPkl::factory()->waitingCompany()->create(['perusahaan_id' => $perusahaan->id]);

        $this->expectException(ValidationException::class);
        $this->quotaService->acceptApplication($pengajuan, $perusahaanUser);
    }

    public function test_rejecting_application_does_not_change_quota(): void
    {
        $perusahaan = Perusahaan::factory()->create(['kuota_maksimal' => 5, 'kuota_terisi' => 1]);
        $perusahaanUser = User::factory()->perusahaanAccount()->create();

        $pengajuan = PengajuanPkl::factory()->waitingCompany()->create(['perusahaan_id' => $perusahaan->id]);

        $this->quotaService->rejectApplication($pengajuan, $perusahaanUser, 'Kuota periode ini sudah terisi siswa lain.');

        $this->assertSame(1, $perusahaan->fresh()->kuota_terisi);
        $this->assertSame(PengajuanPkl::STATUS_REJECTED, $pengajuan->fresh()->status);
    }

    public function test_reject_endpoint_requires_a_reason(): void
    {
        $perusahaan = Perusahaan::factory()->create(['kuota_maksimal' => 5]);
        $perusahaanUser = User::factory()->perusahaanAccount()->create();
        $perusahaan->update(['user_id' => $perusahaanUser->id]);

        $pengajuan = PengajuanPkl::factory()->waitingCompany()->create(['perusahaan_id' => $perusahaan->id]);

        $response = $this->actingAs($perusahaanUser)
            ->post(route('company.pengajuan.reject', $pengajuan), ['alasan_penolakan' => '']);

        $response->assertSessionHasErrors('alasan_penolakan');
        $this->assertSame(PengajuanPkl::STATUS_WAITING_COMPANY, $pengajuan->fresh()->status);
    }

    public function test_accepting_group_application_decreases_quota_by_full_member_count(): void
    {
        $perusahaan = Perusahaan::factory()->create(['kuota_maksimal' => 5, 'kuota_terisi' => 0]);
        $perusahaanUser = User::factory()->perusahaanAccount()->create();

        $pengajuan = PengajuanPkl::factory()->waitingCompany()->create([
            'perusahaan_id' => $perusahaan->id,
            'jenis_pengajuan' => PengajuanPkl::JENIS_GROUP,
        ]);

        $anggota = User::factory()->siswa()->count(2)->create();
        foreach ($anggota as $siswa) {
            $pengajuan->anggotaKelompok()->create(['siswa_id' => $siswa->id]);
        }

        $this->quotaService->acceptApplication($pengajuan->fresh(), $perusahaanUser);

        // 1 ketua + 2 anggota = 3 peserta
        $this->assertSame(3, $perusahaan->fresh()->kuota_terisi);
    }

    public function test_admin_cancelling_an_accepted_application_restores_the_quota(): void
    {
        $admin = User::factory()->admin()->create();
        $perusahaan = Perusahaan::factory()->create(['kuota_maksimal' => 5, 'kuota_terisi' => 2]);
        $pengajuan = PengajuanPkl::factory()->accepted()->create(['perusahaan_id' => $perusahaan->id]);

        $this->quotaService->cancelAcceptedByAdmin($pengajuan, $admin);

        $this->assertSame(1, $perusahaan->fresh()->kuota_terisi);
        $this->assertSame(PengajuanPkl::STATUS_CANCELLED, $pengajuan->fresh()->status);
    }

    public function test_student_cannot_cancel_an_already_accepted_application_themselves(): void
    {
        $siswa = User::factory()->siswa()->create();
        $pengajuan = PengajuanPkl::factory()->accepted()->create(['ketua_siswa_id' => $siswa->id]);

        $response = $this->actingAs($siswa)->post(route('student.pengajuan.cancel', $pengajuan));

        $response->assertSessionHasErrors('pengajuan');
        $this->assertSame(PengajuanPkl::STATUS_ACCEPTED, $pengajuan->fresh()->status);
    }
}
