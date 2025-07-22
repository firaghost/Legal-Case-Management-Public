<?php

namespace App\Services;

use App\Models\CaseFile;
use Illuminate\Support\Facades\DB;

class FileNumberService
{
    /**
     * Generate the next sequential file number for a given two-digit case-type code.
     *
     * Example: code 01 -> 01-0001, 01-0002, ...
     */
    public function generate(string $code): string
    {
        // ensure two-digit code padding
        $code = str_pad($code, 2, '0', STR_PAD_LEFT);

        return DB::transaction(function () use ($code) {
            // Lock rows with the same prefix to prevent race conditions
            $latest = CaseFile::where('file_number', 'like', "$code-%")
                ->lockForUpdate()
                ->orderByDesc('id')
                ->first();

            $next = 1;
            if ($latest) {
                // Extract the numeric part after the dash
                [$prefix, $number] = explode('-', $latest->file_number);
                $next = ((int) $number) + 1;
            }

            // zero-pad to 4 digits
            $numberPart = str_pad((string) $next, 4, '0', STR_PAD_LEFT);

            return "$code-$numberPart";
        });
    }
}






