<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\HeadmasterCandidate;
use App\Models\HeadmasterSelection;
use App\Models\RankPromotion;
use App\Models\Unit;
use App\Models\ViceHeadmasterNomination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeadershipController extends Controller
{
    /**
     * Modul Promosi Kepala Sekolah
     */
    public function headmasters(Request $request)
    {
        $user = auth()->user();
        if (! $user->isYayasanLevel()) {
            abort(403, 'Akses ditolak: Modul Promosi Kepala Sekolah hanya dapat diakses oleh Admin dan Pengurus Yayasan/Majelis.');
        }

        $query = HeadmasterSelection::with(['unit', 'candidates.employee', 'electedCandidate.employee']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if (auth()->user()->isUnitLevel() && auth()->user()->unit_id) {
            $query->where('unit_id', auth()->user()->unit_id);
        } elseif ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }

        $selections = $query->orderBy('created_at', 'desc')->paginate(10);

        $counts = [
            'total'       => (auth()->user()->isUnitLevel() && auth()->user()->unit_id) ? HeadmasterSelection::where('unit_id', auth()->user()->unit_id)->count() : HeadmasterSelection::count(),
            'penjaringan' => (auth()->user()->isUnitLevel() && auth()->user()->unit_id) ? HeadmasterSelection::where('unit_id', auth()->user()->unit_id)->where('status', 'penjaringan')->count() : HeadmasterSelection::where('status', 'penjaringan')->count(),
            'fit_proper'  => (auth()->user()->isUnitLevel() && auth()->user()->unit_id) ? HeadmasterSelection::where('unit_id', auth()->user()->unit_id)->where('status', 'fit_and_proper')->count() : HeadmasterSelection::where('status', 'fit_and_proper')->count(),
            'selesai'     => (auth()->user()->isUnitLevel() && auth()->user()->unit_id) ? HeadmasterSelection::where('unit_id', auth()->user()->unit_id)->where('status', 'selesai')->count() : HeadmasterSelection::where('status', 'selesai')->count(),
        ];

        $units = Unit::active()->orderBy('name')->get();

        return view('leadership.headmasters', compact('selections', 'counts', 'units'));
    }

    /**
     * Detail Seleksi Kepala Sekolah & Calon
     */
    public function headmasterShow($id)
    {
        $user = auth()->user();
        if (! $user->isYayasanLevel()) {
            abort(403, 'Akses ditolak: Modul Promosi Kepala Sekolah hanya dapat diakses oleh Admin dan Pengurus Yayasan/Majelis.');
        }

        $selection = HeadmasterSelection::with(['unit', 'candidates.employee.unit'])->findOrFail($id);
        $candidates = $selection->candidates()->orderBy('total_score', 'desc')->get();

        // Pegawai yang bisa dicalonkan
        $eligibleEmployees = Employee::where('is_active', true)
            ->whereIn('employee_type', ['GTY', 'guru'])
            ->when($selection->unit_id, function($q) use ($selection) {
                $q->where('unit_id', $selection->unit_id);
            })
            ->orderBy('full_name')
            ->get();

        return view('leadership.headmaster_show', compact('selection', 'candidates', 'eligibleEmployees'));
    }

    /**
     * Usulkan Bakal Calon Kepala Sekolah dengan proteksi transaksi & lock duplikasi.
     */
    public function headmasterNominate(Request $request, $selectionId)
    {
        $selection = HeadmasterSelection::findOrFail($selectionId);
        $user = auth()->user();

        if ($user->isKetuaMajelis()) {
            abort(403, 'Akses ditolak: Pendaftaran bakal calon dilakukan oleh Petugas TU Unit atau Tim Penjaringan.');
        }

        $validated = $request->validate([
            'employee_id'            => 'required|exists:employees,id',
            'nbm'                    => 'nullable|string|max:50',
            'vision_mission_rps'     => 'nullable|string',
            'recommendation_pcm'     => 'nullable|string',
            'recommendation_komite'  => 'nullable|string',
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);

        $created = DB::transaction(function () use ($selection, $employee, $validated) {
            // Cek duplikasi dengan lock
            $exists = HeadmasterCandidate::where('headmaster_selection_id', $selection->id)
                ->where('employee_id', $employee->id)
                ->lockForUpdate()
                ->exists();

            if ($exists) {
                return false;
            }

            HeadmasterCandidate::create([
                'headmaster_selection_id' => $selection->id,
                'employee_id'             => $employee->id,
                'nbm'                     => $validated['nbm'] ?: $employee->nbm,
                'vision_mission_rps'      => $validated['vision_mission_rps'],
                'recommendation_pcm'      => $validated['recommendation_pcm'],
                'recommendation_komite'   => $validated['recommendation_komite'],
            ]);

            return true;
        }, 5);

        if (!$created) {
            return back()->with('error', 'Pegawai bersangkutan sudah terdaftar sebagai calon pada agenda seleksi ini.');
        }

        return back()->with('success', 'Bakal calon Kepala Sekolah berhasil didaftarkan ke Majelis Dikdasmen.');
    }

    /**
     * Modul Promosi Wakil Kepala Sekolah
     */
    public function viceHeadmasters(Request $request)
    {
        $user = auth()->user();
        if ($user && $user->isPegawai()) {
            abort(403, 'Akses ditolak: Pegawai tidak memiliki izin untuk mengelola promosi pimpinan unit.');
        }

        $query = ViceHeadmasterNomination::with(['unit', 'employee']);

        if ($request->filled('field')) {
            $query->where('field', $request->field);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if (auth()->user()->isUnitLevel() && auth()->user()->unit_id) {
            $query->where('unit_id', auth()->user()->unit_id);
        } elseif ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }

        $nominations = $query->orderBy('created_at', 'desc')->paginate(15);

        $counts = [
            'total'     => (auth()->user()->isUnitLevel() && auth()->user()->unit_id) ? ViceHeadmasterNomination::where('unit_id', auth()->user()->unit_id)->count() : ViceHeadmasterNomination::count(),
            'diajukan'  => (auth()->user()->isUnitLevel() && auth()->user()->unit_id) ? ViceHeadmasterNomination::where('unit_id', auth()->user()->unit_id)->where('status', 'diajukan')->count() : ViceHeadmasterNomination::where('status', 'diajukan')->count(),
            'disetujui' => (auth()->user()->isUnitLevel() && auth()->user()->unit_id) ? ViceHeadmasterNomination::where('unit_id', auth()->user()->unit_id)->where('status', 'disetujui')->count() : ViceHeadmasterNomination::where('status', 'disetujui')->count(),
        ];

        $units = Unit::active()->orderBy('name')->get();

        // Pegawai untuk form usulan Waka
        $user = auth()->user();
        if ($user->isUnitLevel() && $user->unit_id) {
            $eligibleEmployees = Employee::where('unit_id', $user->unit_id)->where('is_active', true)->orderBy('full_name')->get();
        } else {
            $eligibleEmployees = Employee::where('is_active', true)->orderBy('full_name')->get();
        }

        return view('leadership.vice_headmasters', compact('nominations', 'counts', 'units', 'eligibleEmployees'));
    }

    /**
     * Simpan Usulan Wakil Kepala Sekolah Baru dalam transaksi aman.
     */
    public function viceHeadmasterStore(Request $request)
    {
        $user = auth()->user();
        if ($user->isPegawai()) {
            abort(403, 'Akses ditolak.');
        }

        if ($user->isKetuaMajelis()) {
            abort(403, 'Akses ditolak: Pengajuan calon Wakil Kepala Sekolah dilakukan oleh Petugas TU Unit atau Kepala Sekolah.');
        }

        $validated = $request->validate([
            'employee_id'               => 'required|exists:employees,id',
            'field'                     => 'required|in:kurikulum_ismuba,kesiswaan,sarpras,humas_kemuhammadiyahan,lainnya',
            'period_years'              => 'required|string|max:50',
            'recommendation_headmaster' => 'nullable|string',
            'notes'                     => 'nullable|string',
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);
        $unitId = ($user->isUnitLevel() && $user->unit_id) ? $user->unit_id : ($employee->unit_id ?: 1);

        DB::transaction(function () use ($unitId, $employee, $validated) {
            ViceHeadmasterNomination::create([
                'unit_id'                   => $unitId,
                'employee_id'               => $employee->id,
                'field'                     => $validated['field'],
                'period_years'              => $validated['period_years'],
                'recommendation_headmaster' => $validated['recommendation_headmaster'],
                'status'                    => 'diajukan',
                'notes'                     => $validated['notes'],
            ]);
        }, 5);

        return redirect()->route('leadership.vice_headmasters')
            ->with('success', 'Usulan calon Wakil Kepala Sekolah berhasil diajukan ke Majelis Dikdasmen.');
    }

    /**
     * Verifikasi & Terbitkan SK Wakil Kepala Sekolah dalam transaksi atomik.
     */
    public function viceHeadmasterVerify(Request $request, $id)
    {
        $user = auth()->user();
        if (! $user->isYayasanLevel()) {
            abort(403, 'Akses ditolak: Hanya pengurus Majelis / Yayasan yang berwenang memverifikasi.');
        }

        $nomination = ViceHeadmasterNomination::findOrFail($id);

        $validated = $request->validate([
            'status'    => 'required|in:diajukan,verifikasi_majelis,disetujui,ditolak',
            'sk_number' => 'nullable|string|max:100',
            'sk_date'   => 'nullable|date',
            'notes'     => 'nullable|string',
        ]);

        if ($validated['status'] === 'disetujui' && !$user->canApproveAsKetuaMajelis()) {
            abort(403, 'Akses ditolak: Persetujuan penetapan dan penerbitan SK Wakil Kepala Sekolah merupakan wewenang Ketua Majelis.');
        }

        DB::transaction(function () use ($nomination, $validated, $request) {
            $nomination->status = $validated['status'];
            if ($request->filled('notes')) {
                $nomination->notes = $validated['notes'];
            }
            if ($validated['status'] === 'disetujui') {
                $nomination->sk_number = $validated['sk_number'];
                $nomination->sk_date = $validated['sk_date'] ?: now()->toDateString();
            }

            $nomination->save();
        }, 5);

        return back()->with('success', 'Status usulan Wakil Kepala Sekolah berhasil diperbarui.');
    }

    /**
     * REKAPITULASI USULAN TERPADU
     * Rekap usulan kenaikan pangkat/status (GTT->GTY, PTT->PTY), calon KS, dan Waka dari berbagai sekolah
     */
    public function proposalsRecap(Request $request)
    {
        $user = auth()->user();
        if (! $user->isYayasanLevel()) {
            abort(403, 'Akses ditolak: Rekapitulasi Usulan Terpadu hanya dapat diakses oleh Admin dan Pengurus Yayasan/Majelis.');
        }

        // 1. Query Usulan Kenaikan Pangkat & Alih Status Pegawai
        $rankQuery = RankPromotion::with(['employee', 'unit', 'verifier']);
        if ($user->isUnitLevel() && $user->unit_id) {
            $rankQuery->where('unit_id', $user->unit_id);
        } elseif ($request->filled('unit_id')) {
            $rankQuery->where('unit_id', $request->unit_id);
        }
        if ($request->filled('type')) {
            $rankQuery->where('promotion_type', $request->type);
        }
        if ($request->filled('status')) {
            $rankQuery->where('status', $request->status);
        }
        $rankPromotions = $rankQuery->orderBy('created_at', 'desc')->get();

        // 2. Query Usulan Calon Kepala Sekolah
        $ksQuery = HeadmasterCandidate::with(['selection.unit', 'employee.unit']);
        if ($user->isUnitLevel() && $user->unit_id) {
            $ksQuery->whereHas('selection', function($q) use ($user) {
                $q->where('unit_id', $user->unit_id);
            });
        } elseif ($request->filled('unit_id')) {
            $ksQuery->whereHas('selection', function($q) use ($request) {
                $q->where('unit_id', $request->unit_id);
            });
        }
        $ksCandidates = $ksQuery->orderBy('created_at', 'desc')->get();

        // 3. Query Usulan Tim Manajemen (Wakil Kepala Sekolah)
        $wakaQuery = ViceHeadmasterNomination::with(['unit', 'employee']);
        if ($user->isUnitLevel() && $user->unit_id) {
            $wakaQuery->where('unit_id', $user->unit_id);
        } elseif ($request->filled('unit_id')) {
            $wakaQuery->where('unit_id', $request->unit_id);
        }
        $viceNominations = $wakaQuery->orderBy('created_at', 'desc')->get();

        // Metrik Ringkasan
        $stats = [
            'total_all'      => $rankPromotions->count() + $ksCandidates->count() + $viceNominations->count(),
            'total_rank'     => $rankPromotions->count(),
            'total_alih'     => $rankPromotions->whereIn('promotion_type', ['alih_status_gty', 'alih_status_pty'])->count(),
            'total_ks'       => $ksCandidates->count(),
            'total_waka'     => $viceNominations->count(),
            'pending_review' => $rankPromotions->whereIn('status', ['diajukan', 'diverifikasi_sekolah', 'sidang_majelis'])->count()
                                + $viceNominations->whereIn('status', ['diajukan', 'verifikasi_majelis'])->count(),
            'approved'       => $rankPromotions->where('status', 'disetujui_pdm')->count()
                                + $viceNominations->where('status', 'disetujui')->count()
                                + $ksCandidates->where('is_elected', true)->count(),
        ];

        $units = Unit::active()->orderBy('name')->get();

        return view('proposals.recap', compact('rankPromotions', 'ksCandidates', 'viceNominations', 'stats', 'units'));
    }

    /**
     * Cetak Lembar Rekapitulasi Usulan (Print View)
     */
    public function proposalsRecapPrint(Request $request)
    {
        $user = auth()->user();
        if (! $user->isYayasanLevel()) {
            abort(403, 'Akses ditolak: Rekapitulasi Usulan Terpadu hanya dapat diakses oleh Admin dan Pengurus Yayasan/Majelis.');
        }

        $rankPromotions = RankPromotion::with(['employee', 'unit'])
            ->when($user->isUnitLevel() && $user->unit_id, function($q) use ($user) {
                $q->where('unit_id', $user->unit_id);
            })
            ->when($request->filled('unit_id'), function($q) use ($request) {
                $q->where('unit_id', $request->unit_id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $ksCandidates = HeadmasterCandidate::with(['selection.unit', 'employee.unit'])
            ->when($user->isUnitLevel() && $user->unit_id, function($q) use ($user) {
                $q->whereHas('selection', function($sq) use ($user) {
                    $sq->where('unit_id', $user->unit_id);
                });
            })
            ->when($request->filled('unit_id'), function($q) use ($request) {
                $q->whereHas('selection', function($sq) use ($request) {
                    $sq->where('unit_id', $request->unit_id);
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $viceNominations = ViceHeadmasterNomination::with(['unit', 'employee'])
            ->when($user->isUnitLevel() && $user->unit_id, function($q) use ($user) {
                $q->where('unit_id', $user->unit_id);
            })
            ->when($request->filled('unit_id'), function($q) use ($request) {
                $q->where('unit_id', $request->unit_id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $selectedUnit = $request->filled('unit_id') ? Unit::find($request->unit_id) : ($user->isUnitLevel() ? $user->unit : null);

        return view('proposals.print', compact('rankPromotions', 'ksCandidates', 'viceNominations', 'selectedUnit'));
    }
}
