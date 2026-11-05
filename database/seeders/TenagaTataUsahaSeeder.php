<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenagaTataUsahaSeeder extends Seeder
{
    /**
     * Pastikan seluruh 76 unit kerja AUM memiliki akun Tenaga Tata Usaha (TU).
     * Format Akun Resmi:
     * - Name: TU [Nama Lembaga/Unit] (tanpa nama pribadi tokoh/artis)
     * - Username: NPSN Unit (atau PDMK26 untuk Kantor PDM)
     * - Password: NPSN Unit (atau PDMK26 untuk Kantor PDM)
     */
    public function run(): void
    {
        $units = Unit::all();

        foreach ($units as $unit) {
            $npsn = !empty($unit->npsn) ? trim($unit->npsn) : 'PDMK26';
            $tuName = "TU " . trim($unit->name);
            $codeSlug = strtolower(str_replace(['-', ' '], ['_', ''], $unit->code));
            $emailSlug = strtolower(str_replace(['-', ' '], ['.', ''], $unit->code));
            $defaultEmail = $unit->code === 'SD-27' ? 'tu.sd@sekolah.sch.id' : ("tu." . $emailSlug . "@sekolah.sch.id");

            // Cek apakah sudah ada akun TU untuk unit ini
            $user = User::where('unit_id', $unit->id)
                ->whereIn('role', [User::ROLE_TU, User::ROLE_TU_SEKOLAH])
                ->first();

            if ($user) {
                // Perbarui akun yang sudah ada ke standar resmi
                $user->update([
                    'name' => $tuName,
                    'username' => $npsn,
                    'password' => Hash::make($npsn),
                    'is_active' => true,
                ]);
            } else {
                // Buat akun baru jika belum terdaftar
                $user = User::firstOrCreate(
                    ['username' => $npsn],
                    [
                        'name' => $tuName,
                        'email' => $defaultEmail,
                        'password' => Hash::make($npsn),
                        'role' => User::ROLE_TU,
                        'unit_id' => $unit->id,
                        'is_active' => true,
                    ]
                );
            }

            // Hubungkan profil pegawai TU (PTY / Tendik) di unit bersangkutan
            $tuEmployee = Employee::where('unit_id', $unit->id)
                ->whereIn('employee_type', ['PTY', 'tendik'])
                ->first();

            if ($tuEmployee && !$tuEmployee->user_id) {
                $tuEmployee->update(['user_id' => $user->id]);
            }
        }
    }
}
