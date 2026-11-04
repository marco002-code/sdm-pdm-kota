<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\RankPromotion;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RankPromotionController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = RankPromotion::with(['employee', 'unit', 'verifier']);

        // Isolasi Unit untuk TU / Kepsek
        if ($user && $user->isUnitLevel() && $user->unit_id) {
            $query->where('unit_id', $user->unit_id);
            $baseCountQuery = RankPromotion::where('unit_id', $user->unit_id);
            $units = Unit::where('id', $user->unit_id)->get();
        } else {
            $baseCountQuery = RankPromotion::query();
            $units = Unit::active()->orderBy('name')->get();

            if ($request->filled('unit_id')) {
                $query->where('unit_id', $request->unit_id);
            }
        }

        if ($request->filled('type')) {
            $query->where('promotion_type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('employee', function ($q) use ($s) {
                $q->where('full_name', 'like', "%$s%")
                  ->orWhere('npm', 'like', "%$s%")
                  ->orWhere('nbm', 'like', "%$s%");
            });
        }

        $promotions = $query->orderBy('created_at', 'desc')->paginate(12);

        $counts = [
            'total'      => (clone $baseCountQuery)->count(),
            'diajukan'   => (clone $baseCountQuery)->where('status', 'diajukan')->count(),
            'sidang'     => (clone $baseCountQuery)->where('status', 'sidang_majelis')->count(),
            'disetujui'  => (clone $baseCountQuery)->where('status', 'disetujui_pdm')->count(),
        ];

        return view('promotions.index', compact('promotions', 'counts', 'units'));
    }

    /**
     * Tampilkan formulir pengajuan usulan kenaikan pangkat / alih status (GTT->GTY, PTT->PTY, berkala)
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        if ($user->isPegawai()) {
            abort(403, 'Akses ditolak: Pengajuan usulan dilakukan oleh Tata Usaha atau Kepala Sekolah.');
        }

        if ($user->isKetuaMajelis()) {
            abort(403, 'Akses ditolak: Pengajuan usulan baru dilakukan oleh Petugas TU Unit atau Admin. Wewenang Ketua Majelis adalah memimpin sidang dan menyetujui usulan SK PDM.');
        }

        // Ambil daftar pegawai yang dapat diajukan
        if ($user->isUnitLevel() && $user->unit_id) {
            $employees = Employee::where('unit_id', $user->unit_id)
                ->where('is_active', true)
                ->orderBy('full_name')
                ->get();
            $units = Unit::where('id', $user->unit_id)->get();
        } else {
            $employees = Employee::where('is_active', true)
                ->orderBy('full_name')
                ->get();
            $units = Unit::active()->orderBy('name')->get();
        }

        $selectedEmployeeId = $request->query('employee_id');

        return view('promotions.create', compact('employees', 'units', 'selectedEmployeeId'));
    }

    /**
     * Simpan pengajuan usulan kenaikan pangkat / alih status baru
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        if ($user->isPegawai()) {
            abort(403, 'Akses ditolak.');
        }

        if ($user->isKetuaMajelis()) {
            abort(403, 'Akses ditolak: Pengajuan usulan baru dilakukan oleh Petugas TU Unit atau Admin.');
        }

        $validated = $request->validate([
            'employee_id'            => 'required|exists:employees,id',
            'promotion_type'         => 'required|in:reguler_berkala,penyesuaian_ijazah,alih_status_gty,alih_status_pty,istimewa',
            'current_rank'           => 'nullable|string|max:100',
            'proposed_rank'          => 'required|string|max:100',
            'current_employee_type'  => 'required|string|max:50',
            'proposed_employee_type' => 'required|string|max:50',
            'years_of_service'       => 'required|integer|min:0',
            'performance_score'      => 'nullable|numeric|between:0,100',
            'credit_points'          => 'nullable|numeric|min:0',
            'new_education_degree'   => 'nullable|string|max:100',
            'ortom_involvement'      => 'nullable|string',
            'reviewer_notes'         => 'nullable|string',
            'document_file'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);

        // Proteksi isolasi unit bagi TU / Kepsek
        if ($user->isUnitLevel() && $user->unit_id && $employee->unit_id !== $user->unit_id) {
            abort(403, 'Akses ditolak: Anda hanya dapat mengusulkan pegawai pada unit kerja Anda.');
        }

        $unitId = $employee->unit_id ?: ($user->unit_id ?: 1);

        // Upload berkas pendukung jika ada
        $docs = [];
        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $filename = 'promo_' . time() . '_' . Str::slug($employee->full_name) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('promotions/documents', $filename, 'public');
            $docs[] = [
                'name' => 'Dokumen Usulan & Rekomendasi',
                'path' => '/storage/' . $path,
                'uploaded_at' => now()->toDateTimeString(),
            ];
        }

        $promotion = DB::transaction(function () use ($employee, $unitId, $validated, $request, $docs) {
            return RankPromotion::create([
                'employee_id'            => $employee->id,
                'unit_id'                => $unitId,
                'promotion_type'         => $validated['promotion_type'],
                'current_rank'           => $request->input('current_rank') ?: ($employee->inpassing_grade ?: 'Penata Muda / III.a'),
                'proposed_rank'          => $validated['proposed_rank'],
                'current_employee_type'  => $validated['current_employee_type'],
                'proposed_employee_type' => $validated['proposed_employee_type'],
                'years_of_service'       => (int) $validated['years_of_service'],
                'performance_score'      => $request->input('performance_score'),
                'credit_points'          => $request->input('credit_points'),
                'new_education_degree'   => $request->input('new_education_degree'),
                'ortom_involvement'      => $request->input('ortom_involvement'),
                'supporting_documents'   => $docs,
                'status'                 => 'diajukan',
                'reviewer_notes'         => $request->input('reviewer_notes'),
            ]);
        }, 5);

        return redirect()->route('promotions.show', $promotion->id)
            ->with('success', 'Usulan kenaikan pangkat / alih status pegawai berhasil diajukan ke PDM Kota Yogyakarta.');
    }

    public function show($id)
    {
        $user = auth()->user();
        $promotion = RankPromotion::with(['employee', 'unit', 'verifier'])->findOrFail($id);

        if ($user && $user->isUnitLevel() && $user->unit_id && $user->unit_id !== $promotion->unit_id) {
            abort(403, 'Akses ditolak: Anda hanya berhak melihat usulan kenaikan pangkat pada unit kerja Anda sendiri.');
        }

        return view('promotions.show', compact('promotion'));
    }

    /**
     * Verifikasi, Sidang Majelis, atau Persetujuan SK Kenaikan Pangkat & Alih Status
     */
    public function verify(Request $request, $id)
    {
        $user = auth()->user();
        if ($user->isPegawai()) {
            abort(403, 'Akses ditolak.');
        }

        $promotion = RankPromotion::with(['employee', 'unit'])->findOrFail($id);

        // Jika Petugas TU / unit-level hanya berhak memverifikasi tingkat sekolah jika belum diajukan ke majelis
        if ($user->isUnitLevel()) {
            if ($user->unit_id && $user->unit_id !== $promotion->unit_id) {
                abort(403, 'Akses ditolak: Anda hanya dapat memverifikasi usulan unit Anda sendiri.');
            }
            if (in_array($request->input('status'), ['sidang_majelis', 'disetujui_pdm'])) {
                abort(403, 'Akses ditolak: Persetujuan SK PDM dan Sidang Majelis hanya dapat dilakukan oleh Ketua Majelis / PDM.');
            }
        }

        $validated = $request->validate([
            'status'         => 'required|in:diajukan,diverifikasi_sekolah,sidang_majelis,disetujui_pdm,ditolak',
            'reviewer_notes' => 'nullable|string',
            'sk_number'      => 'nullable|string|max:100',
            'sk_date'        => 'nullable|date',
            'effective_date' => 'nullable|date',
        ]);

        DB::transaction(function () use ($promotion, $validated, $request, $user) {
            $promotion->status = $validated['status'];
            if ($request->filled('reviewer_notes')) {
                $promotion->reviewer_notes = $validated['reviewer_notes'];
            }

            if ($validated['status'] === 'disetujui_pdm') {
                $promotion->sk_number = $validated['sk_number'] ?? null;
                $promotion->sk_date = $validated['sk_date'] ?? null;
                $promotion->effective_date = ($validated['effective_date'] ?? null) ?: now()->toDateString();
                $promotion->verified_by = $user->id;

                // Update data pegawai sesuai usulan yang disetujui (misal GTT -> GTY, PTT -> PTY)
                if ($promotion->employee) {
                    $emp = Employee::lockForUpdate()->find($promotion->employee_id);
                    if ($emp) {
                        if (!empty($promotion->proposed_employee_type)) {
                            $emp->employee_type = $promotion->proposed_employee_type;
                        }
                        if (!empty($promotion->proposed_rank)) {
                            $emp->inpassing_grade = $promotion->proposed_rank;
                        }
                        $emp->save();
                    }
                }
            }

            $promotion->save();
        }, 5);

        return redirect()->route('promotions.show', $promotion->id)
            ->with('success', 'Status usulan kenaikan pangkat / alih status berhasil diperbarui.');
    }
}
