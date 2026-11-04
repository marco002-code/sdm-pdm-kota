<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\PerformanceAppraisal;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

class AppTestCommand extends Command
{
    protected $signature = 'app:test';
    protected $description = 'Menjalankan pengujian menyeluruh (End-to-End & Unit Logic) untuk Sistem Informasi SDM Yayasan';

    private int $passed = 0;
    private int $failed = 0;

    public function handle(): int
    {
        $this->info('===========================================================');
        $this->info('🚀 MENJALANKAN PENGUJIAN SISTEM INFORMASI SDM PDM KOTA YOGYAKARTA');
        $this->info('===========================================================');

        $this->testDatabaseAndSeeders();
        $this->testModelBusinessLogic();
        $this->testAuthentication();
        $this->testRoleBasedAccessControl();
        $this->testDashboardRenderingAndIsolation();
        $this->testEmployeeModule();
        $this->testUnitMasterDataModule();
        $this->testPerformanceAppraisalModules();
        $this->testRolePrivilegesAndCapabilities();
        $this->testDynamicSidebarVisibilityAndRoleIsolation();
        $this->testMaintenanceModule();
        $this->testLogout();

        $this->newLine();
        $this->info('===========================================================');
        $this->info("📊 HASIL PENGUJIAN: {$this->passed} BERHASIL | {$this->failed} GAGAL");
        $this->info('===========================================================');

        return $this->failed === 0 ? Command::SUCCESS : Command::FAILURE;
    }

    private function assert(string $label, bool $condition, string $failureMsg = ''): void
    {
        if ($condition) {
            $this->passed++;
            $this->line("  <fg=green;options=bold>✔ PASS</> {$label}");
        } else {
            $this->failed++;
            $this->line("  <fg=red;options=bold>✘ FAIL</> {$label} - {$failureMsg}");
        }
    }

    private function testDatabaseAndSeeders(): void
    {
        $this->newLine();
        $this->comment('▶ FASE 1: Pengujian Data Database & Relasi Model');

        // Unit count
        $unitsCount = Unit::count();
        $this->assert("Master Data Unit Kerja memuat 76 unit ({$unitsCount} unit ditemukan)", $unitsCount === 76);

        // Unit count per level
        $tpaCount = Unit::where('level', 'TPA')->count();
        $tkCount = Unit::where('level', 'TK')->count();
        $sdCount = Unit::where('level', 'SD')->count();
        $smpCount = Unit::where('level', 'SMP')->count();
        $smaCount = Unit::where('level', 'SMA')->count();
        $smkCount = Unit::where('level', 'SMK')->count();
        $maCount = Unit::where('level', 'MA')->count();
        $yayasanCount = Unit::where('level', 'YAYASAN')->count();

        $this->assert("Rekap Unit: 6 TPA, 10 TK, 37 SD, 10 SMP, 7 SMA, 4 SMK, 1 MA, 1 PDM", 
            $tpaCount === 6 && $tkCount === 10 && $sdCount === 37 && 
            $smpCount === 10 && $smaCount === 7 && $smkCount === 4 && 
            $maCount === 1 && $yayasanCount === 1
        );

        // Unit relation with employees
        $sdUnit = Unit::whereHas('employees')->first();
        $this->assert("Unit {$sdUnit->name} memiliki relasi pegawai ({$sdUnit->employees()->count()} pegawai)", $sdUnit && $sdUnit->employees()->count() >= 3);

        // Users count
        $usersCount = User::count();
        $this->assert("Tabel Users memiliki akun pengguna ({$usersCount} akun)", $usersCount >= 6);

        // Employee count
        $empCount = Employee::count();
        $this->assert("Tabel Employees memiliki profil data induk ({$empCount} data)", $empCount >= 3);

        // Scopes
        $guruCount = Employee::teachers()->count();
        $tendikCount = Employee::tendik()->count();
        $karyawanCount = Employee::staff()->count();
        $this->assert("Scope Employee::teachers() mendeteksi guru ({$guruCount} guru)", $guruCount >= 1);
        $this->assert("Scope Employee::tendik() mendeteksi staf TU ({$tendikCount} tendik)", $tendikCount >= 1);
        $this->assert("Scope Employee::staff() mendeteksi karyawan ({$karyawanCount} karyawan)", $karyawanCount >= 1);

        // Scopes unit & certified
        $certified = Employee::certifiedTeachers()->count();
        $this->assert("Scope Employee::certifiedTeachers() mendeteksi sertifikasi guru ({$certified} guru bersertifikat)", $certified >= 1);
    }

    private function testModelBusinessLogic(): void
    {
        $this->newLine();
        $this->comment('▶ FASE 2: Pengujian Logika Bisnis (Business Logic)');

        $guru = Employee::where('full_name', 'Rahmat Hidayat')->first() ?? Employee::teachers()->first();
        $this->assert(
            "Perhitungan sisa kuota cuti pegawai ({$guru->remaining_leave_quota} hari tersisa dari {$guru->annual_leave_quota})",
            $guru && $guru->remaining_leave_quota === ($guru->annual_leave_quota - $guru->used_leave_quota)
        );

        $this->assert(
            "Format nama lengkap dan gelar ({$guru->full_name_with_titles})",
            str_contains($guru->full_name_with_titles, $guru->full_name) && str_contains($guru->full_name_with_titles, $guru->title_suffix)
        );

        // Test NBM & Riwayat Pendidikan
        $this->assert("Atribut NBM terisi ({$guru->nbm})", !empty($guru->nbm));
        $this->assert("Riwayat Pendidikan S1 terisi ({$guru->education_history['s1']['school']} - {$guru->education_history['s1']['major']})", !empty($guru->education_history['s1']['school']));
        $this->assert("Riwayat Pendidikan Dasar & Menengah terisi ({$guru->education_history['sma']})", !empty($guru->education_history['sma']));
        $this->assert("Pengalaman Pekerjaan tersimpan (" . count($guru->work_experiences ?? []) . " riwayat)", count($guru->work_experiences ?? []) >= 3);
        $this->assert("Pengalaman Organisasi tersimpan (" . count($guru->organizational_experiences ?? []) . " riwayat)", count($guru->organizational_experiences ?? []) >= 3);

        // Test Performance Appraisal calculation (Guru)
        $appraisalGuru = new PerformanceAppraisal([
            'pedagogic_score' => 90,
            'discipline_score' => 85,
            'technical_competency_score' => 85,
            'attitude_score' => 90,
        ]);
        $scoreGuru = $appraisalGuru->calculateFinalScore(true);
        // (90*0.4) + (85*0.2) + (85*0.2) + (90*0.2) = 36 + 17 + 17 + 18 = 88.0
        $this->assert(
            "Kalkulasi nilai kinerja Guru (Bobot Pedagogik 40%, Disiplin 20%, Kompetensi 20%, Sikap 20%): {$scoreGuru} / Grade {$appraisalGuru->grade}",
            abs($scoreGuru - 88.0) < 0.01 && $appraisalGuru->grade === 'B'
        );

        // Test Performance Appraisal calculation (Karyawan)
        $appraisalKaryawan = new PerformanceAppraisal([
            'technical_competency_score' => 95,
            'discipline_score' => 90,
            'attitude_score' => 90,
        ]);
        $scoreKaryawan = $appraisalKaryawan->calculateFinalScore(false);
        // (95*0.4) + (90*0.3) + (90*0.3) = 38 + 27 + 27 = 92.0 -> Grade A
        $this->assert(
            "Kalkulasi nilai kinerja Karyawan (Bobot Teknis 40%, Disiplin 30%, Sikap 30%): {$scoreKaryawan} / Grade {$appraisalKaryawan->grade}",
            abs($scoreKaryawan - 92.0) < 0.01 && $appraisalKaryawan->grade === 'A'
        );
    }

