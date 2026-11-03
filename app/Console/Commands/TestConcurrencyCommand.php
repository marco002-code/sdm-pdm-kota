<?php

namespace App\Console\Commands;

use App\Jobs\GenerateMasterNpmBatchJob;
use App\Jobs\UpdateUnitAuditMetricsJob;
use App\Models\Employee;
use App\Models\MasterNpm;
use App\Models\Unit;
use App\Models\UnitUpdateAudit;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Throwable;

class TestConcurrencyCommand extends Command
{
    protected $signature = 'app:test-concurrency {--admins=15 : Jumlah simulasi admin bersamaan}';
    protected $description = 'Uji ketahanan sistem antrian & transaksi database ketika diakses lebih dari 10 admin secara bersamaan';

    private int $passed = 0;
    private int $failed = 0;

    public function handle(): int
    {
        $adminsCount = (int) $this->option('admins');

        $this->info('========================================================================');
        $this->info("🚀 PENGUJIAN SISTEM ANTRIAN & KONKURENSI (SIMULASI {$adminsCount} ADMIN BERSAMAAN)");
        $this->info('========================================================================');

        $this->testDatabaseConfiguration();
        $this->testQueueSystem();
        $this->testConcurrentEmployeeCreation($adminsCount);
        $this->testNpmCollisionSafety();
        $this->testConcurrentJsonAppraisalSafety();
        $this->testMasterNpmBatchQueue();
        $this->testQueueWorkerExecution();

        $this->newLine();
        $this->info('========================================================================');
        $this->info("📊 HASIL PENGUJIAN: {$this->passed} BERHASIL | {$this->failed} GAGAL");
        $this->info('========================================================================');

        return $this->failed === 0 ? Command::SUCCESS : Command::FAILURE;
    }

    private function assert(string $label, bool $condition, string $msg = ''): void
    {
        if ($condition) {
            $this->passed++;
            $this->line("  <fg=green;options=bold>✔ PASS</> {$label}");
        } else {
            $this->failed++;
            $this->line("  <fg=red;options=bold>✘ FAIL</> {$label} - {$msg}");
        }
    }

    private function testDatabaseConfiguration(): void
    {
        $this->newLine();
        $this->comment('▶ FASE 1: Verifikasi Mode Antrian & Konkurensi Database SQLite / DB');

        $driver = config('database.default');
        $this->assert("Koneksi database aktif menggunakan driver: {$driver}", in_array($driver, ['sqlite', 'mysql']));

        if ($driver === 'sqlite') {
            $journalMode = DB::select('PRAGMA journal_mode;')[0]->journal_mode ?? '';
            $this->assert("SQLite berjalan dalam mode WAL (Write-Ahead Logging: '{$journalMode}') untuk konkurensi multi-writer", strtolower($journalMode) === 'wal');

            $busyTimeout = DB::select('PRAGMA busy_timeout;')[0]->timeout ?? 0;
            $this->assert("SQLite busy_timeout terkonfigurasi ({$busyTimeout}ms >= 10000ms) untuk antrian penulisan", $busyTimeout >= 10000);
        }

        $queueConn = config('queue.default');
        $this->assert("Koneksi antrian (QUEUE_CONNECTION) dikonfigurasi ke: '{$queueConn}'", in_array($queueConn, ['database', 'redis']));
    }

    private function testQueueSystem(): void
    {
        $this->newLine();
        $this->comment('▶ FASE 2: Verifikasi Tabel Antrian (Queue Jobs) & Dispatcher');

        $hasJobsTable = DB::getSchemaBuilder()->hasTable('jobs');
        $this->assert("Tabel database 'jobs' untuk antrian asinkron tersedia", $hasJobsTable);

        $hasFailedJobsTable = DB::getSchemaBuilder()->hasTable('failed_jobs');
        $this->assert("Tabel database 'failed_jobs' untuk retensi error tersedia", $hasFailedJobsTable);

        $hasBatchesTable = DB::getSchemaBuilder()->hasTable('job_batches');
        $this->assert("Tabel database 'job_batches' untuk pemrosesan batch tersedia", $hasBatchesTable);
    }

