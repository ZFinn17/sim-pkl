<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class UploadSuratBalasanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSiswa() ?? false;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'file' => 'file surat balasan',
        ];
    }

    public function messages(): array
    {
        return [
            'file.max' => 'File terlalu besar. Maksimal 5 MB.',
            'file.mimes' => 'Format file harus PDF, JPG, atau PNG.',
        ];
    }
}
