<?php

namespace App\Console\Commands;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class PreflightCheckCommand extends Command
{
    protected $signature = 'app:preflight';
    protected $description = 'Pemeriksaan pra-deploy (pre-flight check) untuk memastikan kesiapan server versi beta / production';

    private int $errors = 0;
    private int $warnings = 0;

    public function handle(): int
    {
        $this->info('========================================================================');
        $this->info('✈️  PRE-FLIGHT CHECK: PEMERIKSAAN KESIAPAN DEPLOY VERSI BETA / PRODUCTION');
        $this->info('========================================================================');

        $this->checkPhpVersionAndExtensions();
        $this->checkEnvironmentConfig();
        $this->checkDirectoryPermissions();
        $this->checkDatabaseAndMigrations();
        $this->checkQueueAndBackgroundWorker();
        $this->checkStorageSymlinkAndAssets();

        $this->newLine();
        $this->info('========================================================================');
        if ($this->errors === 0 && $this->warnings === 0) {
            $this->info('🎉 STATUS: SEMUA INDIKATOR HIJAU! SISTEM 100% SIAP UNTUK DEPLOY BETA.');
        } elseif ($this->errors === 0) {
            $this->warn("⚠️  STATUS: SIAP DEPLOY DENGAN {$this->warnings} CATATAN (PERINGATAN).");
        } else {
            $this->error("❌ STATUS: DITEMUKAN {$this->errors} MASALAH KRITIS & {$this->warnings} PERINGATAN SEBELUM DEPLOY.");
        }
        $this->info('========================================================================');

        return $this->errors === 0 ? Command::SUCCESS : Command::FAILURE;
    }

    private function passMsg(string $msg): void
    {
        $this->line("  <fg=green;options=bold>✔ PASS</> {$msg}");
    }

    private function warnMsg(string $msg): void
    {
        $this->warnings++;
        $this->line("  <fg=yellow;options=bold>▲ WARN</> {$msg}");
    }

    private function failMsg(string $msg): void
    {
        $this->errors++;
        $this->line("  <fg=red;options=bold>✘ FAIL</> {$msg}");
    }

    private function checkPhpVersionAndExtensions(): void
    {
        $this->newLine();
        $this->comment('▶ FASE 1: PHP Runtime & Ekstensi Server');

        $phpVersion = PHP_VERSION;
        if (version_compare($phpVersion, '8.2.0', '>=')) {
            $this->passMsg("PHP Version: {$phpVersion} (Memenuhi syarat minimum >= 8.2)");
        } else {
            $this->failMsg("PHP Version: {$phpVersion} (Wajib PHP >= 8.2 untuk Laravel 12)");
        }

        $requiredExtensions = [
            'pdo' => 'Database Abstraction (PDO)',
            'mbstring' => 'Multibyte String Processing',
            'openssl' => 'Kriptografi & HTTPS Support',
            'tokenizer' => 'PHP Tokenizer',
            'xml' => 'XML Parser & DOM Engine',
            'ctype' => 'Ctype Character Checking',
            'json' => 'JSON Parser/Encoder',
            'bcmath' => 'Arbitrary Precision Mathematics',
            'fileinfo' => 'MIME Type Detection untuk Upload Dokumen',
            'gd' => 'Manipulasi Gambar & Foto Pegawai (GD/Imagick)',
        ];

        foreach ($requiredExtensions as $ext => $desc) {
            if (extension_loaded($ext)) {
                $this->passMsg("Ekstensi {$ext} aktif ({$desc})");
            } else {
                if ($ext === 'gd') {
                    if (extension_loaded('imagick')) {
                        $this->passMsg("Ekstensi imagick aktif (Alternatif GD)");
                        continue;
                    }
                    $this->warnMsg("Ekstensi gd/imagick tidak aktif (Ekspor PDF foto profil pegawai disarankan menggunakan php-gd / php-imagick)");
                } elseif ($ext === 'bcmath') {
                    $this->warnMsg("Ekstensi bcmath tidak aktif (Disarankan 'apt install php-bcmath' untuk kalkulasi presisi tinggi)");
                } else {
                    $this->failMsg("Ekstensi {$ext} TIDAK aktif ({$desc})");
                }
            }
        }
    }

