<?php

namespace App\Observers;

use App\Models\Appeal;
use Illuminate\Support\Carbon;

class AppealObserver
{
    public function saved(Appeal $appeal): void
    {
        // if decision date just set, and next level not present, create next level appeal record automatically
        if ($appeal->wasChanged('decided_at') && $appeal->decided_at) {
            $levels = ['appeal', 'cassation', 'execution'];
            $currentIndex = array_search($appeal->level, $levels, true);
            if ($currentIndex !== false && $currentIndex < count($levels) - 1) {
                $nextLevel = $levels[$currentIndex + 1];
                // avoid duplicates
                if (!$appeal->caseFile->appeals()->where('level', $nextLevel)->exists()) {
                    $appeal->caseFile->appeals()->create([
                        'level' => $nextLevel,
                        'file_number' => $appeal->caseFile->file_number . '-' . strtoupper(substr($nextLevel, 0, 3)),
                    ]);
                }
            }
        }
    }
}