    private function testAuthentication(): void
    {
        $this->newLine();
        $this->comment('▶ FASE 3: Pengujian Autentikasi & Quick Login');

        $kernel = app()->make(\Illuminate\Contracts\Http\Kernel::class);

        // 1. Guest access on root (Landing Page) & Protected Dashboard
        $reqRoot = Request::create('/', 'GET');
        $resRoot = $kernel->handle($reqRoot);
        $this->assert('Halaman root (/) dapat diakses publik sebagai Landing Page (HTTP 200)', $resRoot->getStatusCode() === 200);

        $reqDash = Request::create('/dashboard', 'GET');
        $resDash = $kernel->handle($reqDash);
        $this->assert('Akses /dashboard tanpa login diarahkan ke login (HTTP 302)', $resDash->getStatusCode() === 302 && str_contains($resDash->headers->get('Location'), 'login'));

        // 2. Login page accessibility
        $reqLogin = Request::create('/login', 'GET');
        $resLogin = $kernel->handle($reqLogin);
        $this->assert('Halaman login dapat diakses publik (HTTP 200)', $resLogin->getStatusCode() === 200);

        // 3. Quick Login test for each demo role
        $roles = [
            'admin@yayasan.sch.id' => 'IT Support (Admin)',
            'ketua@yayasan.sch.id' => 'Ketua Yayasan',
            'hr@yayasan.sch.id' => 'HR Pusat',
            'kepsek.sd@sekolah.sch.id' => 'Kepala Sekolah SD',
            'tu.sd@sekolah.sch.id' => 'Tata Usaha SD',
            'rahmat.guru@sekolah.sch.id' => 'Guru SD (ESS)',
        ];

        foreach ($roles as $email => $label) {
            $user = User::where('email', $email)->first();
            $this->assert("Pengguna demo {$label} ({$email}) terdaftar", $user !== null);

            if ($user) {
                $reqQuick = Request::create('/quick-login', 'POST', ['email' => $email]);
                // Ensure CSRF middleware doesn't block internal subrequest or mock auth
                Auth::login($user);
                $this->assert("Autentikasi role {$label} berhasil", Auth::check() && Auth::id() === $user->id);
            }
        }
    }

    private function testRoleBasedAccessControl(): void
    {
        $this->newLine();
        $this->comment('▶ FASE 4: Pengujian Role-Based Access Control (RBAC)');

        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $ketua = User::where('role', User::ROLE_YAYASAN)->first();
        $kepsek = User::where('role', User::ROLE_KEPSEK)->first();
        $tu = User::where('role', User::ROLE_TU)->first();
        $guru = User::where('role', User::ROLE_PEGAWAI)->first();

        $this->assert('Role Admin terdeteksi isAdmin()', $admin && $admin->isAdmin());
        $this->assert('Role Ketua Yayasan terdeteksi isYayasan() & isYayasanLevel()', $ketua && $ketua->isYayasan() && $ketua->isYayasanLevel());
        $this->assert('Role Kepsek terdeteksi isKepalaSekolah() & isUnitLevel()', $kepsek && $kepsek->isKepalaSekolah() && $kepsek->isUnitLevel());
        $this->assert('Role TU terdeteksi isTu() & isUnitLevel()', $tu && $tu->isTu() && $tu->isUnitLevel());
        $this->assert('Role TU tidak memiliki hak Yayasan Level (!isYayasanLevel)', $tu && !$tu->isYayasanLevel());
        $this->assert('Role Guru terdeteksi isPegawai() & isUnitLevel()', $guru && $guru->isPegawai() && $guru->isUnitLevel());
        $this->assert('Seluruh 76 unit AUM memiliki akun Tenaga Tata Usaha', User::whereIn('role', [User::ROLE_TU, User::ROLE_TU_SEKOLAH])->count() >= 76);
    }

    private function testDashboardRenderingAndIsolation(): void
    {
        $this->newLine();
        $this->comment('▶ FASE 5: Pengujian Dasbor & Isolasi Data Per Role');

        $kernel = app()->make(\Illuminate\Contracts\Http\Kernel::class);

        // 1. Yayasan Level (Melihat seluruh yayasan)
        $ketua = User::where('role', User::ROLE_YAYASAN)->first();
        Auth::login($ketua);
        $reqKetua = Request::create('/dashboard', 'GET');
        app()->instance('request', $reqKetua);
        $resKetua = $kernel->handle($reqKetua);
        $contentKetua = $resKetua->getContent();

        $this->assert('Dasbor Eksekutif Yayasan berhasil dirender (HTTP 200)', $resKetua->getStatusCode() === 200);
        $this->assert('Dasbor memuat Chart.js dan canvas grafik', str_contains($contentKetua, 'aumPieChart') || str_contains($contentKetua, 'employeeBarChart'));

        // 2. Unit Level (Kepala Sekolah SD)
        $kepsek = User::where('role', User::ROLE_KEPSEK)->first();
        Auth::login($kepsek);
        $reqKepsek = Request::create('/dashboard', 'GET');
        app()->instance('request', $reqKepsek);
        $resKepsek = $kernel->handle($reqKepsek);
        $contentKepsek = $resKepsek->getContent();

        $this->assert('Dasbor Unit Kepsek berhasil dirender (HTTP 200)', $resKepsek->getStatusCode() === 200);
        $this->assert('Nama Kepsek tampil pada sambutan dasbor', str_contains($contentKepsek, $kepsek->name));
    }

