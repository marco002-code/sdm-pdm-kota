<?php

namespace App\Jobs;

use App\Models\MasterNpm;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateMasterNpmBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300;

    protected int $start;
    protected int $end;
    protected string $prefix;
    protected int $pad;
    protected ?int $allocatedUnitId;
    protected ?string $notes;

    /**
     * Create a new job instance.
     */
    public function __construct(
        int $start,
        int $end,
        string $prefix = '',
        int $pad = 5,
        ?int $allocatedUnitId = null,
        ?string $notes = null
    ) {
        $this->start = $start;
        $this->end = $end;
        $this->prefix = $prefix;
        $this->pad = $pad;
        $this->allocatedUnitId = $allocatedUnitId;
        $this->notes = $notes ?: 'Generated via Queue Batch Job';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Memulai antrian GenerateMasterNpmBatchJob: {$this->start} s.d. {$this->end}");

        $chunkSize = 250;
        $batch = [];
        $totalInserted = 0;
        $now = now();

        for ($i = $this->start; $i <= $this->end; $i++) {
            $npmFormatted = $this->prefix . str_pad((string) $i, $this->pad, '0', STR_PAD_LEFT);

            $batch[] = [
                'npm' => $npmFormatted,
                'is_used' => false,
                'allocated_unit_id' => $this->allocatedUnitId,
                'notes' => $this->notes,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($batch) >= $chunkSize) {
                $totalInserted += $this->insertChunk($batch);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            $totalInserted += $this->insertChunk($batch);
        }

        Log::info("Selesai GenerateMasterNpmBatchJob: Berhasil menambahkan {$totalInserted} NPM.");
    }

    /**
     * Insert chunk with concurrency retry safety.
     */
    protected function insertChunk(array $rows): int
    {
        return DB::transaction(function () use ($rows) {
            $existingNpms = MasterNpm::whereIn('npm', array_column($rows, 'npm'))
                ->pluck('npm')
                ->all();

            $existingSet = array_flip($existingNpms);
            $toInsert = array_filter($rows, fn($r) => !isset($existingSet[$r['npm']]));

            if (!empty($toInsert)) {
                MasterNpm::insert($toInsert);
                return count($toInsert);
            }

            return 0;
        }, 5);
    }
}

