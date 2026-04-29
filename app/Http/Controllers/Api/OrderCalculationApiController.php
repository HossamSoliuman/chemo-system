<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Protocol;
use App\Services\ClinicalCalculationService;
use Illuminate\Http\Request;

class OrderCalculationApiController extends Controller
{
    public function __construct(private ClinicalCalculationService $calc) {}

    public function calculate(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'protocol_id' => 'required|exists:protocols,id',
            'dose_modification_percent' => 'nullable|numeric|min:1|max:200',
        ]);

        $patient = Patient::findOrFail($request->patient_id);
        $protocol = Protocol::with('protocolDrugs.drug')->findOrFail($request->protocol_id);

        $age = $this->calc->calculateAge($patient->date_of_birth->toDateTime());
        $bsa = $this->calc->calculateBSA($patient->height_cm, $patient->weight_kg);
        $crcl = $this->calc->calculateCrCl($age, $patient->weight_kg, $patient->serum_creatinine, $patient->gender);
        $modPct = (float) ($request->dose_modification_percent ?? 100);
        $cycleInfo = $this->calc->determineCycleNumber($patient, $protocol);

        $drugs = $protocol->protocolDrugs->map(function ($pd) use ($bsa, $crcl) {
            $result = $this->calc->calculateDrugDose($pd, $bsa, $crcl, 100);
            return [
                'protocol_drug_id' => $pd->id,
                'drug_id' => $pd->drug_id,
                'drug_name' => $pd->drug->name,
                'drug_unit' => $pd->drug->unit,
                'category' => $pd->category,
                'dose_type' => $pd->dose_type,
                'route' => $pd->route,
                'frequency' => $pd->frequency,
                'per_cycle_cap' => $pd->per_cycle_cap,
                'per_cycle_cap_unit' => $pd->per_cycle_cap_unit,
                'lifetime_cap' => $pd->lifetime_cap,
                'lifetime_cap_unit' => $pd->lifetime_cap_unit,
                'calculated_dose' => $result['calculated'],
                'final_dose' => $result['final'],
                'cap_applied' => $result['cap_applied'],
            ];
        });

        return response()->json([
            'bsa' => $bsa,
            'crcl' => $crcl,
            'age' => $age,
            'cycle_info' => $cycleInfo,
            'drugs' => $drugs,
        ]);
    }
}
