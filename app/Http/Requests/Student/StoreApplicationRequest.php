<?php

namespace App\Http\Requests\Student;

use App\Models\PengajuanPkl;
use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSiswa() ?? false;
    }

    public function rules(): array
    {
        return [
            'perusahaan_id' => ['required', 'integer', 'exists:perusahaan,id'],
            'jenis_pengajuan' => ['required', 'in:' . PengajuanPkl::JENIS_INDIVIDUAL . ',' . PengajuanPkl::JENIS_GROUP],
            'tgl_mulai_pkl' => ['required', 'date', 'after_or_equal:today'],
            'tgl_selesai_pkl' => ['required', 'date', 'after:tgl_mulai_pkl'],
            'anggota' => ['array', 'max:' . (PengajuanPkl::MAKSIMAL_ANGGOTA_KELOMPOK - 1)],
            'anggota.*' => ['integer', 'distinct', 'exists:users,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'perusahaan_id' => 'perusahaan',
            'jenis_pengajuan' => 'jenis pengajuan',
            'tgl_mulai_pkl' => 'tanggal mulai PKL',
            'tgl_selesai_pkl' => 'tanggal selesai PKL',
            'anggota' => 'anggota kelompok',
        ];
    }
}
