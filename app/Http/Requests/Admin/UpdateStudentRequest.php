<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $studentId = $this->route('siswa');

        return [
            'nis_nip' => ['required', 'string', 'max:30', Rule::unique('users', 'nis_nip')->ignore($studentId)],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'kelas' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($studentId)],
            'no_hp' => ['nullable', 'string', 'max:20'],
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
}
