<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_siswa_can_login_and_is_redirected_to_student_dashboard(): void
    {
        $siswa = User::factory()->siswa()->create(['password' => bcrypt('password')]);

        $response = $this->post('/login', [
            'email' => $siswa->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('student.dashboard'));
        $this->assertAuthenticatedAs($siswa);
    }

    public function test_admin_can_login_and_is_redirected_to_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create(['password' => bcrypt('password')]);

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_perusahaan_can_login_and_is_redirected_to_company_dashboard(): void
    {
        $akun = User::factory()->perusahaanAccount()->create(['password' => bcrypt('password')]);

        $response = $this->post('/login', [
            'email' => $akun->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('company.dashboard'));
        $this->assertAuthenticatedAs($akun);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $siswa = User::factory()->siswa()->create(['password' => bcrypt('password')]);

        $response = $this->post('/login', [
            'email' => $siswa->email,
            'password' => 'salah',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_inactive_account_cannot_login(): void
    {
        $siswa = User::factory()->siswa()->inactive()->create(['password' => bcrypt('password')]);

        $response = $this->post('/login', [
            'email' => $siswa->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_siswa_cannot_access_admin_routes(): void
    {
        $siswa = User::factory()->siswa()->create();

        $response = $this->actingAs($siswa)->get('/admin/dashboard');

        $response->assertForbidden();
    }
}
