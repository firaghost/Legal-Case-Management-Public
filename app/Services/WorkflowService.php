<?php

namespace App\Services;

use App\Models\CaseFile;
use App\Models\Appeal;
use App\Models\ExecutionFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WorkflowService
{
    /**
     * Advance the case status based on action type.
     */
    public function advanceStatus(CaseFile $case, string $action): void
    {
        $map = [
            'direct_suit' => 'In Court',
            'appeal' => 'On Appeal',
            'second_appeal' => 'On Appeal',
            'cassation' => 'On Cassation',
            'execution_opened' => 'Executed',
            'closed' => 'Closed',
        ];

        if (isset($map[$action])) {
            $case->update(['status' => $map[$action]]);
        }
    }

    /**
     * Create appeal record and update status.
     */
    public function createAppeal(CaseFile $case, array $data): Appeal
    {
        return DB::transaction(function () use ($case, $data) {
            $appeal = Appeal::create(array_merge($data, [
                'case_file_id' => $case->id,
            ]));

            $this->advanceStatus($case, strtolower($data['level']));
            return $appeal;
        });
    }

    /**
     * Open execution file only if result is in favor.
     */
    public function openExecution(CaseFile $case, int $userId): ExecutionFile
    {
        if ($case->result !== 'Won') {
            throw new ModelNotFoundException('Execution not permitted unless decision is in favor.');
        }

        return DB::transaction(function () use ($case, $userId) {
            $exec = ExecutionFile::create([
                'case_file_id' => $case->id,
                'opened_at' => Carbon::now(),
                'created_by' => $userId,
            ]);
            $this->advanceStatus($case, 'execution_opened');
            return $exec;
        });
    }

    /**
     * Close case after supervisor approval.
     */
    public function closeCase(CaseFile $case, int $supervisorId): CaseFile
    {
        return DB::transaction(function () use ($case, $supervisorId) {
            $case->update([
                'status' => 'Closed',
                'closed_at' => Carbon::now(),
                'closed_by' => $supervisorId,
            ]);
            return $case;
        });
    }
}