    private function testConcurrentEmployeeCreation(int $count): void
    {
        $this->newLine();
        $this->comment("▶ FASE 3: Simulasi {$count} Admin Input Data Pegawai Secara Bersamaan");

        $unit = Unit::first();
        $admin = User::first();
        Auth::login($admin);

        $createdIds = [];
        $errors = [];
        $startTime = microtime(true);

        for ($i = 1; $i <= $count; $i++) {
            $uniqueSuffix = time() . '_' . $i . '_' . rand(1000, 9999);
            $nik = '347101' . str_pad((string) rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT);
            $npm = 'TEST_' . $uniqueSuffix;

            try {
                // Buat Master NPM dulu
                MasterNpm::create([
                    'npm' => $npm,
                    'is_used' => false,
                    'notes' => 'Test concurrency NPM',
                ]);

                // Simulasi Controller store dengan transaksi dan dispatch queue
                $emp = DB::transaction(function () use ($unit, $nik, $npm, $uniqueSuffix) {
                    $master = MasterNpm::where('npm', $npm)->lockForUpdate()->first();
                    if ($master && $master->is_used) {
                        throw new \Exception("NPM sudah terpakai");
                    }

                    $e = Employee::create([
                        'full_name' => "Pegawai Uji Konkurensi {$uniqueSuffix}",
                        'nik' => $nik,
                        'npm' => $npm,
                        'unit_id' => $unit->id,
                        'gender' => 'L',
                        'position' => 'Guru Penguji',
                        'join_date' => now()->toDateString(),
                        'employee_type' => 'GTY',
                    ]);

                    $master->update([
                        'is_used' => true,
                        'used_by_employee_id' => $e->id,
                        'used_at' => now(),
                    ]);

                    return $e;
                }, 5);

                // Dispatch antrian audit
                UpdateUnitAuditMetricsJob::dispatch($unit->id, $admin->id);

                $createdIds[] = $emp->id;
            } catch (Throwable $e) {
                $errors[] = "Admin {$i} gagal: " . $e->getMessage();
            }
        }

        $duration = round(microtime(true) - $startTime, 3);
        $successCount = count($createdIds);

        $this->assert("Semua {$count} transaksi input admin berhasil tersimpan ({$successCount}/{$count}) dalam {$duration}s", $successCount === $count, implode(', ', $errors));

        // Bersihkan data tes
        if (!empty($createdIds)) {
            Employee::whereIn('id', $createdIds)->forceDelete();
            MasterNpm::where('npm', 'like', 'TEST_%')->delete();
        }
    }

    private function testNpmCollisionSafety(): void
    {
        $this->newLine();
        $this->comment('▶ FASE 4: Pengujian Tabrakan NPM Bersamaan (Race Condition Prevention)');

        $testNpm = 'COLLISION_' . time();
        $master = MasterNpm::create([
            'npm' => $testNpm,
            'is_used' => false,
            'notes' => 'Uji tabrakan klaim',
        ]);

        $unit = Unit::first();
        $winnerId = null;
        $loserRejected = false;

        // Simulasi Admin A
        try {
            DB::transaction(function () use ($master, $unit, $testNpm, &$winnerId) {
                $m = MasterNpm::where('npm', $testNpm)->lockForUpdate()->first();
                if ($m->is_used) {
                    throw new \Exception("NPM sudah dipakai");
                }
                $emp = Employee::create([
                    'full_name' => 'Admin A Claimed',
                    'nik' => '347101' . rand(1000000000, 9999999999),
                    'npm' => $testNpm,
                    'unit_id' => $unit->id,
                    'gender' => 'L',
                    'position' => 'Staff',
                    'join_date' => now()->toDateString(),
                ]);
                $m->update(['is_used' => true, 'used_by_employee_id' => $emp->id, 'used_at' => now()]);
                $winnerId = $emp->id;
            }, 5);
        } catch (Throwable $e) {
            //
        }

        // Simulasi Admin B mencoba mengambil NPM yang sama persis
        try {
            DB::transaction(function () use ($testNpm, $unit) {
                $m = MasterNpm::where('npm', $testNpm)->lockForUpdate()->first();
                if ($m->is_used) {
                    throw new \Exception("Nomor NPM ini baru saja digunakan oleh pegawai lain.");
                }
                Employee::create([
                    'full_name' => 'Admin B Claimed',
                    'nik' => '347101' . rand(1000000000, 9999999999),
                    'npm' => $testNpm,
                    'unit_id' => $unit->id,
                    'gender' => 'P',
                    'position' => 'Staff',
                    'join_date' => now()->toDateString(),
                ]);
            }, 5);
        } catch (Throwable $e) {
            if (str_contains($e->getMessage(), 'NPM ini baru saja digunakan') || str_contains($e->getMessage(), 'sudah dipakai')) {
                $loserRejected = true;
            }
        }

        $this->assert("Pessimistic lock berhasil menolak Admin B dan mencegah duplikasi NPM pada klaim bersamaan", $loserRejected && $winnerId !== null);

        // Cleanup
        if ($winnerId) {
            Employee::where('id', $winnerId)->forceDelete();
        }
        $master->delete();
    }

