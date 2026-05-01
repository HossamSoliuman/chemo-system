<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Services\ClinicalCalculationService;
use Illuminate\Http\Request;

class PatientQuickUpdateController extends Controller
{
    public function __construct(private ClinicalCalculationService $calc) {}

    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'height_cm'        => 'nullable|numeric|min:50|max:250',
            'weight_kg'        => 'nullable|numeric|min:1|max:500',
            'serum_creatinine' => 'nullable|numeric|min:10|max:2000',
        ]);

        $patient->update(array_filter([
            'height_cm'        => $request->height_cm,
            'weight_kg'        => $request->weight_kg,
            'serum_creatinine' => $request->serum_creatinine,
        ], fn($v) => $v !== null));

        $patient->refresh();
        $age  = $patient->date_of_birth->age;
        $bsa  = $this->calc->calculateBSA($patient->height_cm, $patient->weight_kg);
        $crcl = $this->calc->calculateCrCl($age, $patient->weight_kg, $patient->serum_creatinine, $patient->gender);

        return response()->json([
            'success' => true,
            'patient' => $patient,
            'bsa'     => $bsa,
            'crcl'    => $crcl,
        ]);
    }
}
