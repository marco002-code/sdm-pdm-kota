<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EmployeeDemoSeeder extends Seeder
{
    /**
     * Seed data demo pegawai untuk seluruh unit kerja AUM di lingkungan PDM Kota Yogyakarta.
     */
    public function run(): void
    {
        // 1. Update data lama jika masih memakai format legacy
        Employee::where('employee_type', 'guru')->update(['employee_type' => 'GTY']);
        Employee::where('employee_type', 'tendik')->update(['employee_type' => 'PTY']);
        Employee::where('employee_type', 'karyawan')->update(['employee_type' => 'PTT']);

        $firstNames = [
            'Ahmad', 'Muhammad', 'Agus', 'Budi', 'Cahyono', 'Dedi', 'Eko', 'Fajar', 'Gunawan', 'Hadi',
            'Irfan', 'Joko', 'Kurniawan', 'Lukman', 'Mansyur', 'Nur', 'Oki', 'Pramono', 'Rahmat', 'Surya',
            'Tri', 'Utomo', 'Wahyu', 'Yusuf', 'Zainal', 'Siti', 'Nurul', 'Dewi', 'Endang', 'Fitri',
            'Hani', 'Indah', 'Kartika', 'Lestari', 'Mardhiah', 'Novita', 'Putri', 'Ratna', 'Sri', 'Triana',
            'Umi', 'Wulandari', 'Yuni', 'Zahra', 'Aisyah', 'Fatimah', 'Khadijah', 'Maryam', 'Salma', 'Safitri'
        ];

        $lastNames = [
            'Pratama', 'Saputra', 'Hidayat', 'Kusuma', 'Santoso', 'Wibowo', 'Nugroho', 'Wijaya', 'Setiawan', 'Hermawan',
            'Purnomo', 'Cahyadi', 'Irawan', 'Firmansyah', 'Budiman', 'Mahendra', 'Suryanto', 'Wahyudi', 'Kurnia', 'Subekti',
            'Rahayu', 'Handayani', 'Susanti', 'Wulandari', 'Anggraini', 'Pertiwi', 'Utami', 'Lestari', 'Kusumawati', 'Sulistyo'
        ];

        $units = Unit::all();

        $nikCounter = 3471010000000000;
        $nbmCounter = 1100000;

        foreach ($units as $unit) {
            // Cek jika unit sudah memiliki cukup pegawai demo selain yang di-seed manual
            $currentCount = Employee::where('unit_id', $unit->id)->count();

            if ($unit->code === 'YAYASAN') {
                // Seed untuk Kantor PDM Kota Yogyakarta (Majelis)
                $pdmProfiles = [
                    [
                        'name' => 'Drs. H. Akhid Widi Rahmanto',
                        'prefix' => 'Drs. H.',
                        'suffix' => '',
                        'gender' => 'L',
                        'type' => 'PDM',
                        'pos' => 'Ketua Majelis Dikdasmen & PNF PDM',
                        'salary' => 6000000,
                        'edu' => 'S1',
                        'major' => 'Pendidikan Agama Islam',
                    ],
                    [
                        'name' => 'Dr. H. Muhammad Joko Susilo, M.Pd.',
                        'prefix' => 'Dr. H.',
                        'suffix' => 'M.Pd.',
                        'gender' => 'L',
                        'type' => 'PDM',
                        'pos' => 'Wakil Ketua Bidang Kurikulum & SDM',
                        'salary' => 5500000,
                        'edu' => 'S3',
                        'major' => 'Manajemen Pendidikan',
                    ],
                    [
                        'name' => 'Drs. Sukirno, M.Pd.',
                        'prefix' => 'Drs.',
                        'suffix' => 'M.Pd.',
                        'gender' => 'L',
                        'type' => 'PDM',
                        'pos' => 'Sekretaris Majelis Dikdasmen & PNF',
                        'salary' => 5000000,
                        'edu' => 'S2',
                        'major' => 'Pendidikan Matematika',
                    ],
                    [
                        'name' => 'Hj. Siti Romlah, S.E.',
                        'prefix' => 'Hj.',
                        'suffix' => 'S.E.',
                        'gender' => 'P',
                        'type' => 'PTY',
                        'pos' => 'Kepala Sekretariat & Keuangan Kantor PDM',
                        'salary' => 4200000,
                        'edu' => 'S1',
                        'major' => 'Akuntansi',
                    ],
                    [
                        'name' => 'Danang Prasetyo, S.Kom.',
                        'prefix' => '',
                        'suffix' => 'S.Kom.',
                        'gender' => 'L',
                        'type' => 'PTT',
                        'pos' => 'Staf IT & Pengelola Data SIM PDM',
                        'salary' => 3200000,
                        'edu' => 'S1',
                        'major' => 'Teknik Informatika',
                    ],
                ];

                foreach ($pdmProfiles as $idx => $p) {
                    $nikCounter++;
                    $nbmCounter++;
                    Employee::firstOrCreate(
                        ['full_name' => $p['name'], 'unit_id' => $unit->id],
                        [
                            'nik' => (string) $nikCounter,
                            'nbm' => (string) $nbmCounter,
                            'nip' => 'PDM-' . str_pad($idx + 1, 3, '0', STR_PAD_LEFT),
                            'title_prefix' => $p['prefix'],
                            'title_suffix' => $p['suffix'],
                            'gender' => $p['gender'],
                            'birth_place' => 'Yogyakarta',
                            'birth_date' => '1975-05-12',
                            'religion' => 'Islam',
                            'marital_status' => 'menikah',
                            'address' => 'Jl. Sultan Agung No. 14, Pakualaman, Kota Yogyakarta',
                            'phone_number' => '0812' . rand(10000000, 99999999),
                            'email' => strtolower(Str::slug($p['name'])) . '@pdmjogja.org',
                            'employee_type' => $p['type'],
                            'employment_status' => $p['type'] === 'PTT' ? 'kontrak' : 'tetap',
                            'position' => $p['pos'],
                            'join_date' => '2018-01-01',
                            'status' => 'aktif',
                            'basic_salary' => $p['salary'],
                            'highest_education' => $p['edu'],
                            'major' => $p['major'],
                            'annual_leave_quota' => 12,
                            'used_leave_quota' => rand(0, 4),
                        ]
                    );
                }
                continue;
            }

            // Jika unit sekolah / madrasah / TPA / TK
            if ($currentCount < 3) {
                // Kategori yang di-seed untuk tiap unit:
                // 1. GTY (Guru Tetap Yayasan)
                // 2. GTT (Guru Tidak Tetap)
                // 3. PTY (Pegawai Tetap Yayasan)
                // 4. PTT (Pegawai Tidak Tetap)
                $rolesToSeed = ['GTY', 'GTT', 'PTY', 'PTT'];

                // Jika jenjang menengah/atas tambahkan juga 1 LAINNYA (Satpam / Teknisi)
                if (in_array($unit->level, ['SMP', 'SMA', 'SMK', 'SD'])) {
                    $rolesToSeed[] = 'LAINNYA';
                }

                foreach ($rolesToSeed as $type) {
                    $nikCounter++;
                    $nbmCounter++;

                    $fName = $firstNames[array_rand($firstNames)];
                    $lName = $lastNames[array_rand($lastNames)];
                    $fullName = "{$fName} {$lName}";
                    $isMale = rand(0, 1) === 1;
                    $gender = $isMale ? 'L' : 'P';

                    $pos = '';
                    $titlePrefix = '';
                    $titleSuffix = '';
                    $edu = 'S1';
                    $major = 'Pendidikan';
                    $salary = 3000000;

                    switch ($type) {
                        case 'GTY':
                            $pos = in_array($unit->level, ['TPA', 'TK']) ? 'Guru Kelompok / Wali Kelas' : 'Guru Mapel / Guru Kelas (GTY)';
                            $titleSuffix = 'S.Pd.';
                            $salary = rand(3800000, 4800000);
                            $major = 'Pendidikan ' . ($unit->level === 'TK' ? 'PAUD' : 'Bahasa Indonesia');
                            break;
                        case 'GTT':
                            $pos = 'Guru Mapel / Pendamping (GTT)';
                            $titleSuffix = 'S.Pd.';
                            $salary = rand(2500000, 3200000);
                            $major = 'Pendidikan ' . ($isMale ? 'Penjaskes' : 'Agama Islam');
                            break;
                        case 'PTY':
                            $pos = 'Kepala Tata Usaha / Bendahara';
                            $titleSuffix = 'S.E.';
                            $edu = 'S1';
                            $major = 'Manajemen / Akuntansi';
                            $salary = rand(3200000, 3800000);
                            break;
                        case 'PTT':
                            $pos = 'Staf Administrasi & Operator Dapodik';
                            $titleSuffix = 'A.Md.';
                            $edu = 'D3';
                            $major = 'Teknik Komputer / Administrasi Perkantoran';
                            $salary = rand(2200000, 2800000);
                            break;
                        case 'LAINNYA':
                            $pos = $isMale ? 'Petugas Keamanan (Satpam)' : 'Petugas Kebersihan & Rumah Tangga';
                            $edu = 'SMA/SMK';
                            $major = 'Umum';
                            $salary = rand(2100000, 2500000);
                            break;
                    }

                    Employee::create([
                        'unit_id' => $unit->id,
                        'nik' => (string) $nikCounter,
                        'nbm' => (string) $nbmCounter,
                        'nip' => $unit->code . '-' . rand(100, 999),
                        'full_name' => $fullName,
                        'title_prefix' => $titlePrefix,
                        'title_suffix' => $titleSuffix,
                        'gender' => $gender,
                        'birth_place' => 'Yogyakarta',
                        'birth_date' => date('Y-m-d', strtotime('-' . rand(24, 52) . ' years')),
                        'religion' => 'Islam',
                        'marital_status' => 'menikah',
                        'address' => ($unit->address ?: $unit->district) . ', Kota Yogyakarta',
                        'phone_number' => '08' . rand(11, 99) . rand(1000000, 9999999),
                        'email' => strtolower("{$fName}.{$lName}" . rand(10, 99)) . '@muhammadiyah.sch.id',
                        'employee_type' => $type,
                        'employment_status' => in_array($type, ['GTY', 'PTY']) ? 'gty' : (in_array($type, ['GTT', 'PTT']) ? 'gtt' : 'kontrak'),
                        'position' => $pos,
                        'join_date' => date('Y-m-d', strtotime('-' . rand(1, 10) . ' years')),
                        'status' => 'aktif',
                        'basic_salary' => $salary,
                        'is_certified_educator' => ($type === 'GTY'),
                        'certification_year' => ($type === 'GTY') ? 2020 : null,
                        'highest_education' => $edu,
                        'major' => $major,
                        'annual_leave_quota' => 12,
                        'used_leave_quota' => rand(0, 5),
                    ]);
                }
            }
        }
    }
}
