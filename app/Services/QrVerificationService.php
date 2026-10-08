<?php

namespace App\Services;

use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrVerificationService
{
    /**
     * Token verifikasi acak & aman (bukan ID yang mudah ditebak).
     */
    public function generateToken(): string
    {
        return (string) Str::random(48);
    }

    public function verificationUrl(string $token): string
    {
        return route('verifikasi.surat', ['token' => $token]);
    }

    /**
     * QR dibuat lokal (bacon/bacon-qr-code), tanpa API eksternal.
     * Format SVG dipilih karena tidak butuh ekstensi Imagick.
     */
    public function generateSvg(string $url): string
    {
        return QrCode::format('svg')
            ->size(140)
            ->margin(1)
            ->errorCorrection('M')
            ->generate($url);
    }
}