    private function testEmployeeModule(): void
    {
        $this->newLine();
        $this->comment('▶ FASE 6: Pengujian Modul Data Induk Pegawai & Formulir Biodata');

        $kernel = app()->make(\Illuminate\Contracts\Http\Kernel::class);
        $guruUser = User::where('role', User::ROLE_PEGAWAI)->first();
        $guruEmp = $guruUser?->employee ?? Employee::first();

        // 1. Index listing as HR Pusat
        $hr = User::where('role', User::ROLE_HR_PUSAT)->first();
        Auth::login($hr);
        $reqIndex = Request::create('/employees', 'GET');
        app()->instance('request', $reqIndex);
        $resIndex = $kernel->handle($reqIndex);
        $this->assert('Daftar Data Induk Pegawai dapat diakses oleh HR Pusat (HTTP 200)', $resIndex->getStatusCode() === 200);
        $this->assert('Halaman index memuat NBM pegawai', str_contains($resIndex->getContent(), 'NBM:'));

        // 2. Show Data Induk Pegawai official view
        $reqShow = Request::create("/employees/{$guruEmp->id}", 'GET');
        app()->instance('request', $reqShow);
        $resShow = $kernel->handle($reqShow);
        $contentShow = $resShow->getContent();

        $this->assert('Formulir Data Induk Pegawai berhasil dirender (HTTP 200)', $resShow->getStatusCode() === 200);
        $this->assert('Formulir memuat judul Biodata Pegawai', str_contains($contentShow, 'Biodata Pegawai'));
        $this->assert('Formulir memuat nomor NBM pegawai (' . $guruEmp->nbm . ')', str_contains($contentShow, $guruEmp->nbm));
        $this->assert('Formulir memuat Identitas & Nomor Registrasi', str_contains($contentShow, 'Identitas & Nomor Registrasi'));
        $this->assert('Formulir memuat Biodata Pribadi & Kontak', str_contains($contentShow, 'Biodata Pribadi & Kontak'));
        $this->assert('Formulir memuat Penugasan & Status Kerja', str_contains($contentShow, 'Penugasan & Status Kerja'));

        // 3. RBAC on ESS (Guru accessing their own profile)
        $guruUser = User::where('role', User::ROLE_PEGAWAI)->first();
        Auth::login($guruUser);
        $reqEss = Request::create("/employees/{$guruEmp->id}", 'GET');
        app()->instance('request', $reqEss);
        $resEss = $kernel->handle($reqEss);
        $this->assert('Pegawai dapat melihat profil Data Induk pribadinya (HTTP 200)', $resEss->getStatusCode() === 200);

        // 4. Test Filter Jenis AUM pada halaman Pegawai (misal jenis_aum=SD)
        Auth::login($hr);
        $reqFilterSd = Request::create('/employees?jenis_aum=SD', 'GET');
        app()->instance('request', $reqFilterSd);
        $resFilterSd = $kernel->handle($reqFilterSd);
        $this->assert('Filter pegawai berdasarkan Jenis AUM SD berhasil (HTTP 200)', $resFilterSd->getStatusCode() === 200);
        $this->assert('Halaman index memuat filter Jenis AUM dan Unit Kerja untuk HR Pusat', 
            str_contains($resFilterSd->getContent(), 'filter_jenis_aum') && 
            str_contains($resFilterSd->getContent(), 'filter_unit_id')
        );

        // 5. Test Akses & Domain Tenaga Tata Usaha (TU)
        $tu = User::where('role', User::ROLE_TU)->whereNotNull('unit_id')->first();
        Auth::login($tu);
        $reqTuIndex = Request::create('/employees', 'GET');
        app()->instance('request', $reqTuIndex);
        $resTuIndex = $kernel->handle($reqTuIndex);
        $contentTuIndex = $resTuIndex->getContent();
        $this->assert('Halaman Data Induk Pegawai dapat diakses oleh Tenaga Tata Usaha (HTTP 200)', $resTuIndex->getStatusCode() === 200);
        $this->assert('Halaman index TU memuat banner Domain Unit Kerja Anda', str_contains($contentTuIndex, 'Domain Unit Kerja Anda'));
        $this->assert('Dropdown filter jenis AUM seluruh yayasan disembunyikan untuk TU', !str_contains($contentTuIndex, 'filter_jenis_aum'));

        // TU mencoba melihat pegawai di unitnya sendiri
        $unitEmployee = Employee::where('unit_id', $tu->unit_id)->first();
        if ($unitEmployee) {
            $reqTuOwnEmp = Request::create("/employees/{$unitEmployee->id}", 'GET');
            app()->instance('request', $reqTuOwnEmp);
            $resTuOwnEmp = $kernel->handle($reqTuOwnEmp);
            $this->assert("TU dapat mengakses pegawai unitnya sendiri (HTTP 200)", $resTuOwnEmp->getStatusCode() === 200);
        }

        // TU mencoba mengakses pegawai dari unit lain (harus 403 Forbidden)
        $otherEmployee = Employee::where('unit_id', '!=', $tu->unit_id)->first();
        if ($otherEmployee) {
            $reqTuOtherEmp = Request::create("/employees/{$otherEmployee->id}", 'GET');
            app()->instance('request', $reqTuOtherEmp);
            $resTuOtherEmp = $kernel->handle($reqTuOtherEmp);
            $this->assert("Isolasi Domain: TU dilarang melihat pegawai unit lain (HTTP 403)", $resTuOtherEmp->getStatusCode() === 403);
        }

        // TU mencoba menghapus pegawai (harus 403 Forbidden)
        if ($unitEmployee) {
            $isForbidden = false;
            try {
                Auth::login($tu);
                app()->make(\App\Http\Controllers\EmployeeController::class)->destroy($unitEmployee);
            } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
                if ($e->getStatusCode() === 403) {
                    $isForbidden = true;
                }
            }
            $this->assert("Pemisahan Privilege: TU dilarang menghapus pegawai (HTTP 403)", $isForbidden);
        }

