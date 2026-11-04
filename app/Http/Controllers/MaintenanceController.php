<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MaintenanceController extends Controller
{
    /**
     * Memastikan hanya Admin Utama dan Admin Yayasan / IT Support yang berhak mengakses.
     */
    protected function authorizeAdmin(): void
    {
        $user = Auth::user();
        if (!$user || (!$user->isAdminUtama() && !$user->isAdminYayasan())) {
            abort(403, 'Akses ditolak: Modul Pemeliharaan Sistem hanya dapat diakses oleh Admin Utama dan IT Support / Admin Yayasan.');
        }
    }

    /**
     * Halaman Utama Dasbor Pemeliharaan Sistem & Status Maintenance.
     */
    public function index()
    {
        $this->authorizeAdmin();

        // 1. Status Mode Pemeliharaan
        $isDown = app()->isDownForMaintenance();
        $downData = [];
        $downFile = storage_path('framework/down');
        if ($isDown && file_exists($downFile)) {
            $downData = json_decode(file_get_contents($downFile), true) ?: [];
        }

        // 2. Diagnostik Sistem & Lingkungan
        $systemInfo = [
            'app_name'         => config('app.name', 'SIM SDM PDM Kota Yogyakarta'),
            'app_env'          => config('app.env', 'production'),
            'app_debug'        => config('app.debug', false),
            'app_url'          => config('app.url', url('/')),
            'php_version'      => PHP_VERSION,
            'laravel_version'  => app()->version(),
            'server_os'        => PHP_OS_FAMILY . ' (' . php_uname('s') . ' ' . php_uname('r') . ')',
            'memory_limit'     => ini_get('memory_limit'),
            'max_exec_time'    => ini_get('max_execution_time') . 's',
            'storage_symlink'  => file_exists(public_path('storage')),
            'is_down'          => $isDown,
            'down_secret'      => $downData['secret'] ?? null,
            'down_retry'       => $downData['retry'] ?? null,
            'down_status'      => $downData['status'] ?? 503,
        ];

        // 3. Status Basis Data
        $dbStatus = [
            'driver'   => config('database.default'),
            'name'     => 'N/A',
            'status'   => 'Terkoneksi',
            'size'     => 'N/A',
        ];

        try {
            $connection = DB::connection();
            $driver = $connection->getDriverName();
            $dbStatus['driver'] = strtoupper($driver);

            if ($driver === 'sqlite') {
                $dbPath = $connection->getDatabaseName();
                $dbStatus['name'] = basename($dbPath);
                if (file_exists($dbPath)) {
                    $dbStatus['size'] = $this->formatBytes(filesize($dbPath));
                }
            } elseif ($driver === 'mysql') {
                $dbStatus['name'] = $connection->getDatabaseName();
                $sizeQuery = DB::select("SELECT SUM(data_length + index_length) AS size FROM information_schema.TABLES WHERE table_schema = ?", [$dbStatus['name']]);
                if (!empty($sizeQuery[0]->size)) {
                    $dbStatus['size'] = $this->formatBytes((float)$sizeQuery[0]->size);
                }
            }
        } catch (\Throwable $e) {
            $dbStatus['status'] = 'Gagal: ' . $e->getMessage();
        }

        // 4. Informasi Disk & Backup
        $backupDir = storage_path('backups');
        $backups = [];
        $totalBackupSize = 0;

        if (file_exists($backupDir)) {
            $files = File::files($backupDir);
            foreach ($files as $file) {
                $size = $file->getSize();
                $totalBackupSize += $size;
                $backups[] = [
                    'name'       => $file->getFilename(),
                    'size'       => $this->formatBytes($size),
                    'created_at' => date('d M Y H:i:s', $file->getMTime()),
                ];
            }
            // Urutkan backup terbaru di atas
            usort($backups, fn($a, $b) => strcmp($b['name'], $a['name']));
        }

        $logFile = storage_path('logs/laravel.log');
        $logSize = file_exists($logFile) ? $this->formatBytes(filesize($logFile)) : '0 B';

        // Ambil 50 baris terakhir log
        $recentLogs = $this->getRecentLogs($logFile, 50);

        // Kapasitas Disk
        $freeDisk = disk_free_space(base_path());
        $totalDisk = disk_total_space(base_path());
        $diskUsagePercent = $totalDisk > 0 ? round((($totalDisk - $freeDisk) / $totalDisk) * 100, 1) : 0;

        $storageInfo = [
            'free_space'       => $this->formatBytes($freeDisk),
            'total_space'      => $this->formatBytes($totalDisk),
            'usage_percent'    => $diskUsagePercent,
            'backup_count'     => count($backups),
            'total_backup_size'=> $this->formatBytes($totalBackupSize),
            'log_size'         => $logSize,
        ];

        return view('maintenance.index', compact(
            'systemInfo',
            'dbStatus',
            'storageInfo',
            'backups',
            'recentLogs'
        ));
    }

    /**
     * Mengaktifkan atau menonaktifkan Maintenance Mode.
     */
    public function toggle(Request $request)
    {
        $this->authorizeAdmin();

        $isCurrentlyDown = app()->isDownForMaintenance();

        if ($isCurrentlyDown) {
            // Nonaktifkan Maintenance Mode
            Artisan::call('up');
            return redirect()->route('maintenance.index')->with('success', 'Aplikasi berhasil diaktifkan kembali (Maintenance Mode Nonaktif). Sistem kini dapat diakses normal oleh seluruh pengguna.');
        }

        // Validasi parameter aktivasi
        $validated = $request->validate([
            'secret'  => 'nullable|string|alpha_dash|max:60',
            'retry'   => 'nullable|integer|min:10|max:86400',
            'refresh' => 'nullable|integer|min:5|max:3600',
        ]);

        $secret = !empty($validated['secret']) ? $validated['secret'] : 'pdm-bypass-' . Str::random(12);

        $params = [
            '--secret' => $secret,
            '--render' => 'errors.503',
        ];

        if (!empty($validated['retry'])) {
            $params['--retry'] = (int) $validated['retry'];
        }

        if (!empty($validated['refresh'])) {
            $params['--refresh'] = (int) $validated['refresh'];
        }

        Artisan::call('down', $params);

        $bypassUrl = url('/' . $secret);

        return redirect()->route('maintenance.index')->with('warning', "Mode Pemeliharaan (Maintenance Mode) BERHASIL DIAKTIFKAN. Pengguna umum akan menerima respon HTTP 503. Tautan rahasia untuk Admin bypass: {$bypassUrl}");
    }

    /**
     * Membersihkan cache aplikasi (Optimize Clear / Cache / Config / Route / View).
     */
    public function clearCache(Request $request)
    {
        $this->authorizeAdmin();

        $type = $request->input('type', 'all');

        switch ($type) {
            case 'cache':
                Artisan::call('cache:clear');
                $message = 'Cache data aplikasi berhasil dibersihkan.';
                break;
            case 'route':
                Artisan::call('route:clear');
                $message = 'Cache rute URL berhasil dibersihkan.';
                break;
            case 'config':
                Artisan::call('config:clear');
                $message = 'Cache konfigurasi (.env) berhasil dibersihkan.';
                break;
            case 'view':
                Artisan::call('view:clear');
                $message = 'Cache template Blade view berhasil dibersihkan.';
                break;
            case 'all':
            default:
                Artisan::call('optimize:clear');
                $message = 'Seluruh cache sistem (konfigurasi, rute, template Blade, dan data) berhasil dibersihkan.';
                break;
        }

        return redirect()->route('maintenance.index')->with('success', $message);
    }

    /**
     * Mengoptimalkan konfigurasi dan rute aplikasi untuk performa tinggi.
     */
    public function optimize(Request $request)
    {
        $this->authorizeAdmin();

        try {
            Artisan::call('optimize');
            return redirect()->route('maintenance.index')->with('success', 'Optimasi sistem berhasil dijalankan (Konfigurasi dan rute telah dicache untuk performa maksimal).');
        } catch (\Throwable $e) {
            return redirect()->route('maintenance.index')->with('error', 'Optimasi gagal: ' . $e->getMessage());
        }
    }

    /**
     * Memperbaiki atau membuat symlink storage (storage:link).
     */
    public function linkStorage()
    {
        $this->authorizeAdmin();

        try {
            Artisan::call('storage:link');
            return redirect()->route('maintenance.index')->with('success', 'Symlink publik storage (public/storage) berhasil dibuat / disinkronkan.');
        } catch (\Throwable $e) {
            return redirect()->route('maintenance.index')->with('error', 'Pembuatan symlink storage gagal: ' . $e->getMessage());
        }
    }

    /**
     * Memicu pencadangan (backup) database manual instan.
     */
    public function backup()
    {
        $this->authorizeAdmin();

        $backupDir = storage_path('backups');
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $timestamp = date('Ymd_His');
        $defaultConn = config('database.default');

        try {
            if ($defaultConn === 'sqlite') {
                $dbPath = DB::connection()->getDatabaseName();
                if (file_exists($dbPath)) {
                    $targetFile = $backupDir . "/sdm_db_{$timestamp}.sqlite.gz";
                    $data = file_get_contents($dbPath);
                    $gzdata = gzencode($data, 9);
                    file_put_contents($targetFile, $gzdata);
                    return redirect()->route('maintenance.index')->with('success', "Pencadangan SQLite berhasil disimpan: sdm_db_{$timestamp}.sqlite.gz");
                }
            } elseif ($defaultConn === 'mysql') {
                $dbName = config('database.connections.mysql.database');
                $dbUser = config('database.connections.mysql.username');
                $dbPass = config('database.connections.mysql.password');
                $dbHost = config('database.connections.mysql.host', '127.0.0.1');
                $targetFile = $backupDir . "/sdm_mysql_{$dbName}_{$timestamp}.sql.gz";

                $command = "mysqldump --no-tablespaces -h " . escapeshellarg($dbHost) . " -u " . escapeshellarg($dbUser) . " -p" . escapeshellarg($dbPass) . " " . escapeshellarg($dbName) . " | gzip > " . escapeshellarg($targetFile);
                exec($command, $output, $returnVar);

                if ($returnVar === 0 && file_exists($targetFile)) {
                    return redirect()->route('maintenance.index')->with('success', "Pencadangan MySQL berhasil disimpan: sdm_mysql_{$dbName}_{$timestamp}.sql.gz");
                }
                throw new \Exception('mysqldump gagal dieksekusi atau tidak memiliki izin shell.');
            }

            return redirect()->route('maintenance.index')->with('info', 'Pencadangan selesai diproses.');
        } catch (\Throwable $e) {
            return redirect()->route('maintenance.index')->with('error', 'Pencadangan database gagal: ' . $e->getMessage());
        }
    }

    /**
     * Mengosongkan berkas log laravel.log.
     */
    public function clearLog()
    {
        $this->authorizeAdmin();

        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            file_put_contents($logFile, '');
            return redirect()->route('maintenance.index')->with('success', 'Berkas log sistem (storage/logs/laravel.log) berhasil dikosongkan.');
        }

        return redirect()->route('maintenance.index')->with('info', 'Berkas log belum ada atau sudah kosong.');
    }

    /**
     * Membaca N baris terakhir dari berkas log.
     */
    protected function getRecentLogs(string $filePath, int $lines = 50): string
    {
        if (!file_exists($filePath) || filesize($filePath) === 0) {
            return "--- Tidak ada catatan log (Berkas log kosong atau bersih) ---";
        }

        $f = fopen($filePath, 'r');
        if (!$f) {
            return "--- Tidak dapat membaca berkas log ---";
        }

        $buffer = '';
        $lineCount = 0;
        $pos = -2;
        $maxBytes = 250000; // Maksimal 250KB dari ujung file

        fseek($f, 0, SEEK_END);
        $fileSize = ftell($f);

        while ($lineCount < $lines && abs($pos) < $fileSize && abs($pos) < $maxBytes) {
            fseek($f, $pos, SEEK_END);
            $char = fgetc($f);
            if ($char === "\n") {
                $lineCount++;
            }
            $pos--;
        }

        $buffer = fread($f, abs($pos));
        fclose($f);

        return trim($buffer) ?: "--- Tidak ada catatan log ---";
    }

    /**
     * Format byte ke ukuran yang mudah dibaca (KB, MB, GB).
     */
    protected function formatBytes(float|int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

