<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Unit;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Menampilkan Landing Page ringkas dan sederhana SIM-SDM PDM Kota Yogyakarta.
     */
    public function index()
    {
        $totalUnits = Unit::count();
        $totalEmployees = Employee::count();

        return view('landing', compact('totalUnits', 'totalEmployees'));
    }
}
