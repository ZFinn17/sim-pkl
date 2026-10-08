<?php

namespace App\Services;

use App\Models\LetterSequence;
use Illuminate\Support\Facades\DB;

class LetterNumberService
{
    /**
     * Hasilkan nomor surat berikutnya untuk tahun berjalan.
     * Menggunakan row lock pada letter_sequences agar aman apabila dua Admin
     * melakukan approval secara bersamaan (tidak boleh dapat nomor sama).
     */
    public function next(): string
    {
        return DB::transaction(function () {
            $year = now()->year;

            /** @var LetterSequence $sequence */
            $sequence = LetterSequence::query()
                ->where('year', $year)
                ->lockForUpdate()
                ->first();

            if (! $sequence) {
                $sequence = LetterSequence::create(['year' => $year, 'last_number' => 0]);
                // Kunci ulang baris yang baru dibuat agar tetap aman dari race condition.
                $sequence = LetterSequence::query()->where('year', $year)->lockForUpdate()->first();
            }

            $sequence->increment('last_number');

            return $this->format($sequence->last_number, $year);
        });
    }

    private function format(int $number, int $year): string
    {
        $format = config('simpkl.surat.format');
        $code = config('simpkl.surat.kode');
        $sequence = str_pad((string) $number, 3, '0', STR_PAD_LEFT);
        $month = now()->format('m');

        return str_replace(
            ['{sequence}', '{code}', '{month}', '{year}'],
            [$sequence, $code, $month, (string) $year],
            $format,
        );
    }
}
