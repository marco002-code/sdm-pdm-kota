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
     */
    public function run(): void
    {
        $units = Unit::all();

        foreach ($units as $unit) {
            // Cek apakah sudah ada akun TU untuk unit ini
            $existingTu = User::where('unit_id', $unit->id)
                ->whereIn('role', [User::ROLE_TU, User::ROLE_TU_SEKOLAH])
                ->first();

            if ($existingTu) {
                // Pastikan terhubung dengan profil pegawai TU (PTY) jika belum
                $tuEmployee = Employee::where('unit_id', $unit->id)
                    ->whereIn('employee_type', ['PTY', 'tendik'])
                    ->first();

                if ($tuEmployee && !$tuEmployee->user_id) {
                    $tuEmployee->update(['user_id' => $existingTu->id]);
                }
                continue;
            }

            // Cari profil pegawai TU (PTY / Tendik) di unit bersangkutan
            $tuEmployee = Employee::where('unit_id', $unit->id)
                ->whereIn('employee_type', ['PTY', 'tendik'])
                ->first();

            $codeSlug = strtolower(str_replace('-', '_', $unit->code));
            $emailSlug = strtolower(str_replace('-', '.', $unit->code));

            $name = $tuEmployee ? $tuEmployee->full_name : "Tata Usaha " . $unit->name;
            $username = "tu_" . $codeSlug;
            $email = "tu." . $emailSlug . "@sekolah.sch.id";

            // Pastikan username & email unik
            $user = User::firstOrCreate(
                ['username' => $username],
                [
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'role' => User::ROLE_TU,
                    'unit_id' => $unit->id,
                    'is_active' => true,
                ]
            );

            // Hubungkan employee ke user
            if ($tuEmployee && !$tuEmployee->user_id) {
                $tuEmployee->update(['user_id' => $user->id]);
            }
        }
    }
}
