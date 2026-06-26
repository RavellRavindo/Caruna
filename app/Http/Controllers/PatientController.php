<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{

    public function index()
    {
        $patients = Patient::where('user_id', Auth::id())->get();
        return view('patients.index', compact('patients'));
    }
    
    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'birth_date' => 'required|date',
            'health_condition' => 'required|string',
            'emergency_contact' => 'required|string',
        ]);

        Patient::create([
            'user_id' => Auth::id(),
            'full_name' => $request->full_name,
            'gender' => $request->gender,
            'birth_date' => $request->birth_date,
            'health_condition' => $request->health_condition,
            'emergency_contact' => $request->emergency_contact,
        ]);

        return redirect()->route('patients.index')->with('success', 'Data pasien berhasil ditambahkan!');
    }
}