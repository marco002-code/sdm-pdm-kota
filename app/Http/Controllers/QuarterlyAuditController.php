<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Unit;
use App\Models\UnitUpdateAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuarterlyAuditController extends Controller
{
    /**
     * Tampilkan rekapitulasi monitoring pembaruan data berkala triwulanan (per 3 bulan).
     */
    public function index(Request $request)
    {
        $year = (int) $request->input('year', date('Y'));
        $quarter = (int) $request->input('quarter', UnitUpdateAudit::getCurrentQuarter());

        $user = Auth::user();

        // Ambil semua unit sekolah aktif
        $unitsQuery = Unit::where('is_active', true)->where('level', '!=', 'YAYASAN');

        if ($user->isUnitLevel() && $user->unit_id) {
            $unitsQuery->where('id', $user->unit_id);
        }

        if ($request->filled('level')) {
            $unitsQuery->where('level', $request->level);
        }

        $units = $unitsQuery->orderBy('name', 'asc')->get();

        // Ambil audit records untuk periode ini
        $audits = UnitUpdateAudit::with(['updater', 'verifier'])
            ->where('year', $year)
            ->where('quarter', $quarter)
            ->get()
            ->keyBy('unit_id');

        // Hitung statistik
        $totalUnits = $units->count();
        $updatedUnits = 0;
        $verifiedUnits = 0;

        foreach ($units as $unit) {
            $audit = $audits->get($unit->id);
            if ($audit && in_array($audit->status, ['sudah_update', 'terverifikasi'])) {
                $updatedUnits++;
            }
            if ($audit && $audit->status === 'terverifikasi') {
                $verifiedUnits++;
            }
        }

        $pendingUnits = $totalUnits - $updatedUnits;

        return view('audits.index', compact(
            'units',
            'audits',
            'year',
            'quarter',
            'totalUnits',
            'updatedUnits',
            'pendingUnits',
            'verifiedUnits'
        ));
    }

    /**
     * Konfirmasi update data triwulanan oleh operator sekolah / verifikasi PDM dalam transaksi atomik.
     */
    public function updateStatus(Request $request, Unit $unit)
    {
        $user = Auth::user();

        if ($user->isUnitLevel() && $user->unit_id && $user->unit_id !== $unit->id) {
            abort(403, 'Akses ditolak.');
        }

        $year = (int) $request->input('year', date('Y'));
        $quarter = (int) $request->input('quarter', UnitUpdateAudit::getCurrentQuarter());
        $status = $request->input('status', 'sudah_update');

        DB::transaction(function () use ($unit, $year, $quarter, $status, $user, $request) {
            $empCount = Employee::where('unit_id', $unit->id)->where('status', 'aktif')->count();

            $audit = UnitUpdateAudit::firstOrNew([
                'unit_id' => $unit->id,
                'year' => $year,
                'quarter' => $quarter,
            ]);

            $audit->status = $status;
            $audit->total_employees = $empCount;
            $audit->last_updated_at = now();

            if ($user->isYayasanLevel()) {
                $audit->verified_by = $user->id;
                if ($status === 'terverifikasi') {
                    $audit->status = 'terverifikasi';
                }
            } else {
                $audit->updated_by = $user->id;
            }

            if ($request->filled('notes')) {
                $audit->notes = $request->notes;
            }

            $audit->save();
        }, 5);

        return redirect()->back()
            ->with('success', "Status kepatuhan pembaruan data triwulanan untuk {$unit->name} berhasil diperbarui.");
    }
}
