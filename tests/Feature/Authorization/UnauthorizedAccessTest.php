<?php

namespace Tests\Feature\Authorization;

use App\Models\PengajuanPkl;
use App\Models\Perusahaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnauthorizedAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_siswa_cannot_view_another_students_application(): void
    {
        $pemilik = User::factory()->siswa()->create();
        $siswaLain = User::factory()->siswa()->create();
        $perusahaan = Perusahaan::factory()->create();

        $pengajuan = PengajuanPkl::factory()->create([
            'ketua_siswa_id' => $pemilik->id,
            'perusahaan_id' => $perusahaan->id,
        ]);

        $response = $this->actingAs($siswaLain)->get(route('student.pengajuan.show', $pengajuan));

        $response->assertForbidden();
    }

    public function test_siswa_who_is_a_group_member_can_view_the_application(): void
    {
        $ketua = User::factory()->siswa()->create();
        $anggota = User::factory()->siswa()->create();
        $perusahaan = Perusahaan::factory()->create();

        $pengajuan = PengajuanPkl::factory()->create([
            'ketua_siswa_id' => $ketua->id,
            'perusahaan_id' => $perusahaan->id,
            'jenis_pengajuan' => PengajuanPkl::JENIS_GROUP,
        ]);
        $pengajuan->anggotaKelompok()->create(['siswa_id' => $anggota->id]);

        $response = $this->actingAs($anggota)->get(route('student.pengajuan.show', $pengajuan));

        $response->assertOk();
    }

    public function test_company_cannot_view_another_companys_application(): void
    {
        $perusahaanA = Perusahaan::factory()->create();
        $perusahaanB = Perusahaan::factory()->create();
        $userB = User::factory()->perusahaanAccount()->create();
        $perusahaanB->update(['user_id' => $userB->id]);

        $pengajuan = PengajuanPkl::factory()->waitingCompany()->create(['perusahaan_id' => $perusahaanA->id]);

        $response = $this->actingAs($userB)->get(route('company.pengajuan.show', $pengajuan));

        $response->assertForbidden();
    }

    public function test_guest_cannot_access_any_dashboard(): void
    {
        $this->get(route('student.dashboard'))->assertRedirect(route('login'));
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('company.dashboard'))->assertRedirect(route('login'));
    }
}
