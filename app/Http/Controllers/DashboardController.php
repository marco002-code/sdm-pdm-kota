<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    /**
     * Menampilkan Dasbor Kepegawaian & AUM Yayasan / Unit.
     */
    public function index()
    {
        $user = auth()->user();
        $cacheKey = 'dashboard_data_v3_' . ($user ? $user->id : 'guest');

        $data = Cache::remember($cacheKey, 600, function () use ($user) {
            $query = Employee::query();

            // Isolasi data untuk level unit (Kepsek / TU)
            if ($user && in_array($user->role, ['kepsek', 'kepala_sekolah', 'tu', 'tu_sekolah'])) {
                $query->where('unit_id', $user->unit_id);
            }

            $rawEmp = (clone $query)->selectRaw('employee_type, count(*) as count')
                ->groupBy('employee_type')
                ->pluck('count', 'employee_type')
                ->toArray();

            $categories = Employee::getCategories();

            $categoryLabels = [];
            $categoryCounts = [];
            $categoryBreakdown = [];
            $totalEmployees = 0;

            foreach ($categories as $type => $label) {
                $count = $rawEmp[$type] ?? 0;
                if ($type === 'GTY' && isset($rawEmp['guru'])) $count += $rawEmp['guru'];
                if ($type === 'PTY' && isset($rawEmp['tendik'])) $count += $rawEmp['tendik'];
                if ($type === 'PTT' && isset($rawEmp['karyawan'])) $count += $rawEmp['karyawan'];

                $categoryLabels[] = $label;
                $categoryCounts[] = $count;
                $categoryBreakdown[$type] = [
                    'type'  => $type,
                    'label' => $label,
                    'count' => $count,
                ];
                $totalEmployees += $count;
            }

            $totalUnits = Unit::count();

            // Distribusi Jenis AUM
            $rawAum = Unit::selectRaw('level, count(*) as count')
                ->groupBy('level')
                ->pluck('count', 'level')
                ->toArray();

            $order = [
                'TK' => 'TK / ABA (Taman Kanak-Kanak)',
                'SD' => 'SD / MI (Sekolah Dasar)',
                'SMP' => 'SMP (Sekolah Menengah Pertama)',
                'MTS' => 'MTs (Madrasah Tsanawiyah)',
                'SMA' => 'SMA (Sekolah Menengah Atas)',
                'SMK' => 'SMK (Sekolah Menengah Kejuruan)',
                'PKBM' => 'PKBM (Pusat Kegiatan Belajar)',
                'TPA' => 'TPA (Taman Pengasuhan Anak)',
                'MA' => 'MA (Madrasah Aliyah)',
                'YAYASAN' => 'Kantor PDM Kota Yogyakarta',
            ];

            $aumLabels = [];
            $aumCounts = [];
            $aumBreakdown = [];

            foreach ($order as $key => $label) {
                $count = $rawAum[$key] ?? 0;
                $aumLabels[] = $label;
                $aumCounts[] = $count;
                $aumBreakdown[$key] = [
                    'label' => $label,
                    'count' => $count,
                ];
            }

            return [
                'totalEmployees'    => $totalEmployees,
                'totalUnits'        => $totalUnits,
                'categoryLabels'    => $categoryLabels,
                'categoryCounts'    => $categoryCounts,
                'categoryBreakdown' => $categoryBreakdown,
                'aumLabels'         => $aumLabels,
                'aumCounts'         => $aumCounts,
                'aumBreakdown'      => $aumBreakdown,
            ];
        });

        return view('dashboard.index', $data);
    }
}
