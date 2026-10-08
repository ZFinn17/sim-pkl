<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user && ($user->isAdmin() || $user->isPerusahaan());
    }

    public function rules(): array
    {
        return [
            'alasan_penolakan' => ['required', 'string', 'min:10', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'alasan_penolakan' => 'alasan penolakan',
        ];
    }
}
