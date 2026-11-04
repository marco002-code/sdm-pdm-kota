<?php

namespace App\Http\Controllers;

use App\Models\Recruitment;
use App\Models\RecruitmentApplicant;
use App\Models\Unit;
use Illuminate\Http\Request;

class RecruitmentController extends Controller
{
    public function index(Request $request)
    {
        if (! auth()->user()->isYayasanLevel()) {
            abort(403, 'Akses ditolak: Modul Rekrutmen Pegawai hanya dapat diakses oleh Admin dan Pengurus Yayasan/Majelis.');
        }

        $query = Recruitment::withCount(['applicants', 'applicants as passed_count' => function ($q) {
            $q->where('selection_status', 'lulus');
        }]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        $recruitments = $query->orderBy('created_at', 'desc')->paginate(10);
        $totalApplicants = RecruitmentApplicant::count();
        $totalPassed = RecruitmentApplicant::where('selection_status', 'lulus')->count();
        $totalActive = Recruitment::whereIn('status', ['buka', 'seleksi_berkas', 'ujian_penjajagan', 'wawancara'])->count();

        return view('recruitments.index', compact(
            'recruitments',
            'totalApplicants',
            'totalPassed',
            'totalActive'
        ));
    }

    public function show($id, Request $request)
    {
        if (! auth()->user()->isYayasanLevel()) {
            abort(403, 'Akses ditolak: Modul Rekrutmen Pegawai hanya dapat diakses oleh Admin dan Pengurus Yayasan/Majelis.');
        }

        $recruitment = Recruitment::with(['creator'])->findOrFail($id);

        $applicantQuery = $recruitment->applicants()->with(['preferredUnit', 'placedUnit']);

        if ($request->filled('status')) {
            $applicantQuery->where('selection_status', $request->status);
        }

        if ($request->filled('position')) {
            $applicantQuery->where('applied_position', 'like', '%' . $request->position . '%');
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $applicantQuery->where(function ($q) use ($s) {
                $q->where('full_name', 'like', "%$s%")
                  ->orWhere('registration_number', 'like', "%$s%")
                  ->orWhere('nik', 'like', "%$s%");
            });
        }

        $applicants = $applicantQuery->orderByRaw("
            CASE 
                WHEN selection_status = 'lulus' THEN 1
                WHEN selection_status = 'cadangan' THEN 2
                WHEN selection_status = 'wawancara_microteaching' THEN 3
                WHEN selection_status = 'ujian_penjajagan' THEN 4
                WHEN selection_status = 'seleksi_administrasi' THEN 5
                ELSE 6
            END, final_score DESC
        ")->paginate(15);

        $units = Unit::active()->orderBy('name')->get();

        return view('recruitments.show', compact('recruitment', 'applicants', 'units'));
    }
}
