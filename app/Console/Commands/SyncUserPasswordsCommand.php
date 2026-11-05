<?php

namespace App\Console\Commands;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SyncUserPasswordsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:sync-passwords {--user= : Reset username atau email tertentu}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronkan username, kata sandi resmi (ADMN26, MAJL26, YAYS26, KPSK26, NPSN TU), dan aktifkan status akun pengguna di database server produksi';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $target = $this->option('user');

        $this->info('===========================================================');
        $this->info('🔑 SINKRONISASI KREDENSIAL PENGGUNA SIM SDM PDM KOTA YOGYAKARTA');
        $this->info('===========================================================');

        $sd = Unit::where('code', 'SD-27')->first() ?? Unit::where('level', 'SD')->first();

        // Daftar Akun Utama & Eksekutif Yayasan
        $coreAccounts = [
            [
                'name' => 'Admin Utama PDM',
                'username' => 'admin',
                'email' => 'admin@yayasan.sch.id',
                'password_plain' => 'ADMN26',
                'role' => User::ROLE_ADMIN,
                'unit_id' => null,
            ],
            [
                'name' => 'Ketua Majelis Dikdasmen PDM',
                'username' => 'ketuayayasan',
                'email' => 'ketua@yayasan.sch.id',
                'password_plain' => 'MAJL26',
                'role' => User::ROLE_YAYASAN,
                'unit_id' => null,
            ],
            [
                'name' => 'Admin Bidang SDM Dikdasmen',
                'username' => 'hrpusat',
                'email' => 'hr@yayasan.sch.id',
                'password_plain' => 'YAYS26',
                'role' => User::ROLE_HR_PUSAT,
                'unit_id' => null,
            ],
            [
                'name' => 'Kepala SD Muhammadiyah Sapen 1',
                'username' => 'kepseksd',
                'email' => 'kepsek.sd@sekolah.sch.id',
                'password_plain' => 'KPSK26',
                'role' => User::ROLE_KEPSEK,
                'unit_id' => $sd?->id,
            ],
            [
                'name' => 'Guru SD Muhammadiyah Sapen 1',
                'username' => 'rahmatguru',
                'email' => 'rahmat.guru@sekolah.sch.id',
                'password_plain' => 'GURU26',
                'role' => User::ROLE_PEGAWAI,
                'unit_id' => $sd?->id,
            ],
        ];

        $tableRows = [];

        foreach ($coreAccounts as $acc) {
            if ($target && !in_array($target, [$acc['username'], $acc['email']])) {
                continue;
            }

            // Cari user berdasarkan username atau email atau role
            $user = User::where('username', $acc['username'])
                ->orWhere('email', $acc['email'])
                ->orWhere(function ($q) use ($acc) {
                    if ($acc['role'] === User::ROLE_ADMIN) {
                        $q->where('role', User::ROLE_ADMIN);
                    }
                })
                ->first();

            if ($user) {
                $user->name = $acc['name'];
                $user->username = $acc['username'];
                $user->email = $acc['email'];
                $user->password = Hash::make($acc['password_plain']);
                $user->role = $acc['role'];
                $user->is_active = true;
                if (is_null($user->unit_id) && $acc['unit_id']) {
                    $user->unit_id = $acc['unit_id'];
                }
                $user->save();
                $statusText = 'DIPERBARUI';
            } else {
                $user = User::create([
                    'name' => $acc['name'],
                    'username' => $acc['username'],
                    'email' => $acc['email'],
                    'password' => Hash::make($acc['password_plain']),
                    'role' => $acc['role'],
                    'unit_id' => $acc['unit_id'],
                    'is_active' => true,
                ]);
                $statusText = 'DIBUAT BARU';
            }

            $tableRows[] = [
                $acc['role'],
                $acc['name'],
                $acc['username'],
                $acc['email'],
                $acc['password_plain'],
                $statusText,
            ];
        }

        // Sinkronisasi Akun TU Sekolah (Username = NPSN, Password = NPSN) jika tidak ada filter spesifik atau target adalah 'tu'
        if (!$target || $target === 'tu') {
            $units = Unit::all();
            $tuCount = 0;

            foreach ($units as $unit) {
                $npsn = !empty($unit->npsn) ? trim($unit->npsn) : 'PDMK26';
                $tuName = "TU " . trim($unit->name);
                $codeSlug = strtolower(str_replace(['-', ' '], ['.', ''], $unit->code));
                $defaultEmail = $unit->code === 'SD-27' ? 'tu.sd@sekolah.sch.id' : ("tu." . $codeSlug . "@sekolah.sch.id");

                $tuUser = User::where('unit_id', $unit->id)
                    ->whereIn('role', [User::ROLE_TU, User::ROLE_TU_SEKOLAH])
                    ->first();

                if ($tuUser) {
                    $tuUser->name = $tuName;
                    $tuUser->username = $npsn;
                    $tuUser->password = Hash::make($npsn);
                    $tuUser->is_active = true;
                    $tuUser->save();
                } else {
                    $tuUser = User::where('username', $npsn)->first();
                    if ($tuUser) {
                        $tuUser->name = $tuName;
                        $tuUser->password = Hash::make($npsn);
                        $tuUser->unit_id = $unit->id;
                        $tuUser->role = User::ROLE_TU;
                        $tuUser->is_active = true;
                        $tuUser->save();
                    } else {
                        User::create([
                            'name' => $tuName,
                            'username' => $npsn,
                            'email' => $defaultEmail,
                            'password' => Hash::make($npsn),
                            'role' => User::ROLE_TU,
                            'unit_id' => $unit->id,
                            'is_active' => true,
                        ]);
                    }
                }
                $tuCount++;
            }
            $this->info("✔ Berhasil menyelaraskan {$tuCount} akun Petugas TU Unit (Username = NPSN & Password = NPSN)");
        }

        $this->table(
            ['Role', 'Nama Akun', 'Username', 'Email', 'Password', 'Status Database'],
            $tableRows
        );

        $this->info('===========================================================');
        $this->info('✅ SELURUH KREDENSIAL BERHASIL DISINKRONKAN DAN DIAKTIFKAN!');
        $this->info('===========================================================');

        return Command::SUCCESS;
    }
}