    private function checkEnvironmentConfig(): void
    {
        $this->newLine();
        $this->comment('▶ FASE 2: Konfigurasi File Environment (.env)');

        $envFile = base_path('.env');
        if (File::exists($envFile)) {
            $this->passMsg("File .env ditemukan di root aplikasi");
        } else {
            $this->failMsg("File .env TIDAK ditemukan! Salin dari .env.beta atau .env.example");
            return;
        }

        $appKey = config('app.key');
        if (!empty($appKey)) {
            $this->passMsg("APP_KEY terkonfigurasi secara aman");
        } else {
            $this->failMsg("APP_KEY kosong! Jalankan 'php artisan key:generate'");
        }

        $appEnv = config('app.env');
        $appDebug = config('app.debug');

        if ($appDebug === false) {
            $this->passMsg("APP_DEBUG=false (Aman untuk beta/production, pesan error tidak bocor)");
        } else {
            if (in_array($appEnv, ['production', 'beta', 'staging'])) {
                $this->failMsg("APP_DEBUG bernilai TRUE di lingkungan {$appEnv}! Wajib disetel false untuk keamanan.");
            } else {
                $this->warnMsg("APP_DEBUG bernilai true (Lingkungan saat ini: {$appEnv}. Ubah ke false saat deploy beta).");
            }
        }

        $appUrl = config('app.url');
        if (str_starts_with($appUrl, 'https://')) {
            $this->passMsg("APP_URL menggunakan protokol HTTPS ({$appUrl})");
        } else {
            $this->warnMsg("APP_URL tidak menggunakan HTTPS ({$appUrl}). Diperlukan HTTPS untuk enkripsi cookie & session aman di production.");
        }
    }

    private function checkDirectoryPermissions(): void
    {
        $this->newLine();
        $this->comment('▶ FASE 3: Izin Tulis Direktori (Storage & Bootstrap Cache)');

        $writablePaths = [
            'storage' => storage_path(),
            'storage/app' => storage_path('app'),
            'storage/app/public' => storage_path('app/public'),
            'storage/framework' => storage_path('framework'),
            'storage/framework/cache' => storage_path('framework/cache'),
            'storage/framework/sessions' => storage_path('framework/sessions'),
            'storage/framework/views' => storage_path('framework/views'),
            'storage/logs' => storage_path('logs'),
            'bootstrap/cache' => base_path('bootstrap/cache'),
        ];

        foreach ($writablePaths as $label => $path) {
            if (!File::isDirectory($path)) {
                File::makeDirectory($path, 0775, true, true);
            }

            if (is_writable($path)) {
                $this->passMsg("Direktori {$label} memiliki izin tulis (writable)");
            } else {
                $this->failMsg("Direktori {$label} TIDAK dapat ditulis (chmod -R 775 {$path})");
            }
        }
    }

