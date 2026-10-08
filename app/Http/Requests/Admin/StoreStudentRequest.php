<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'nis_nip' => ['required', 'string', 'max:30', 'unique:users,nis_nip'],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'kelas' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ];
    }

    public function attributes(): array
    {
        return [
            'nis_nip' => 'NIS',
            'nama_lengkap' => 'nama lengkap',
            'kelas' => 'kelas',
            'no_hp' => 'no. HP',
        ];
    }

    public function toUserAttributes(): array
    {
        return [
            'nis_nip' => $this->string('nis_nip'),
            'nama_lengkap' => $this->string('nama_lengkap'),
            'kelas' => $this->string('kelas'),
            'role' => User::ROLE_SISWA,
            'no_hp' => $this->string('no_hp'),
            'is_active' => true,
            'email' => $this->string('email'),
            'password' => $this->string('password'),
        ];
    }
}
