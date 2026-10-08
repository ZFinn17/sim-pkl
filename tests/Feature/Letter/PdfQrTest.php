<?php

namespace Tests\Feature\Letter;

use App\Models\GuruPembimbing;
use App\Models\PengajuanPkl;
use App\Models\Perusahaan;
use App\Models\SuratPengantar;
use App\Models\User;
use App\Services\PdfLetterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class PdfQrTest extends TestCase
{
    use RefreshDatabase;

    public function test_approving_an_application_generates_a_real_pdf_file_and_surat_record(): void
    {
        Storage::fake('local');

        $admin = User::factory()->admin()->create();
        $guru = GuruPembimbing::factory()->create();
        $perusahaan = Perusahaan::factory()->create();
        $pengajuan = PengajuanPkl::factory()->create(['perusahaan_id' => $perusahaan->id]);

        $result = app(PdfLetterService::class)->approveAndGenerate($pengajuan, $guru, $admin);

        $this->assertSame(PengajuanPkl::STATUS_WAITING_COMPANY, $result->status);
        $this->assertSame($guru->id, $result->guru_pembimbing_id);
        $this->assertNotNull($result->suratPengantar);

        $surat = $result->suratPengantar;
        $this->assertNotEmpty($surat->nomor_surat);
        $this->assertNotEmpty($surat->verification_token);
        Storage::disk('local')->assertExists($surat->file_path);

        // File yang tersimpan harus benar-benar PDF (%PDF magic bytes), bukan file kosong/dummy.
        $contents = Storage::disk('local')->get($surat->file_path);
        $this->assertStringStartsWith('%PDF', $contents);
    }

    public function test_letter_numbers_are_unique_and_sequential_within_a_year(): void
    {
        Storage::fake('local');

        $admin = User::factory()->admin()->create();
        $guru = GuruPembimbing::factory()->create();
        $perusahaan = Perusahaan::factory()->create(['kuota_maksimal' => 10]);

        $pengajuan1 = PengajuanPkl::factory()->create(['perusahaan_id' => $perusahaan->id]);
        $pengajuan2 = PengajuanPkl::factory()->create(['perusahaan_id' => $perusahaan->id]);

        $service = app(PdfLetterService::class);
        $hasil1 = $service->approveAndGenerate($pengajuan1, $guru, $admin);
        $hasil2 = $service->approveAndGenerate($pengajuan2, $guru, $admin);

        $this->assertNotSame($hasil1->suratPengantar->nomor_surat, $hasil2->suratPengantar->nomor_surat);
    }

    public function test_qr_verification_page_shows_valid_for_a_real_token(): void
    {
        $surat = SuratPengantar::factory()->create(['verification_token' => Str::random(48)]);

        $response = $this->get(route('verifikasi.surat', $surat->verification_token));

        $response->assertOk();
        $response->assertSee('Terverifikasi');
        $response->assertSee($surat->nomor_surat);
    }

    public function test_qr_verification_page_shows_invalid_for_an_unknown_token(): void
    {
        $response = $this->get(route('verifikasi.surat', 'token-acak-tidak-ada'));

        $response->assertOk();
        $response->assertSee('Tidak Dapat Diverifikasi');
    }

    public function test_verification_page_never_exposes_personal_contact_data(): void
    {
        $surat = SuratPengantar::factory()->create(['verification_token' => Str::random(48)]);
        $pengajuan = $surat->pengajuan;

        $response = $this->get(route('verifikasi.surat', $surat->verification_token));

        $response->assertDontSee($pengajuan->ketua->email);
        $response->assertDontSee($pengajuan->ketua->no_hp);
    }
}
