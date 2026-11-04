<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\CoachingController;
use App\Http\Controllers\LeadershipController;
use App\Http\Controllers\MasterNpmController;
use App\Http\Controllers\EmployeeMutationController;
use App\Http\Controllers\QuarterlyAuditController;
use App\Http\Controllers\RankPromotionController;
use App\Http\Controllers\RecruitmentController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\UnitController;

// Halaman Utama / Landing Page Resmi
Route::get('/', [LandingController::class, 'index'])->name('landing');

// Rute Tamu (Autentikasi & Demo Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/quick-login', [AuthController::class, 'quickLogin'])->name('login.quick');
});

// Lindungi rute dengan auth middleware
Route::middleware(['auth'])->group(function () {
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dasbor utama (data otomatis terfilter sesuai RBAC di controller)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Modul Data Induk Pegawai (Core HR) - URGENT
    Route::get('/employees/{employee}/pdf', [EmployeeController::class, 'printPdf'])->name('employees.print_pdf');
    Route::resource('employees', EmployeeController::class);

    // API Validasi Instan NPM untuk Operator TU Sekolah
    Route::get('/api/check-npm', [MasterNpmController::class, 'check'])->name('api.check_npm');

    // Bank Data Master NPM (Nomor 1 s.d. Terkini)
    Route::resource('master-npms', MasterNpmController::class)->only(['index', 'create', 'store']);

    // Modul Mutasi Pegawai (Pencatatan & Riwayat Mutasi)
    Route::resource('mutations', EmployeeMutationController::class)->only(['index', 'create', 'store']);

    // Modul Pembinaan Pegawai (Ideologi, Loyalitas, Pelatihan & Disiplin)
    Route::resource('coachings', CoachingController::class)->only(['index', 'create', 'store']);

    // MODUL KARIR & KENAIKAN PANGKAT / ALIH STATUS
    Route::get('/promotions', [RankPromotionController::class, 'index'])->name('promotions.index');
    Route::get('/promotions/create', [RankPromotionController::class, 'create'])->name('promotions.create');
    Route::post('/promotions', [RankPromotionController::class, 'store'])->name('promotions.store');
    Route::get('/promotions/{id}', [RankPromotionController::class, 'show'])->name('promotions.show');
    Route::post('/promotions/{id}/verify', [RankPromotionController::class, 'verify'])->name('promotions.verify');
    Route::get('/career/achievements', [CareerController::class, 'achievements'])->name('career.achievements');
    Route::get('/career/performance', [CareerController::class, 'performance'])->name('career.performance');
    Route::post('/career/performance', [CareerController::class, 'storePerformance'])->name('career.performance_store');

    // Monitoring Pembaruan Data Berkala Triwulanan (Per 3 Bulan)
    Route::get('/audits/quarterly', [QuarterlyAuditController::class, 'index'])->name('audits.quarterly');
    Route::post('/audits/quarterly/{unit}', [QuarterlyAuditController::class, 'updateStatus'])->name('audits.update_status');

    // Master Data Unit Kerja (Sekolah & Kantor PDM)
    Route::resource('units', UnitController::class)->only(['index', 'show']);
    Route::post('/units/{unit}/sync-npsn', [UnitController::class, 'syncNpsn'])->name('units.sync_npsn');

    // Modul Rekrutmen & Promosi KS & Rekap Usulan Terpadu (Khusus Tingkat Yayasan / PDM)
    Route::middleware(['role:admin_utama,admin,admin_yayasan,it_support,hr_pusat,ketua_majelis,ketua_yayasan,yayasan'])->group(function () {
        // 1. Modul Rekrutmen Pegawai (GTT, PTT, GTY, PTY)
        Route::get('/recruitments', [RecruitmentController::class, 'index'])->name('recruitments.index');
        Route::get('/recruitments/{id}', [RecruitmentController::class, 'show'])->name('recruitments.show');

        // 2. Modul Promosi Kepala Sekolah
        Route::get('/leadership/headmasters', [LeadershipController::class, 'headmasters'])->name('leadership.headmasters');
        Route::get('/leadership/headmasters/{id}', [LeadershipController::class, 'headmasterShow'])->name('leadership.headmaster_show');
        Route::post('/leadership/headmasters/{selectionId}/nominate', [LeadershipController::class, 'headmasterNominate'])->name('leadership.headmasters.nominate');

        // 3. REKAPITULASI USULAN TERPADU DARI SELURUH SEKOLAH
        Route::get('/proposals/recap', [LeadershipController::class, 'proposalsRecap'])->name('proposals.recap');
        Route::get('/proposals/recap/print', [LeadershipController::class, 'proposalsRecapPrint'])->name('proposals.recap_print');
    });

    // Modul Promosi Tim Manajemen / Waka (Dapat diakses Yayasan & Sekolah, non-Pegawai)
    Route::middleware(['role:admin_utama,admin,admin_yayasan,it_support,hr_pusat,ketua_majelis,ketua_yayasan,yayasan,kepsek,kepala_sekolah,tu_unit,tu,tu_sekolah'])->group(function () {
        Route::get('/leadership/vice-headmasters', [LeadershipController::class, 'viceHeadmasters'])->name('leadership.vice_headmasters');
        Route::post('/leadership/vice-headmasters', [LeadershipController::class, 'viceHeadmasterStore'])->name('leadership.vice_headmasters.store');
        Route::post('/leadership/vice-headmasters/{id}/verify', [LeadershipController::class, 'viceHeadmasterVerify'])->name('leadership.vice_headmasters.verify');
    });

    // Modul Pemeliharaan Sistem & Maintenance Mode (Khusus Admin Utama & IT Support / Admin Yayasan)
    Route::middleware(['role:admin_utama,admin,admin_yayasan,it_support,hr_pusat'])->group(function () {
        Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
        Route::post('/maintenance/toggle', [MaintenanceController::class, 'toggle'])->name('maintenance.toggle');
        Route::post('/maintenance/clear-cache', [MaintenanceController::class, 'clearCache'])->name('maintenance.clear_cache');
        Route::post('/maintenance/optimize', [MaintenanceController::class, 'optimize'])->name('maintenance.optimize');
        Route::post('/maintenance/backup', [MaintenanceController::class, 'backup'])->name('maintenance.backup');
        Route::post('/maintenance/storage-link', [MaintenanceController::class, 'linkStorage'])->name('maintenance.storage_link');
        Route::post('/maintenance/clear-log', [MaintenanceController::class, 'clearLog'])->name('maintenance.clear_log');
    });
});
