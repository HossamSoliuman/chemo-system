<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Diagnosis;
use Illuminate\Http\Request;

class DiagnosisController extends Controller
{
    public function index()
    {
        $diagnoses = Diagnosis::withCount('protocols')->orderBy('name')->paginate(20);
        return view('admin.diagnoses.index', compact('diagnoses'));
    }

    public function create()
    {
        return view('admin.diagnoses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icd_code' => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);

        Diagnosis::create($request->only('name', 'icd_code', 'description'));

        return redirect()->route('admin.diagnoses.index')->with('success', 'Diagnosis created successfully.');
    }

    public function edit(Diagnosis $diagnosis)
    {
        return view('admin.diagnoses.edit', compact('diagnosis'));
    }

    public function update(Request $request, Diagnosis $diagnosis)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icd_code' => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);

        $diagnosis->update($request->only('name', 'icd_code', 'description'));

        return redirect()->route('admin.diagnoses.index')->with('success', 'Diagnosis updated successfully.');
    }

    public function destroy(Diagnosis $diagnosis)
    {
        if ($diagnosis->protocols()->count() > 0) {
            return back()->with('error', 'Cannot delete diagnosis with associated protocols.');
        }
        $diagnosis->delete();
        return redirect()->route('admin.diagnoses.index')->with('success', 'Diagnosis deleted.');
    }
}
