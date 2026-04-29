<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\ProtocolDrug;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::query();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sq) use ($q) {
                $sq->where('mrn', 'like', "%$q%")
                    ->orWhere('name', 'like', "%$q%");
            });
        }

        $patients = $query->orderBy('name')->paginate(20)->withQueryString();
        return view('patients.index', compact('patients'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $request->validate($this->rules());
        Patient::create($request->only(array_keys($this->rules())));
        return redirect()->route('patients.index')->with('success', 'Patient created successfully.');
    }

    public function show(Patient $patient)
    {
        $patient->load(['orders.protocol.diagnosis', 'cumulativeDoses.drug']);

        $cumulativeDoses = $patient->cumulativeDoses()
            ->with('drug')
            ->get()
            ->map(function ($cd) use ($patient) {
                $protocolDrug = ProtocolDrug::where('drug_id', $cd->drug_id)
                    ->whereNotNull('lifetime_cap')
                    ->orderByDesc('lifetime_cap')
                    ->first();
                $cd->lifetime_cap = $protocolDrug?->lifetime_cap;
                $cd->lifetime_cap_unit = $protocolDrug?->lifetime_cap_unit;
                return $cd;
            });

        return view('patients.show', compact('patient', 'cumulativeDoses'));
    }

    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $rules = $this->rules();
        $rules['mrn'] = 'required|string|max:50|unique:patients,mrn,' . $patient->id;
        $request->validate($rules);
        $patient->update($request->only(array_keys($this->rules())));
        return redirect()->route('patients.show', $patient)->with('success', 'Patient updated successfully.');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();
        return redirect()->route('patients.index')->with('success', 'Patient deleted.');
    }

    public function search(Request $request)
    {
        $mrn = $request->mrn;
        $patient = Patient::where('mrn', $mrn)->first();

        if (!$patient) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found' => true,
            'patient' => $patient,
        ]);
    }

    private function rules(): array
    {
        return [
            'mrn' => 'required|string|max:50|unique:patients,mrn',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'required|date|before:today',
            'height_cm' => 'required|numeric|min:50|max:250',
            'weight_kg' => 'required|numeric|min:1|max:500',
            'serum_creatinine' => 'required|numeric|min:10|max:2000',
        ];
    }
}