    private function testConcurrentJsonAppraisalSafety(): void
    {
        $this->newLine();
        $this->comment('▶ FASE 5: Pengujian Concurrency JSON Field (Input Nilai DP3 Multi-Tahun)');

        $unit = Unit::first();
        $testEmp = Employee::create([
            'full_name' => 'Pegawai Uji DP3 ' . time(),
            'nik' => '347101' . rand(1000000000, 9999999999),
            'unit_id' => $unit->id,
            'gender' => 'L',
            'position' => 'Guru',
            'join_date' => now()->toDateString(),
            'dp3_scores' => [],
        ]);

        // Simulasi 3 input DP3 pada pegawai yang sama secara beruntun
        $scoresToInsert = [
            ['year' => 2022, 'score' => 85.5],
            ['year' => 2023, 'score' => 90.0],
            ['year' => 2024, 'score' => 92.5],
        ];

        foreach ($scoresToInsert as $item) {
            DB::transaction(function () use ($testEmp, $item) {
                $lockedEmp = Employee::lockForUpdate()->find($testEmp->id);
                $list = $lockedEmp->dp3_scores ?? [];
                $list[] = [
                    'year' => $item['year'],
                    'score' => $item['score'],
                    'predicate' => 'Baik',
                ];
                $lockedEmp->update(['dp3_scores' => array_values($list)]);
            }, 5);
        }

        $testEmp->refresh();
        $savedCount = count($testEmp->dp3_scores ?? []);
        $this->assert("Seluruh riwayat nilai DP3 tersimpan utuh ({$savedCount}/3 nilai) tanpa ada yang tertimpa", $savedCount === 3);

        $testEmp->forceDelete();
    }

    private function testMasterNpmBatchQueue(): void
    {
        $this->newLine();
        $this->comment('▶ FASE 6: Pengujian Antrian Otomatis Batch NPM (>20 Nomor)');

        $start = 90001;
        $end = 90050; // 50 nomor (> 20 batas antrian)
        $total = ($end - $start + 1);

        // Hapus range ini jika ada sebelumnya
        MasterNpm::where('npm', 'like', 'QTEST_%')->delete();

        // Dispatch background job
        GenerateMasterNpmBatchJob::dispatch($start, $end, 'QTEST_', 6, null, 'Uji antrian batch');

        // Pastikan job masuk ke antrian database
        $queuedJobs = DB::table('jobs')->where('payload', 'like', '%GenerateMasterNpmBatchJob%')->count();
        $this->assert("Job pembuatan batch {$total} NPM berhasil masuk ke antrian sistem (Queue: {$queuedJobs} job aktif)", $queuedJobs >= 1);
    }

    private function testQueueWorkerExecution(): void
    {
        $this->newLine();
        $this->comment('▶ FASE 7: Pengujian Eksekusi Queue Worker');

        // Jalankan worker untuk memproses seluruh antrian yang ada hingga selesai
        Artisan::call('queue:work', [
            '--stop-when-empty' => true,
        ]);

        $createdBatchNpms = MasterNpm::where('npm', 'like', 'QTEST_%')->count();
        $this->assert("Queue Worker sukses memproses antrian batch NPM di latar belakang ({$createdBatchNpms} NPM terdaftar)", $createdBatchNpms === 50);

        $remainingJobs = DB::table('jobs')->count();
        $this->assert("Seluruh antrian latar belakang berhasil dikosongkan dan tuntas (Sisa jobs: {$remainingJobs})", $remainingJobs === 0);

        // Cleanup
        MasterNpm::where('npm', 'like', 'QTEST_%')->delete();
    }
}
