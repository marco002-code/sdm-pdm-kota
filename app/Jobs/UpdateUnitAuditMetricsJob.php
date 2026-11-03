<?php

namespace App\Jobs;

use App\Models\Employee;
use App\Models\UnitUpdateAudit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateUnitAuditMetricsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    protected int $unitId;
    protected ?int $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $unitId, ?int $userId = null)
    {
        $this->unitId = $unitId;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::transaction(function () {
            $currentQuarter = UnitUpdateAudit::getCurrentQuarter();
            $year = (int) date('Y');

            $totalActive = Employee::where('unit_id', $this->unitId)
                ->where('status', 'aktif')
                ->count();

            UnitUpdateAudit::updateOrCreate(
                [
                    'unit_id' => $this->unitId,
                    'year'    => $year,
                    'quarter' => $currentQuarter,
                ],
                [
                    'status'          => 'sudah_update',
                    'last_updated_at' => now(),
                    'updated_by'      => $this->userId,
                    'total_employees' => $totalActive,
                ]
            );
        }, 5);

        Log::info("UpdateUnitAuditMetricsJob selesai untuk unit ID {$this->unitId}");
    }
}

