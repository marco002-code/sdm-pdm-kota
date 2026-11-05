<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Inisialisasi Seluruh Master Data Unit Kerja (76 Unit Kerja PDM Kota Yogyakarta)
        $this->call(UnitSeeder::class);

        $yayasan = Unit::where('code', 'YAYASAN')->first();
        $sd = Unit::where('code', 'SD-27')->first() ?? Unit::where('level', 'SD')->first(); // SD Muhammadiyah Sapen 1

        // 2. Akun Superadmin & Eksekutif Yayasan
        $admin = User::create([
            'name' => 'IT Support Yayasan',
            'email' => 'admin@yayasan.sch.id',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
            'unit_id' => null,
            'is_active' => true,
        ]);

        $ketua = User::create([
            'name' => 'Dr. H. Ahmad Dahlan, M.Pd.',
            'email' => 'ketua@yayasan.sch.id',
            'username' => 'ketuayayasan',
            'password' => Hash::make('password'),
            'role' => User::ROLE_YAYASAN,
            'unit_id' => null,
            'is_active' => true,
        ]);

        $hrPusat = User::create([
            'name' => 'Siti Nurhaliza, S.Psi.',
            'email' => 'hr@yayasan.sch.id',
            'username' => 'hrpusat',
            'password' => Hash::make('password'),
            'role' => User::ROLE_HR_PUSAT,
            'unit_id' => null,
            'is_active' => true,
        ]);

        // 3. Akun Unit (Kepsek & TU SD)
        $kepsekSD = User::create([
            'name' => 'Dra. Endang Lestari, M.Pd.',
            'email' => 'kepsek.sd@sekolah.sch.id',
            'username' => 'kepseksd',
            'password' => Hash::make('password'),
            'role' => User::ROLE_KEPSEK,
            'unit_id' => $sd->id,
            'is_active' => true,
        ]);

        $tuSD = User::create([
            'name' => 'Budi Setiawan, A.Md.',
            'email' => 'tu.sd@sekolah.sch.id',
            'username' => 'tusd',
            'password' => Hash::make('password'),
            'role' => User::ROLE_TU,
            'unit_id' => $sd->id,
            'is_active' => true,
        ]);

        // 4. Sample Data Pegawai (Guru, Tendik, Karyawan)
        $guruUser = User::create([
            'name' => 'Rahmat Hidayat, S.Pd.',
            'email' => 'rahmat.guru@sekolah.sch.id',
            'username' => 'rahmatguru',
            'password' => Hash::make('password'),
            'role' => User::ROLE_PEGAWAI,
            'unit_id' => $sd->id,
            'is_active' => true,
        ]);

        Employee::create([
            'user_id' => $guruUser->id,
            'unit_id' => $sd->id,
            'nik' => '3201011234560001',
            'nip' => 'GUR-SD-2021001',
            'nbm' => '1089234',
            'full_name' => 'Rahmat Hidayat',
            'title_suffix' => 'S.Pd.',
            'gender' => 'L',
            'birth_place' => 'Jakarta',
            'birth_date' => '1990-05-15',
            'religion' => 'Islam',
            'marital_status' => 'menikah',
            'address' => 'Jl. Dahlia No. 5, Kota',
            'phone_number' => '081234567890',
            'email' => 'rahmat.guru@sekolah.sch.id',
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_holder' => 'Rahmat Hidayat',
            'employee_type' => Employee::TYPE_GURU,
            'employment_status' => 'tetap',
            'position' => 'Guru Matematika',
            'join_date' => '2021-07-01',
            'status' => Employee::STATUS_ACTIVE,
            'basic_salary' => 4500000.00,
            'nuptk' => '1234567890123456',
            'teaching_subject' => 'Matematika',
            'is_certified_educator' => true,
            'certification_year' => 2022,
            'highest_education' => 'S1',
            'major' => 'Pendidikan Matematika',
            'university' => 'Universitas Muhammadiyah Prof. DR. HAMKA',
            'graduation_year' => 2012,
            'education_history' => [
                's3' => ['school' => null, 'major' => null],
                's2' => ['school' => null, 'major' => null],
                's1' => ['school' => 'Universitas Muhammadiyah Prof. DR. HAMKA', 'major' => 'Pendidikan Matematika'],
                'd3' => ['school' => null, 'major' => null],
                'sma' => 'SMA Muhammadiyah 1 Kota',
                'smp' => 'SMP Muhammadiyah 2 Kota',
                'sd' => 'SD Muhammadiyah 01 Kota',
            ],
            'work_experiences' => [
                'Guru Matematika SMP Muhammadiyah 2 (2015 - 2021)',
                'Tentor Olimpiade Sains Matematika Nasional (2013 - 2015)',
                'Staf Pengajar Matematika Privat (2012 - 2013)',
            ],
            'organizational_experiences' => [
                'Ketua Bidang Kaderisasi PC Pemuda Muhammadiyah (2020 - 2024)',
                'Sekretaris Pimpinan Ranting Muhammadiyah (2022 - Sekarang)',
                'Ketua Umum PC Ikatan Pelajar Muhammadiyah (2008 - 2010)',
            ],
            'annual_leave_quota' => 12,
            'used_leave_quota' => 2,
        ]);

        // Tendik (Staf TU)
        Employee::create([
            'user_id' => $tuSD->id,
            'unit_id' => $sd->id,
            'nik' => '3201011234560002',
            'nip' => 'TDK-SD-2020001',
            'nbm' => '1092841',
            'full_name' => 'Budi Setiawan',
            'title_suffix' => 'A.Md.',
            'gender' => 'L',
            'birth_place' => 'Bandung',
            'birth_date' => '1993-08-20',
            'religion' => 'Islam',
            'marital_status' => 'menikah',
            'address' => 'Jl. Kenanga No. 8, Kota',
            'phone_number' => '081298765432',
            'email' => 'tu.sd@sekolah.sch.id',
            'bank_name' => 'BRI',
            'bank_account_number' => '9876543210',
            'bank_account_holder' => 'Budi Setiawan',
            'employee_type' => Employee::TYPE_TENDIK,
            'employment_status' => 'tetap',
            'position' => 'Staf Tata Usaha',
            'join_date' => '2020-01-10',
            'status' => Employee::STATUS_ACTIVE,
            'basic_salary' => 3800000.00,
            'work_unit_division' => 'Tata Usaha',
            'work_shift' => 'normal',
            'highest_education' => 'D3',
            'major' => 'Administrasi Perkantoran',
            'university' => 'Politeknik Negeri',
            'graduation_year' => 2015,
            'education_history' => [
                's3' => ['school' => null, 'major' => null],
                's2' => ['school' => null, 'major' => null],
                's1' => ['school' => null, 'major' => null],
                'd3' => ['school' => 'Politeknik Negeri', 'major' => 'Administrasi Perkantoran'],
                'sma' => 'SMK Muhammadiyah 3 Kota',
                'smp' => 'SMP Negeri 4 Kota',
                'sd' => 'SD Negeri 02 Kota',
            ],
            'work_experiences' => [
                'Staf Administrasi Keuangan CV Mitra Mandiri (2016 - 2019)',
                'Customer Service Kantor Pos Cabang Kota (2015 - 2016)',
            ],
            'organizational_experiences' => [
                'Anggota Komando Kesiapsiagaan Angkatan Muda Muhammadiyah (KOKAM)',
                'Pengurus Bidang Informasi & Komunikasi PC Pemuda Muhammadiyah',
            ],
            'annual_leave_quota' => 12,
            'used_leave_quota' => 0,
        ]);

        // Karyawan Operasional (Keamanan)
        Employee::create([
            'user_id' => null,
            'unit_id' => $sd->id,
            'nik' => '3201011234560003',
            'nip' => 'KRY-SD-2022001',
            'nbm' => '1103921',
            'full_name' => 'Agus Santoso',
            'gender' => 'L',
            'birth_place' => 'Bogor',
            'birth_date' => '1988-12-01',
            'religion' => 'Islam',
            'marital_status' => 'menikah',
            'address' => 'Jl. Melati No. 15, Kota',
            'phone_number' => '081311223344',
            'bank_name' => 'Mandiri',
            'bank_account_number' => '5556667778',
            'bank_account_holder' => 'Agus Santoso',
            'employee_type' => Employee::TYPE_KARYAWAN,
            'employment_status' => 'kontrak',
            'position' => 'Petugas Keamanan (Satpam)',
            'join_date' => '2022-02-01',
            'status' => Employee::STATUS_ACTIVE,
            'basic_salary' => 3200000.00,
            'work_unit_division' => 'Keamanan & Sarpras',
            'work_shift' => 'pagi',
            'technical_certifications' => ['Gada Pratama', 'K3 Dasar'],
            'highest_education' => 'SMA/SMK',
            'graduation_year' => 2007,
            'education_history' => [
                's3' => ['school' => null, 'major' => null],
                's2' => ['school' => null, 'major' => null],
                's1' => ['school' => null, 'major' => null],
                'd3' => ['school' => null, 'major' => null],
                'sma' => 'SMK Negeri 1 Kota',
                'smp' => 'SMP Negeri 2 Kota',
                'sd' => 'SD Negeri 01 Kota',
            ],
            'work_experiences' => [
                'Security Officer PT Secure Parking (2018 - 2021)',
                'Petugas Keamanan Gudang Logistik (2010 - 2018)',
            ],
            'organizational_experiences' => [
                'Anggota KOKAM Cabang Kota',
                'Ketua RT 04 RW 02 Kelurahan Maju Jaya',
            ],
            'annual_leave_quota' => 12,
            'used_leave_quota' => 1,
        ]);

        // 5. Demo Seed Pegawai ke seluruh 76 Unit Kerja AUM (PDM, GTY, GTT, PTY, PTT, LAINNYA)
        $this->call(EmployeeDemoSeeder::class);

        // 6. Seed akun Tenaga Tata Usaha (TU) untuk seluruh unit kerja AUM
        $this->call(TenagaTataUsahaSeeder::class);
    }
}
