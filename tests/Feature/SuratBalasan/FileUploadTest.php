<?php

namespace Tests\Feature\SuratBalasan;

use App\Models\PengajuanPkl;
use App\Models\Perusahaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_siswa_can_upload_a_valid_pdf_reply_letter_for_an_accepted_application(): void
    {
        Storage::fake('local');

        $siswa = User::factory()->siswa()->create();
        $perusahaan = Perusahaan::factory()->create();
        $pengajuan = PengajuanPkl::factory()->accepted()->create([
            'ketua_siswa_id' => $siswa->id,
            'perusahaan_id' => $perusahaan->id,
        ]);

        // Laravel's `mimes` rule sniffs actual file content (not just the filename/
        // declared Content-Type), so the fake file needs real PDF magic bytes.
        $file = UploadedFile::fake()->createWithContent('balasan.pdf', '%PDF-1.4' . str_repeat('x', 1024));

        $response = $this->actingAs($siswa)->post(
            route('student.pengajuan.surat-balasan', $pengajuan),
            ['file' => $file],
        );

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('surat_balasan', [
            'pengajuan_id' => $pengajuan->id,
            'uploaded_by' => $siswa->id,
            'original_filename' => 'balasan.pdf',
        ]);
    }

    public function test_upload_rejects_file_larger_than_5mb(): void
    {
        Storage::fake('local');

        $siswa = User::factory()->siswa()->create();
        $pengajuan = PengajuanPkl::factory()->accepted()->create(['ketua_siswa_id' => $siswa->id]);

        $file = UploadedFile::fake()->create('balasan.pdf', 6000, 'application/pdf'); // 6 MB > batas 5 MB

        $response = $this->actingAs($siswa)->post(
            route('student.pengajuan.surat-balasan', $pengajuan),
            ['file' => $file],
        );

        $response->assertSessionHasErrors('file');
        $this->assertDatabaseMissing('surat_balasan', ['pengajuan_id' => $pengajuan->id]);
    }

    public function test_upload_rejects_disallowed_file_type(): void
    {
        Storage::fake('local');

        $siswa = User::factory()->siswa()->create();
        $pengajuan = PengajuanPkl::factory()->accepted()->create(['ketua_siswa_id' => $siswa->id]);

        $file = UploadedFile::fake()->create('balasan.exe', 100, 'application/x-msdownload');

        $response = $this->actingAs($siswa)->post(
            route('student.pengajuan.surat-balasan', $pengajuan),
            ['file' => $file],
        );

        $response->assertSessionHasErrors('file');
    }

    public function test_upload_rejected_when_application_is_not_yet_accepted(): void
    {
        Storage::fake('local');

        $siswa = User::factory()->siswa()->create();
        $pengajuan = PengajuanPkl::factory()->create([
            'ketua_siswa_id' => $siswa->id,
            'status' => PengajuanPkl::STATUS_SUBMITTED,
        ]);

        // Konten valid supaya gagal karena status pengajuan (yang sedang diuji),
        // bukan karena validasi mimes di FormRequest.
        $file = UploadedFile::fake()->createWithContent('balasan.pdf', '%PDF-1.4' . str_repeat('x', 1024));

        $response = $this->actingAs($siswa)->post(
            route('student.pengajuan.surat-balasan', $pengajuan),
            ['file' => $file],
        );

        $response->assertSessionHasErrors('file');
        $this->assertDatabaseMissing('surat_balasan', ['pengajuan_id' => $pengajuan->id]);
    }

    public function test_siswa_cannot_upload_reply_letter_for_someone_elses_application(): void
    {
        Storage::fake('local');

        $pemilik = User::factory()->siswa()->create();
        $siswaLain = User::factory()->siswa()->create();
        $pengajuan = PengajuanPkl::factory()->accepted()->create(['ketua_siswa_id' => $pemilik->id]);

        $file = UploadedFile::fake()->createWithContent('balasan.pdf', '%PDF-1.4' . str_repeat('x', 1024));

        $response = $this->actingAs($siswaLain)->post(
            route('student.pengajuan.surat-balasan', $pengajuan),
            ['file' => $file],
        );

        $response->assertForbidden();
    }
}
