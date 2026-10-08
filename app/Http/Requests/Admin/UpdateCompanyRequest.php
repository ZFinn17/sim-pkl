<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'nama_perusahaan' => ['required', 'string', 'max:255'],
            'alamat_lengkap' => ['required', 'string'],
            'nama_pic' => ['required', 'string', 'max:255'],
            'kontak_hrd' => ['required', 'string', 'max:30'],
            'email_hrd' => ['required', 'string', 'email', 'max:255'],
            'kuota_maksimal' => ['required', 'integer', 'min:0', 'max:1000'],
            'status_mitra' => ['required', 'in:aktif,nonaktif'],
            'deskripsi' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nama_perusahaan' => 'nama perusahaan',
            'alamat_lengkap' => 'alamat',
            'nama_pic' => 'nama PIC',
            'kontak_hrd' => 'kontak HRD',
            'email_hrd' => 'email HRD',
            'kuota_maksimal' => 'kuota maksimal',
        ];
    }

    /**
     * Pastikan kuota maksimal baru tidak lebih kecil dari kuota yang sudah terisi
     * (mencegah kuota_tersedia menjadi negatif).
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $perusahaan = $this->route('perusahaan');

            if ($perusahaan && $this->integer('kuota_maksimal') < $perusahaan->kuota_terisi) {
                $validator->errors()->add(
                    'kuota_maksimal',
                    "Kuota maksimal tidak boleh kurang dari kuota yang sudah terisi ({$perusahaan->kuota_terisi})."
                );
            }
        });
    }
}