    private function checkDatabaseAndMigrations(): void
    {
        $this->newLine();
        $this->comment('▶ FASE 4: Koneksi Database, SQLite WAL & Integritas Data');

        try {
            DB::connection()->getPdo();
            $driver = config('database.default');
            $this->passMsg("Koneksi database ({$driver}) berhasil terhubung");

            if ($driver === 'sqlite') {
                $dbPath = config('database.connections.sqlite.database');
                if (File::exists($dbPath) && is_writable($dbPath)) {
                    $this->passMsg("File database SQLite writable: {$dbPath}");
                } else {
                    $this->failMsg("File database SQLite tidak ditemukan atau tidak writable: {$dbPath}");
                }

                $dir = dirname($dbPath);
                if (is_writable($dir)) {
                    $this->passMsg("Direktori database SQLite writable (Diperlukan untuk WAL file: .sqlite-wal)");
                } else {
                    $this->failMsg("Direktori database SQLite TIDAK writable! Chmod direktori ke 775.");
                }

                $journalMode = DB::select("PRAGMA journal_mode;")[0]->journal_mode ?? '';
                if (strtolower($journalMode) === 'wal') {
                    $this->passMsg("SQLite Journal Mode: WAL (Multi-writer concurrency aktif)");
                } else {
                    $this->warnMsg("SQLite Journal Mode bukan WAL (saat ini: {$journalMode}). Setel DB_JOURNAL_MODE=WAL.");
                }

                $busyTimeout = DB::select("PRAGMA busy_timeout;")[0]->timeout ?? 0;
                if ($busyTimeout >= 10000) {
                    $this->passMsg("SQLite Busy Timeout: {$busyTimeout}ms (Antrian lock konkurensi aktif)");
                } else {
                    $this->warnMsg("SQLite Busy Timeout rendah ({$busyTimeout}ms). Rekomendasi 60000ms.");
                }
            }

            // Periksa tabel-tabel utama
            $expectedTables = ['units', 'users', 'employees', 'performance_appraisals', 'jobs'];
            foreach ($expectedTables as $tbl) {
                if (Schema::hasTable($tbl)) {
                    $this->passMsg("Tabel '{$tbl}' tersedia");
                } else {
                    $this->failMsg("Tabel '{$tbl}' TIDAK ditemukan! Jalankan 'php artisan migrate'");
                }
            }

            // Periksa data induk
            $unitsCount = Unit::count();
            if ($unitsCount >= 76) {
                $this->passMsg("Master Data Unit Kerja AUM terisi lengkap ({$unitsCount} unit)");
            } else {
                $this->warnMsg("Master Data Unit Kerja hanya memuat {$unitsCount} unit (harus 76 unit). Jalankan seeder.");
            }

            $adminCount = User::where('role', User::ROLE_ADMIN)->count();
            if ($adminCount >= 1) {
                $this->passMsg("Akun Administrator Yayasan terdaftar");
            } else {
                $this->failMsg("Tidak ada akun administrator! Buat akun admin atau jalankan seeder.");
            }

        } catch (\Throwable $e) {
            $this->failMsg("Koneksi database gagal: " . $e->getMessage());
        }
    }

    private function checkQueueAndBackgroundWorker(): void
    {
        $this->newLine();
        $this->comment('▶ FASE 5: Sistem Antrian Asinkron (Queue Driver)');

        $queueConn = config('queue.default');
        if (in_array($queueConn, ['database', 'redis'])) {
            $this->passMsg("Queue Driver dikonfigurasi ke '{$queueConn}' (Mendukung antrian asinkron)");
        } else {
            $this->warnMsg("Queue Driver adalah '{$queueConn}'. Untuk production/beta, disarankan 'database' atau 'redis' agar tugas berat tidak memblokir UI.");
        }

        if (Schema::hasTable('jobs')) {
            $this->passMsg("Tabel 'jobs' untuk antrian pekerjaan latar belakang siap");
        } else {
            $this->failMsg("Tabel 'jobs' belum dibuat! Jalankan 'php artisan queue:table && php artisan migrate'");
        }
    }

    private function checkStorageSymlinkAndAssets(): void
    {
        $this->newLine();
        $this->comment('▶ FASE 6: Storage Symlink & Build Assets Frontend');

        $symlinkPath = public_path('storage');
        if (is_link($symlinkPath) || (is_dir($symlinkPath) && File::exists($symlinkPath))) {
            $this->passMsg("Storage Symlink (public/storage) terpasang dengan baik");
        } else {
            $this->warnMsg("Storage Symlink (public/storage) belum dibuat! Jalankan 'php artisan storage:link'");
        }

        $manifestPath = public_path('build/manifest.json');
        if (File::exists($manifestPath)) {
            $this->passMsg("Aset CSS/JS ter-compile untuk production (public/build/manifest.json ditemukan)");
        } else {
            $this->warnMsg("Aset frontend belum di-build! Jalankan 'npm run build' sebelum deploy ke server");
        }
    }
}
