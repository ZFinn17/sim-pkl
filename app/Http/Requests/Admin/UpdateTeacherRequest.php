<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $guruId = $this->route('guru');

        return [
            'nip' => ['required', 'string', 'max:30', Rule::unique('guru_pembimbing', 'nip')->ignore($guruId)],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nip' => 'NIP',
            'nama_lengkap' => 'nama lengkap',
            'no_hp' => 'no. HP',
        ];
    }
}