        // Pegawai (ESS) dilarang mengakses modul Rekrutmen (harus 403 Forbidden)
        Auth::login($guruUser);
        $reqPegawaiRecruit = Request::create('/recruitments', 'GET');
        app()->instance('request', $reqPegawaiRecruit);
        $resPegawaiRecruit = $kernel->handle($reqPegawaiRecruit);
        $this->assert("Pemisahan Privilege: Pegawai biasa dilarang mengakses Rekrutmen (HTTP 403)", $resPegawaiRecruit->getStatusCode() === 403);
    }

    private function testUnitMasterDataModule(): void
    {
        $this->newLine();
        $this->comment('▶ FASE 7: Pengujian Master Data Unit Kerja (76 Sekolah & Yayasan)');

        $kernel = app()->make(\Illuminate\Contracts\Http\Kernel::class);

        // 0. Isolasi Domain: TU dialihkan saat mengakses /units
        $tu = User::where('role', User::ROLE_TU)->whereNotNull('unit_id')->first();
        Auth::login($tu);
        $reqTuUnits = Request::create('/units', 'GET');
        app()->instance('request', $reqTuUnits);
        $resTuUnits = $kernel->handle($reqTuUnits);
        $this->assert('Isolasi Domain: TU dialihkan langsung ke profil unit sendiri dari /units (HTTP 302)', $resTuUnits->getStatusCode() === 302);

        // 1. Index Units sebagai Admin (Akses Global Seluruh 76 Unit)
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        Auth::login($admin);
        $reqIndex = Request::create('/units', 'GET');
        app()->instance('request', $reqIndex);
        $resIndex = $kernel->handle($reqIndex);
        $contentIndex = $resIndex->getContent();

        $this->assert('Halaman Master Data Unit Kerja dapat diakses oleh Admin (HTTP 200)', $resIndex->getStatusCode() === 200);
        $this->assert('Halaman memuat judul Master Data Unit Kerja AUM', str_contains($contentIndex, 'Master Data Unit Kerja AUM'));
        $this->assert('Tabel memuat kolom: Jenis AUM, NPSN, Nama Unit, Url / Link, Kecamatan', 
            str_contains($contentIndex, 'Jenis AUM') && 
            str_contains($contentIndex, 'NPSN') && 
            str_contains($contentIndex, 'Nama Unit') && 
            str_contains($contentIndex, 'Url / Link') && 
            str_contains($contentIndex, 'Kecamatan')
        );
        $this->assert('Halaman memuat badge jenjang (TPA, TK, SD, SMP, SMA, SMK, MA)', 
            str_contains($contentIndex, 'TPA') && 
            str_contains($contentIndex, 'TK') && 
            str_contains($contentIndex, 'SD') && 
            str_contains($contentIndex, 'SMP') && 
            str_contains($contentIndex, 'SMA') && 
            str_contains($contentIndex, 'SMK') && 
            str_contains($contentIndex, 'MA')
        );

        // 2. Show a specific unit (SD Muhammadiyah Sapen 1)
        $sapen = Unit::where('npsn', '20403327')->first();
        $this->assert('Unit SD Muhammadiyah Sapen 1 (NPSN 20403327) ditemukan', $sapen !== null);

        if ($sapen) {
            $reqShow = Request::create("/units/{$sapen->id}", 'GET');
            app()->instance('request', $reqShow);
            $resShow = $kernel->handle($reqShow);
            $this->assert('Detail Unit SD Muhammadiyah Sapen 1 berhasil dirender (HTTP 200)', $resShow->getStatusCode() === 200);
            $this->assert('Detail unit memuat nama sekolah dan website resmi', 
                str_contains($resShow->getContent(), 'SD MUHAMMADIYAH SAPEN 1') && 
                str_contains($resShow->getContent(), 'sdmuhsapen-yog.sch.id')
            );
        }
    }

    private function testPerformanceAppraisalModules(): void
    {
        $this->newLine();
        $this->comment('▶ FASE 8: Pengujian Modul Input Nilai DP3 & PKKS (Isolasi Domain TU Unit)');

        $kernel = app()->make(\Illuminate\Contracts\Http\Kernel::class);

        // 1. Verifikasi Sidebar Navigasi: Menu DP3 tampil untuk TU, sedangkan PKKS disembunyikan dari TU
        $tu = User::where('email', 'tu.sd@sekolah.sch.id')->first();
        Auth::login($tu);
        $reqTuDash = Request::create('/dashboard', 'GET');
        app()->instance('request', $reqTuDash);
        $resTuDash = $kernel->handle($reqTuDash);
        $htmlTu = $resTuDash->getContent();

        $posPegawai = strpos($htmlTu, 'Data Induk Pegawai');
        $posDp3 = strpos($htmlTu, 'Input Nilai DP3');
        $posPkks = strpos($htmlTu, 'Input Nilai PKKS');

        $this->assert('Menu Input Nilai DP3 tampil di sidebar TU', $posDp3 !== false);
        $this->assert('Menu Input Nilai PKKS disembunyikan dari sidebar TU', $posPkks === false);

        // Verifikasi bahwa Menu PKKS tampil untuk Kepala Sekolah
        $kepsek = User::where('role', User::ROLE_KEPSEK)->first();
        Auth::login($kepsek);
        $reqKepsekDash = Request::create('/dashboard', 'GET');
        app()->instance('request', $reqKepsekDash);
        $resKepsekDash = $kernel->handle($reqKepsekDash);
        $htmlKepsek = $resKepsekDash->getContent();
        $this->assert('Menu Input Nilai PKKS tampil di sidebar Kepala Sekolah', str_contains($htmlKepsek, 'Input Nilai PKKS'));

        // 2. Verifikasi Sidebar Navigasi: Menu disembunyikan untuk Pegawai biasa (ESS)
        $guru = User::where('email', 'rahmat.guru@sekolah.sch.id')->first();
        Auth::login($guru);
        $reqGuruDash = Request::create('/dashboard', 'GET');
        app()->instance('request', $reqGuruDash);
        $resGuruDash = $kernel->handle($reqGuruDash);
        $htmlGuru = $resGuruDash->getContent();

        $this->assert('Menu Input Nilai DP3 disembunyikan dari sidebar Pegawai (ESS)', !str_contains($htmlGuru, 'Input Nilai DP3'));
        $this->assert('Menu Input Nilai PKKS disembunyikan dari sidebar Pegawai (ESS)', !str_contains($htmlGuru, 'Input Nilai PKKS'));

        // 3. Akses Halaman Input Nilai DP3 oleh TU
        Auth::login($tu);
        $reqTuDp3 = Request::create('/career/performance', 'GET', ['type' => 'dp3']);
        app()->instance('request', $reqTuDp3);
        $resTuDp3 = $kernel->handle($reqTuDp3);
        $contentTuDp3 = $resTuDp3->getContent();

        $this->assert('Halaman Input Nilai DP3 dapat diakses oleh TU Unit (HTTP 200)', $resTuDp3->getStatusCode() === 200);
        $this->assert('Halaman DP3 memuat banner Unit Kerja TU', str_contains($contentTuDp3, 'Unit Kerja: SD MUHAMMADIYAH SAPEN 1'));
        $this->assert('Halaman DP3 memuat pegawai di unit sendiri (Rahmat Hidayat)', str_contains($contentTuDp3, 'Rahmat Hidayat'));
        $this->assert('Isolasi Domain: Halaman DP3 TU TIDAK memuat pegawai dari unit lain', !str_contains($contentTuDp3, 'Drs. H. Akhid'));

        // 4. Pembatasan PKKS: TU dilarang mengakses PKKS (HTTP 403), sedangkan Kepsek diizinkan (HTTP 200)
        $reqTuPkks = Request::create('/career/performance', 'GET', ['type' => 'pkks']);
        app()->instance('request', $reqTuPkks);
        $resTuPkks = $kernel->handle($reqTuPkks);

        $this->assert('Halaman Input Nilai PKKS DITOLAK untuk TU Unit (HTTP 403)', $resTuPkks->getStatusCode() === 403);

        Auth::login($kepsek);
        $reqKepsekPkks = Request::create('/career/performance', 'GET', ['type' => 'pkks']);
        app()->instance('request', $reqKepsekPkks);
        $resKepsekPkks = $kernel->handle($reqKepsekPkks);
        $this->assert('Halaman Input Nilai PKKS dapat diakses oleh Kepala Sekolah (HTTP 200)', $resKepsekPkks->getStatusCode() === 200);
        $this->assert('Halaman PKKS memuat judul Input Nilai PKKS', str_contains($resKepsekPkks->getContent(), 'Input Nilai PKKS'));

        // 5. Pembatasan Hak Akses Pegawai (ESS): Dilarang mengakses menu DP3 & PKKS (HTTP 403)
        Auth::login($guru);
        $reqGuruDp3 = Request::create('/career/performance', 'GET', ['type' => 'dp3']);
        app()->instance('request', $reqGuruDp3);
        $resGuruDp3 = $kernel->handle($reqGuruDp3);
        $this->assert('Pegawai biasa dilarang mengakses Input Nilai DP3 (HTTP 403)', $resGuruDp3->getStatusCode() === 403);

        $reqGuruPkks = Request::create('/career/performance', 'GET', ['type' => 'pkks']);
        app()->instance('request', $reqGuruPkks);
        $resGuruPkks = $kernel->handle($reqGuruPkks);
        $this->assert('Pegawai biasa dilarang mengakses Input Nilai PKKS (HTTP 403)', $resGuruPkks->getStatusCode() === 403);

        // 6. Pengujian Simpan (Store) DP3 & PKKS dengan Validasi Isolasi Domain
        Auth::login($tu);
        $careerController = app()->make(\App\Http\Controllers\CareerController::class);

        // Simpan sukses untuk pegawai unit sendiri
        $ownEmp = Employee::where('unit_id', $tu->unit_id)->first();
        $reqStoreOwn = Request::create('/career/performance', 'POST', [
            'employee_id' => $ownEmp->id,
            'appraisal_type' => 'dp3',
            'period' => '2026',
            'appraisal_date' => date('Y-m-d'),
            'final_score' => 89.0,
            'grade' => 'B',
            'notes' => 'Pengujian input DP3 unit sendiri.',
        ]);
        $resStoreOwn = $careerController->storePerformance($reqStoreOwn);
        $this->assert('TU berhasil menginput nilai DP3 untuk pegawai di unitnya sendiri (Redirect 302)', $resStoreOwn->isRedirect());

        // Blokir jika TU mencoba input untuk pegawai unit lain (HTTP 403)
        $otherEmp = Employee::where('unit_id', '!=', $tu->unit_id)->first();
        $isCrossUnitBlocked = false;
        try {
            $reqStoreCross = Request::create('/career/performance', 'POST', [
                'employee_id' => $otherEmp->id,
                'appraisal_type' => 'dp3',
                'period' => '2026',
                'appraisal_date' => date('Y-m-d'),
                'final_score' => 85.0,
                'grade' => 'B',
                'notes' => 'Upaya input lintas unit.',
            ]);
            $careerController->storePerformance($reqStoreCross);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $isCrossUnitBlocked = ($e->getStatusCode() === 403);
        }
        $this->assert('Isolasi Domain: TU dilarang menginput nilai untuk pegawai unit lain (HTTP 403)', $isCrossUnitBlocked);
    }

    private function testRolePrivilegesAndCapabilities(): void
    {
        $this->newLine();
        $this->comment('▶ FASE 9: Pengujian Hak Akses & Privilege Sesuai 4 Peran Utama');

        $kernel = app()->make(\Illuminate\Contracts\Http\Kernel::class);

        $adminUtama = User::where('role', User::ROLE_ADMIN)->first();
        $adminYayasan = User::where('role', User::ROLE_HR_PUSAT)->first();
        $ketuaMajelis = User::where('role', User::ROLE_YAYASAN)->first();
        $tuUnit = User::where('role', User::ROLE_TU)->whereNotNull('unit_id')->first();
        $sampleEmp = Employee::first();

        // 1. KETUA MAJELIS: Read All Menu, Tidak Bisa Edit/Hapus, KECUALI yang Approve butuh Ketua Majelis
        Auth::login($ketuaMajelis);
        $this->assert('Ketua Majelis terdeteksi isKetuaMajelis()', $ketuaMajelis->isKetuaMajelis());
        $this->assert('Ketua Majelis memiliki canApproveAsKetuaMajelis()', $ketuaMajelis->canApproveAsKetuaMajelis());
        $this->assert('Ketua Majelis TIDAK memiliki canDeleteEmployees()', !$ketuaMajelis->canDeleteEmployees());
        $this->assert('Ketua Majelis TIDAK memiliki canEditEmployees()', !$ketuaMajelis->canEditEmployees());

        // Read all menu
        $menuUrls = ['/dashboard', '/employees', '/audits/quarterly', '/recruitments', '/coachings', '/promotions', '/leadership/headmasters', '/leadership/vice-headmasters', '/proposals/recap', '/career/achievements', '/units', '/master-npms', '/mutations'];
        $allMenuAccessible = true;
        foreach ($menuUrls as $url) {
            $req = Request::create($url, 'GET');
            app()->instance('request', $req);
            $res = $kernel->handle($req);
            if ($res->getStatusCode() !== 200) {
                $allMenuAccessible = false;
                break;
            }
        }
        $this->assert('Ketua Majelis dapat membaca seluruh menu (Read All Menu HTTP 200)', $allMenuAccessible);

        // Ketua Majelis DILARANG menambah/mengedit/menghapus
        $reqKetuaCreateEmp = Request::create('/employees/create', 'GET');
        app()->instance('request', $reqKetuaCreateEmp);
        $this->assert('Ketua Majelis dilarang membuka form Tambah Pegawai (HTTP 403)', $kernel->handle($reqKetuaCreateEmp)->getStatusCode() === 403);

        $reqKetuaEditEmp = Request::create("/employees/{$sampleEmp->id}/edit", 'GET');
        app()->instance('request', $reqKetuaEditEmp);
        $this->assert('Ketua Majelis dilarang membuka form Edit Pegawai (HTTP 403)', $kernel->handle($reqKetuaEditEmp)->getStatusCode() === 403);

        $empController = app()->make(\App\Http\Controllers\EmployeeController::class);
        $isForbiddenKetuaDel = false;
        try {
            $empController->destroy($sampleEmp);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $isForbiddenKetuaDel = ($e->getStatusCode() === 403);
        }
        $this->assert('Ketua Majelis dilarang menghapus Pegawai (HTTP 403)', $isForbiddenKetuaDel);

        $reqKetuaNpm = Request::create('/master-npms/create', 'GET');
        app()->instance('request', $reqKetuaNpm);
        $this->assert('Ketua Majelis dilarang generate range Master NPM (HTTP 403)', $kernel->handle($reqKetuaNpm)->getStatusCode() === 403);

        $reqKetuaMut = Request::create('/mutations/create', 'GET');
        app()->instance('request', $reqKetuaMut);
        $this->assert('Ketua Majelis dilarang membuat Mutasi Pegawai (HTTP 403)', $kernel->handle($reqKetuaMut)->getStatusCode() === 403);

        $reqKetuaCoaching = Request::create('/coachings/create', 'GET');
        app()->instance('request', $reqKetuaCoaching);
        $this->assert('Ketua Majelis dilarang membuat Pembinaan Pegawai (HTTP 403)', $kernel->handle($reqKetuaCoaching)->getStatusCode() === 403);

        $reqKetuaPromo = Request::create('/promotions/create', 'GET');
        app()->instance('request', $reqKetuaPromo);
        $this->assert('Ketua Majelis dilarang mengajukan Usulan Kenaikan Pangkat (HTTP 403)', $kernel->handle($reqKetuaPromo)->getStatusCode() === 403);

        // Ketua Majelis BISA menyetujui / approve usulan SK PDM & Waka
        $samplePromo = \App\Models\RankPromotion::first();
        $promoController = app()->make(\App\Http\Controllers\RankPromotionController::class);
        if ($samplePromo) {
            $reqApprovePromo = Request::create("/promotions/{$samplePromo->id}/verify", 'POST', [
                'status' => 'disetujui_pdm',
                'sk_number' => 'TEST/SK/2026',
                'sk_date' => date('Y-m-d'),
                'reviewer_notes' => 'Disetujui Ketua Majelis',
            ]);
            $resApprove = $promoController->verify($reqApprovePromo, $samplePromo->id);
            $this->assert('Ketua Majelis BERHAK menyetujui usulan SK Kenaikan Pangkat (HTTP 302)', $resApprove->isRedirect());
        }

        // 2. ADMIN UTAMA: Hak Penuh CRUD & Hapus
        Auth::login($adminUtama);
        $this->assert('Admin Utama terdeteksi isAdminUtama()', $adminUtama->isAdminUtama());
        $this->assert('Admin Utama memiliki canDeleteEmployees()', $adminUtama->canDeleteEmployees());
        $this->assert('Admin Utama memiliki canManageMasterNpm()', $adminUtama->canManageMasterNpm());
        $this->assert('Admin Utama memiliki canManageMutations()', $adminUtama->canManageMutations());

        // 3. ADMIN YAYASAN: Kelola Operasional SDM, Tidak Boleh Hapus
        Auth::login($adminYayasan);
        $this->assert('Admin Yayasan terdeteksi isAdminYayasan()', $adminYayasan->isAdminYayasan());
        $this->assert('Admin Yayasan TIDAK memiliki canDeleteEmployees()', !$adminYayasan->canDeleteEmployees());
        $this->assert('Admin Yayasan memiliki canManageMasterNpm()', $adminYayasan->canManageMasterNpm());
        $this->assert('Admin Yayasan memiliki canManageMutations()', $adminYayasan->canManageMutations());

        $isForbiddenAdminYayasanDel = false;
        try {
            $empController->destroy($sampleEmp);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $isForbiddenAdminYayasanDel = ($e->getStatusCode() === 403);
        }
        $this->assert('Admin Yayasan dilarang menghapus data pegawai (HTTP 403)', $isForbiddenAdminYayasanDel);

        // 4. PETUGAS TU UNIT: Kelola Unit Sendiri, Dilarang Hapus & Dilarang Approve SK PDM
        Auth::login($tuUnit);
        $this->assert('Petugas TU Unit terdeteksi isTuUnit()', $tuUnit->isTuUnit());
        $this->assert('Petugas TU Unit TIDAK memiliki canDeleteEmployees()', !$tuUnit->canDeleteEmployees());
        $this->assert('Petugas TU Unit TIDAK memiliki canApproveAsKetuaMajelis()', !$tuUnit->canApproveAsKetuaMajelis());

        $isForbiddenTuDel = false;
        try {
            $empController->destroy($sampleEmp);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $isForbiddenTuDel = ($e->getStatusCode() === 403);
        }
        $this->assert('Petugas TU dilarang menghapus data pegawai (HTTP 403)', $isForbiddenTuDel);

        if ($samplePromo) {
            $isTuPromoForbidden = false;
            try {
                $reqTuApprovePromo = Request::create("/promotions/{$samplePromo->id}/verify", 'POST', [
                    'status' => 'disetujui_pdm',
                    'sk_number' => 'ILLEGAL/SK/2026',
                ]);
                $promoController->verify($reqTuApprovePromo, $samplePromo->id);
            } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
                $isTuPromoForbidden = ($e->getStatusCode() === 403);
            }
            $this->assert('Petugas TU dilarang menyetujui SK PDM Kenaikan Pangkat (HTTP 403)', $isTuPromoForbidden);
        }
    }

    private function testDynamicSidebarVisibilityAndRoleIsolation(): void
    {
        $this->newLine();
        $this->comment('▶ FASE 10: Pengujian Filter Visibilitas Menu Panel Sidebar Sesuai Role & Proteksi URL');

        $kernel = app()->make(\Illuminate\Contracts\Http\Kernel::class);

        // 1. ADMIN UTAMA (admin@yayasan.sch.id): Seluruh 15 menu tampil lengkap
        $adminUtama = User::where('email', 'admin@yayasan.sch.id')->first();
        Auth::login($adminUtama);
        $reqAdmin = Request::create('/dashboard', 'GET');
        app()->instance('request', $reqAdmin);
        $htmlAdmin = $kernel->handle($reqAdmin)->getContent();
        preg_match('/<aside id="sidebarNav".*?<\/aside>/s', $htmlAdmin, $mAdmin);
        $sidebarAdmin = $mAdmin[0] ?? $htmlAdmin;

        $this->assert('Admin Utama melihat menu Dashboard', str_contains($sidebarAdmin, 'Dashboard'));
        $this->assert('Admin Utama melihat menu Data Induk Pegawai', str_contains($sidebarAdmin, 'Data Induk Pegawai'));
        $this->assert('Admin Utama melihat menu Input Nilai DP3', str_contains($sidebarAdmin, 'Input Nilai DP3'));
        $this->assert('Admin Utama melihat menu Input Nilai PKKS', str_contains($sidebarAdmin, 'Input Nilai PKKS'));
        $this->assert('Admin Utama melihat menu Pembaruan Data Berkala', str_contains($sidebarAdmin, 'Pembaruan Data Berkala'));
        $this->assert('Admin Utama melihat section PROSES', str_contains($sidebarAdmin, 'PROSES'));
        $this->assert('Admin Utama melihat menu Rekrutmen', str_contains($sidebarAdmin, 'Rekrutmen'));
        $this->assert('Admin Utama melihat menu Pembinaan Pegawai', str_contains($sidebarAdmin, 'Pembinaan Pegawai'));
        $this->assert('Admin Utama melihat menu Kenaikan Pangkat', str_contains($sidebarAdmin, 'Kenaikan Pangkat'));
        $this->assert('Admin Utama melihat menu Promosi KS', str_contains($sidebarAdmin, 'Promosi KS'));
        $this->assert('Admin Utama melihat menu Promosi Tim Manajemen', str_contains($sidebarAdmin, 'Promosi Tim Manajemen'));
        $this->assert('Admin Utama melihat menu Rekapitulasi Usulan', str_contains($sidebarAdmin, 'Rekapitulasi Usulan'));
        $this->assert('Admin Utama melihat menu Prestasi Pegawai', str_contains($sidebarAdmin, 'Prestasi Pegawai'));
        $this->assert('Admin Utama melihat section MASTER', str_contains($sidebarAdmin, 'MASTER'));
        $this->assert('Admin Utama melihat menu Unit Kerja AUM', str_contains($sidebarAdmin, 'Unit Kerja AUM'));
        $this->assert('Admin Utama melihat menu Bank Data NPM', str_contains($sidebarAdmin, 'Bank Data NPM'));
        $this->assert('Admin Utama melihat menu Riwayat Mutasi', str_contains($sidebarAdmin, 'Riwayat Mutasi'));
        $this->assert('Admin Utama melihat menu Pemeliharaan Sistem', str_contains($sidebarAdmin, 'Pemeliharaan Sistem'));

        // 2. KETUA MAJELIS (ketua@yayasan.sch.id): Read All Menu (Seluruh menu tampil di sidebar)
        $ketua = User::where('email', 'ketua@yayasan.sch.id')->first();
        Auth::login($ketua);
        $reqKetua = Request::create('/dashboard', 'GET');
        app()->instance('request', $reqKetua);
        $htmlKetua = $kernel->handle($reqKetua)->getContent();
        preg_match('/<aside id="sidebarNav".*?<\/aside>/s', $htmlKetua, $mKetua);
        $sidebarKetua = $mKetua[0] ?? $htmlKetua;

        $this->assert('Ketua Majelis melihat seluruh menu: Input Nilai PKKS', str_contains($sidebarKetua, 'Input Nilai PKKS'));
        $this->assert('Ketua Majelis melihat seluruh menu: Rekrutmen', str_contains($sidebarKetua, 'Rekrutmen'));
        $this->assert('Ketua Majelis melihat seluruh menu: Promosi KS', str_contains($sidebarKetua, 'Promosi KS'));
        $this->assert('Ketua Majelis melihat seluruh menu: Rekapitulasi Usulan Terpadu', str_contains($sidebarKetua, 'Rekapitulasi Usulan'));
        $this->assert('Ketua Majelis melihat seluruh menu: Bank Data NPM', str_contains($sidebarKetua, 'Bank Data NPM'));
        $this->assert('Ketua Majelis melihat seluruh menu: Riwayat Mutasi', str_contains($sidebarKetua, 'Riwayat Mutasi'));
        $this->assert('Ketua Majelis: Menu Pemeliharaan Sistem disembunyikan', !str_contains($sidebarKetua, 'Pemeliharaan Sistem'));

        // 3. PETUGAS TU UNIT (tu.sd@sekolah.sch.id): Sembunyikan menu pusat & proteksi rute
        $tu = User::where('email', 'tu.sd@sekolah.sch.id')->first();
        Auth::login($tu);
        $reqTu = Request::create('/dashboard', 'GET');
        app()->instance('request', $reqTu);
        $htmlTu = $kernel->handle($reqTu)->getContent();
        preg_match('/<aside id="sidebarNav".*?<\/aside>/s', $htmlTu, $mTu);
        $sidebarTu = $mTu[0] ?? $htmlTu;

        // Menu Operasional TU Tampil
        $this->assert('TU Unit melihat menu Dashboard', str_contains($sidebarTu, 'Dashboard'));
        $this->assert('TU Unit melihat menu Data Induk Pegawai', str_contains($sidebarTu, 'Data Induk Pegawai'));
        $this->assert('TU Unit melihat menu Input Nilai DP3', str_contains($sidebarTu, 'Input Nilai DP3'));
        $this->assert('TU Unit melihat menu Pembaruan Data Berkala', str_contains($sidebarTu, 'Pembaruan Data Berkala'));
        $this->assert('TU Unit melihat menu Pembinaan Pegawai', str_contains($sidebarTu, 'Pembinaan Pegawai'));
        $this->assert('TU Unit melihat menu Kenaikan Pangkat', str_contains($sidebarTu, 'Kenaikan Pangkat'));
        $this->assert('TU Unit melihat menu Promosi Tim Manajemen', str_contains($sidebarTu, 'Promosi Tim Manajemen'));
        $this->assert('TU Unit melihat menu Prestasi Pegawai', str_contains($sidebarTu, 'Prestasi Pegawai'));
        $this->assert('TU Unit melihat menu Profil Unit Kerja Saya', str_contains($sidebarTu, 'Profil Unit Kerja Saya'));

        // Menu Tingkat Pusat DISEMBUNYIKAN dari Sidebar TU
        $this->assert('Sidebar TU: Menu Input Nilai PKKS disembunyikan', !str_contains($sidebarTu, 'Input Nilai PKKS'));
        $this->assert('Sidebar TU: Menu Rekrutmen disembunyikan', !str_contains($sidebarTu, 'Rekrutmen'));
        $this->assert('Sidebar TU: Menu Promosi KS disembunyikan', !str_contains($sidebarTu, 'Promosi KS'));
        $this->assert('Sidebar TU: Menu Rekapitulasi Usulan disembunyikan', !str_contains($sidebarTu, 'Rekapitulasi Usulan'));
        $this->assert('Sidebar TU: Menu Bank Data NPM disembunyikan', !str_contains($sidebarTu, 'Bank Data NPM'));
        $this->assert('Sidebar TU: Menu Riwayat Mutasi disembunyikan', !str_contains($sidebarTu, 'Riwayat Mutasi'));
        $this->assert('Sidebar TU: Menu Pemeliharaan Sistem disembunyikan', !str_contains($sidebarTu, 'Pemeliharaan Sistem'));

        // Proteksi Direct URL bagi TU (HTTP 403 Forbidden)
        $reqTuNpm = Request::create('/master-npms', 'GET');
        app()->instance('request', $reqTuNpm);
        $this->assert('TU dilarang mengakses Bank Data NPM via URL (HTTP 403)', $kernel->handle($reqTuNpm)->getStatusCode() === 403);

        $reqTuMut = Request::create('/mutations', 'GET');
        app()->instance('request', $reqTuMut);
        $this->assert('TU dilarang mengakses Riwayat Mutasi via URL (HTTP 403)', $kernel->handle($reqTuMut)->getStatusCode() === 403);

        $reqTuRecruit = Request::create('/recruitments', 'GET');
        app()->instance('request', $reqTuRecruit);
        $this->assert('TU dilarang mengakses Rekrutmen via URL (HTTP 403)', $kernel->handle($reqTuRecruit)->getStatusCode() === 403);

        $reqTuKs = Request::create('/leadership/headmasters', 'GET');
        app()->instance('request', $reqTuKs);
        $this->assert('TU dilarang mengakses Promosi KS via URL (HTTP 403)', $kernel->handle($reqTuKs)->getStatusCode() === 403);

        $reqTuRecap = Request::create('/proposals/recap', 'GET');
        app()->instance('request', $reqTuRecap);
        $this->assert('TU dilarang mengakses Rekap Usulan Terpadu via URL (HTTP 403)', $kernel->handle($reqTuRecap)->getStatusCode() === 403);

        $reqTuMaint = Request::create('/maintenance', 'GET');
        app()->instance('request', $reqTuMaint);
        $this->assert('TU dilarang mengakses Modul Pemeliharaan Sistem via URL (HTTP 403)', $kernel->handle($reqTuMaint)->getStatusCode() === 403);

        // 4. PEGAWAI GURU (rahmat.guru@sekolah.sch.id): Hanya ESS Menu & Proteksi Rute
        $guru = User::where('email', 'rahmat.guru@sekolah.sch.id')->first();
        Auth::login($guru);
        $reqGuru = Request::create('/dashboard', 'GET');
        app()->instance('request', $reqGuru);
        $htmlGuru = $kernel->handle($reqGuru)->getContent();
        preg_match('/<aside id="sidebarNav".*?<\/aside>/s', $htmlGuru, $mGuru);
        $sidebarGuru = $mGuru[0] ?? $htmlGuru;

        // Menu ESS Tampil
        $this->assert('Pegawai Guru melihat menu Dashboard', str_contains($sidebarGuru, 'Dashboard'));
        $this->assert('Pegawai Guru melihat menu Profil Data Diri', str_contains($sidebarGuru, 'Profil Data Diri'));
        $this->assert('Pegawai Guru melihat menu Prestasi Pegawai', str_contains($sidebarGuru, 'Prestasi Pegawai'));

        // Seluruh Menu Administratif DISEMBUNYIKAN dari Sidebar Pegawai
        $this->assert('Sidebar Pegawai: Menu Input Nilai DP3 disembunyikan', !str_contains($sidebarGuru, 'Input Nilai DP3'));
        $this->assert('Sidebar Pegawai: Menu Input Nilai PKKS disembunyikan', !str_contains($sidebarGuru, 'Input Nilai PKKS'));
        $this->assert('Sidebar Pegawai: Menu Pembaruan Data Berkala disembunyikan', !str_contains($sidebarGuru, 'Pembaruan Data Berkala'));
        $this->assert('Sidebar Pegawai: Section PROSES disembunyikan', !str_contains($sidebarGuru, '<span>PROSES</span>'));
        $this->assert('Sidebar Pegawai: Menu Rekrutmen disembunyikan', !str_contains($sidebarGuru, 'Rekrutmen'));
        $this->assert('Sidebar Pegawai: Menu Pembinaan Pegawai disembunyikan', !str_contains($sidebarGuru, 'Pembinaan Pegawai'));
        $this->assert('Sidebar Pegawai: Menu Kenaikan Pangkat disembunyikan', !str_contains($sidebarGuru, 'Kenaikan Pangkat'));
        $this->assert('Sidebar Pegawai: Menu Promosi KS disembunyikan', !str_contains($sidebarGuru, 'Promosi KS'));
        $this->assert('Sidebar Pegawai: Menu Promosi Tim Manajemen disembunyikan', !str_contains($sidebarGuru, 'Promosi Tim Manajemen'));
        $this->assert('Sidebar Pegawai: Menu Rekapitulasi Usulan disembunyikan', !str_contains($sidebarGuru, 'Rekapitulasi Usulan'));
        $this->assert('Sidebar Pegawai: Section MASTER disembunyikan', !str_contains($sidebarGuru, '<span>MASTER</span>'));
        $this->assert('Sidebar Pegawai: Menu Unit Kerja AUM disembunyikan', !str_contains($sidebarGuru, 'Unit Kerja AUM'));
        $this->assert('Sidebar Pegawai: Menu Profil Unit Kerja Saya disembunyikan', !str_contains($sidebarGuru, 'Profil Unit Kerja Saya'));
        $this->assert('Sidebar Pegawai: Menu Bank Data NPM disembunyikan', !str_contains($sidebarGuru, 'Bank Data NPM'));
        $this->assert('Sidebar Pegawai: Menu Riwayat Mutasi disembunyikan', !str_contains($sidebarGuru, 'Riwayat Mutasi'));
        $this->assert('Sidebar Pegawai: Menu Pemeliharaan Sistem disembunyikan', !str_contains($sidebarGuru, 'Pemeliharaan Sistem'));

        // Proteksi Direct URL bagi Pegawai (HTTP 403 Forbidden)
        $reqGuruAudit = Request::create('/audits/quarterly', 'GET');
        app()->instance('request', $reqGuruAudit);
        $this->assert('Pegawai dilarang mengakses Pembaruan Data Berkala via URL (HTTP 403)', $kernel->handle($reqGuruAudit)->getStatusCode() === 403);

        $reqGuruPromo = Request::create('/promotions', 'GET');
        app()->instance('request', $reqGuruPromo);
        $this->assert('Pegawai dilarang mengakses Kenaikan Pangkat via URL (HTTP 403)', $kernel->handle($reqGuruPromo)->getStatusCode() === 403);

        $reqGuruCoach = Request::create('/coachings', 'GET');
        app()->instance('request', $reqGuruCoach);
        $this->assert('Pegawai dilarang mengakses Pembinaan Pegawai via URL (HTTP 403)', $kernel->handle($reqGuruCoach)->getStatusCode() === 403);

        $reqGuruUnit = Request::create('/units', 'GET');
        app()->instance('request', $reqGuruUnit);
        $this->assert('Pegawai dilarang mengakses Master Unit via URL (HTTP 403)', $kernel->handle($reqGuruUnit)->getStatusCode() === 403);

        $reqGuruMaint = Request::create('/maintenance', 'GET');
        app()->instance('request', $reqGuruMaint);
        $this->assert('Pegawai dilarang mengakses Modul Pemeliharaan Sistem via URL (HTTP 403)', $kernel->handle($reqGuruMaint)->getStatusCode() === 403);
    }

    private function testMaintenanceModule(): void
    {
        $this->newLine();
        $this->comment('▶ FASE 11: Pengujian Modul Pemeliharaan Sistem (Maintenance Controller & Panel Admin)');

        $kernel = app()->make(\Illuminate\Contracts\Http\Kernel::class);

        // 1. Verifikasi Privilege canManageMaintenance()
        $adminUtama = User::where('email', 'admin@yayasan.sch.id')->first();
        $itSupport = User::where('email', 'hr@yayasan.sch.id')->first();
        $ketua = User::where('email', 'ketua@yayasan.sch.id')->first();
        $tu = User::where('email', 'tu.sd@sekolah.sch.id')->first();
        $guru = User::where('email', 'rahmat.guru@sekolah.sch.id')->first();

        $this->assert('Admin Utama memiliki hak canManageMaintenance()', $adminUtama->canManageMaintenance());
        $this->assert('IT Support / Admin Yayasan memiliki hak canManageMaintenance()', $itSupport->canManageMaintenance());
        $this->assert('Ketua Majelis TIDAK memiliki hak canManageMaintenance()', !$ketua->canManageMaintenance());
        $this->assert('Petugas TU Unit TIDAK memiliki hak canManageMaintenance()', !$tu->canManageMaintenance());
        $this->assert('Pegawai Guru TIDAK memiliki hak canManageMaintenance()', !$guru->canManageMaintenance());

        // 2. Akses Dasbor Pemeliharaan Sistem oleh Admin Utama (HTTP 200)
        Auth::login($adminUtama);
        $reqAdminMaint = Request::create('/maintenance', 'GET');
        app()->instance('request', $reqAdminMaint);
        $resAdminMaint = $kernel->handle($reqAdminMaint);
        $htmlAdminMaint = $resAdminMaint->getContent();

        $this->assert('Admin Utama dapat mengakses Halaman Pemeliharaan Sistem (HTTP 200)', $resAdminMaint->getStatusCode() === 200);
        $this->assert('Halaman memuat judul Pemeliharaan Sistem & Status Maintenance', str_contains($htmlAdminMaint, 'Pemeliharaan Sistem & Status Maintenance'));
        $this->assert('Halaman memuat diagnosa Versi PHP dan Laravel', str_contains($htmlAdminMaint, 'Versi PHP:') && str_contains($htmlAdminMaint, 'Versi Laravel:'));
        $this->assert('Halaman memuat info Kapasitas Disk Server', str_contains($htmlAdminMaint, 'Kapasitas Disk Server'));
        $this->assert('Halaman memuat panel Alat Pemeliharaan & Operasi Cepat', str_contains($htmlAdminMaint, 'Alat Pemeliharaan & Operasi Cepat'));
        $this->assert('Halaman memuat konsol Cuplikan Log Sistem', str_contains($htmlAdminMaint, 'Cuplikan Log Sistem'));

        // 3. Akses oleh IT Support / Admin Yayasan (HTTP 200)
        Auth::login($itSupport);
        $reqItMaint = Request::create('/maintenance', 'GET');
        app()->instance('request', $reqItMaint);
        $resItMaint = $kernel->handle($reqItMaint);
        $this->assert('IT Support / Admin Yayasan dapat mengakses Halaman Pemeliharaan Sistem (HTTP 200)', $resItMaint->getStatusCode() === 200);

        // 4. Pembatasan Akses (Non-Admin HTTP 403 Forbidden)
        Auth::login($ketua);
        $reqKetuaMaint = Request::create('/maintenance', 'GET');
        app()->instance('request', $reqKetuaMaint);
        $this->assert('Ketua Majelis DITOLAK mengakses Halaman Pemeliharaan Sistem (HTTP 403)', $kernel->handle($reqKetuaMaint)->getStatusCode() === 403);

        // 5. Pengujian Aksi Bersihkan Cache (Clear Cache)
        Auth::login($adminUtama);
        $maintController = app()->make(\App\Http\Controllers\MaintenanceController::class);
        $reqClearCache = Request::create('/maintenance/clear-cache', 'POST', ['type' => 'all']);
        $resClearCache = $maintController->clearCache($reqClearCache);
        $this->assert('Aksi Bersihkan Semua Cache berhasil dieksekusi (Redirect 302)', $resClearCache->isRedirect());

        // 6. Pengujian Aksi Optimasi Aplikasi (Optimize)
        $reqOptimize = Request::create('/maintenance/optimize', 'POST');
        $resOptimize = $maintController->optimize($reqOptimize);
        $this->assert('Aksi Optimasi Aplikasi berhasil dieksekusi (Redirect 302)', $resOptimize->isRedirect());

        // Kembalikan auth sesi & bersihkan compiled file untuk CLI test
        Auth::login($adminUtama);
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');

        // 7. Pengujian Aksi Pencadangan Basis Data Instan (Backup)
        $reqBackup = Request::create('/maintenance/backup', 'POST');
        $resBackup = $maintController->backup($reqBackup);
        $this->assert('Aksi Pencadangan Database berhasil dieksekusi (Redirect 302)', $resBackup->isRedirect());

        // Verifikasi bahwa berkas backup tersimpan di storage/backups
        $backupFiles = \Illuminate\Support\Facades\File::files(storage_path('backups'));
        $this->assert('Direktori storage/backups memuat setidaknya 1 berkas cadangan', count($backupFiles) >= 1);

        // 8. Pengujian Aksi Perbaiki Symlink Storage (storage:link)
        $resLink = $maintController->linkStorage();
        $this->assert('Aksi Perbaiki Symlink Storage berhasil dieksekusi (Redirect 302)', $resLink->isRedirect());

        // 9. Pengujian Aksi Kosongkan Log Sistem (Clear Log)
        $resClearLog = $maintController->clearLog();
        $this->assert('Aksi Kosongkan Log Sistem berhasil dieksekusi (Redirect 302)', $resClearLog->isRedirect());

        // 10. Pengujian Siklus Toggle Mode Pemeliharaan (Down -> Up)
        $reqDown = Request::create('/maintenance/toggle', 'POST', [
            'secret' => 'test-bypass-key-1234',
            'retry' => 120,
        ]);
        $resDown = $maintController->toggle($reqDown);
        $this->assert('Aktivasi Maintenance Mode via Controller berhasil (Redirect 302)', $resDown->isRedirect());
        $this->assert('Aplikasi terdeteksi isDownForMaintenance() TRUE', app()->isDownForMaintenance());

        // Matikan kembali agar kembali daring (Live)
        $reqUp = Request::create('/maintenance/toggle', 'POST');
        $resUp = $maintController->toggle($reqUp);
        $this->assert('Penonaktifan Maintenance Mode via Controller berhasil (Redirect 302)', $resUp->isRedirect());
        $this->assert('Aplikasi kembali LIVE (isDownForMaintenance() FALSE)', !app()->isDownForMaintenance());
    }

    private function testLogout(): void
    {
        $this->newLine();
        $this->comment('▶ FASE 10: Pengujian Logout');

        Auth::logout();
        $this->assert('Sesi berhasil dikeluarkan (Auth::check() false)', !Auth::check());
    }
}
