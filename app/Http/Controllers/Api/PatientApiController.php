<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;

class PatientApiController extends Controller
{
    public function findByMrn(string $mrn)
    {
        $patient = Patient::where('mrn', $mrn)->first();

        if (!$patient) {
            return response()->json(['found' => false], 404);
        }

        $age = $patient->date_of_birth->age;

        return response()->json([
            'found' => true,
            'patient' => array_merge($patient->toArray(), ['age' => $age]),
        ]);
    }

    public function cumulativeDoses(Patient $patient)
    {
        $doses = $patient->cumulativeDoses()->with('drug')->get();
        return response()->json($doses);
    }
}
