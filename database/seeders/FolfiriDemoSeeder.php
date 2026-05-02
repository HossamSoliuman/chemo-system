<?php

namespace Database\Seeders;

use App\Models\Diagnosis;
use App\Models\Drug;
use App\Models\Order;
use App\Models\OrderDrug;
use App\Models\Patient;
use App\Models\PatientCumulativeDose;
use App\Models\Protocol;
use App\Models\ProtocolDrug;
use App\Services\ClinicalCalculationService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FolfiriDemoSeeder extends Seeder
{
    public function run(): void
    {
        $calc = new ClinicalCalculationService();

        $diagnosis = Diagnosis::firstOrCreate(
            ['name' => 'Colorectal Cancer'],
            ['icd_code' => 'C18', 'description' => 'Colorectal adenocarcinoma']
        );

        $irinotecan  = Drug::firstOrCreate(['name' => 'Irinotecan'],  ['unit' => 'mg', 'description' => 'Topoisomerase I inhibitor']);
        $leucovorin  = Drug::firstOrCreate(['name' => 'Leucovorin (Folinic Acid)'], ['unit' => 'mg', 'description' => 'Folinic acid']);
        $fivefu      = Drug::firstOrCreate(['name' => 'Fluorouracil (5-FU)'], ['unit' => 'mg', 'description' => '5-Fluorouracil']);
        $netupitant  = Drug::firstOrCreate(['name' => 'Netupitant/Palonosetron'], ['unit' => 'mg', 'description' => 'NK1/5HT3 antagonist combination — 300/0.5 mg capsule']);
        $dexameth    = Drug::firstOrCreate(['name' => 'Dexamethasone'], ['unit' => 'mg']);
        $atropine    = Drug::firstOrCreate(['name' => 'Atropine'], ['unit' => 'mg', 'description' => 'Anticholinergic for irinotecan-related bradycardia']);

        $existing = Protocol::where('name', 'FOLFIRI (Demo)')->first();
        if ($existing) {
            $existing->protocolDrugs()->delete();
            $existing->delete();
        }

        $protocol = Protocol::create([
            'diagnosis_id'      => $diagnosis->id,
            'name'              => 'FOLFIRI (Demo)',
            'description'       => 'FOLFIRI regimen for colorectal cancer. Irinotecan + Leucovorin + 5-FU bolus + 5-FU infusion.',
            'cycle_duration_days' => 14,
            'tests_reminder'    => 'CBC & diff, platelets. Check when necessary: Bilirubin & creatinine. May proceed with doses if ANC > 1.0×10⁹/L, Platelets > 100×10⁹/L, serum creatinine < 1.4 mg/dl, bilirubin < 1.2 mg/dl',
        ]);

        $drugs = [
            [
                'drug_id'        => $netupitant->id,
                'category'       => 'pre_medication',
                'dose_type'      => 'fixed',
                'fixed_dose'     => 1,
                'dose_per_unit'  => null,
                'dose_label'     => null,
                'route'          => 'Oral',
                'frequency'      => 'PO 60 minutes before chemotherapy',
                'duration_days'  => '1',
                'notes'          => 'NETUPITANT/Palonosetron 300mg/0.5mg capsule — take once',
                'sort_order'     => 0,
            ],
            [
                'drug_id'        => $dexameth->id,
                'category'       => 'pre_medication',
                'dose_type'      => 'fixed',
                'fixed_dose'     => 8,
                'dose_per_unit'  => null,
                'dose_label'     => null,
                'route'          => 'IV push',
                'frequency'      => 'Days 1 & 2, 30 minutes before chemotherapy',
                'duration_days'  => '2',
                'notes'          => null,
                'sort_order'     => 1,
            ],
            [
                'drug_id'        => $atropine->id,
                'category'       => 'pre_medication',
                'dose_type'      => 'fixed',
                'fixed_dose'     => 0.5,
                'dose_per_unit'  => null,
                'dose_label'     => null,
                'route'          => 'IM',
                'frequency'      => 'Injection in the abdomen immediately after starting Irinotecan',
                'duration_days'  => '1',
                'notes'          => 'Give if cholinergic symptoms occur (sweating, abdominal cramp, diarrhea within 24h)',
                'sort_order'     => 2,
            ],
            [
                'drug_id'        => $irinotecan->id,
                'category'       => 'chemotherapy',
                'dose_type'      => 'bsa_based',
                'dose_per_unit'  => 180,
                'dose_label'     => '180 mg/m²',
                'fixed_dose'     => null,
                'target_auc'     => null,
                'route'          => 'Continuous IV infusion',
                'frequency'      => 'Day 1',
                'duration_days'  => null,
                'notes'          => 'IV in 500 ml D5W over 90 minutes, given simultaneously with Leucovorin on Day 1',
                'sort_order'     => 3,
            ],
            [
                'drug_id'        => $leucovorin->id,
                'category'       => 'chemotherapy',
                'dose_type'      => 'bsa_based',
                'dose_per_unit'  => 400,
                'dose_label'     => '400 mg/m²',
                'fixed_dose'     => null,
                'target_auc'     => null,
                'route'          => 'Continuous IV infusion',
                'frequency'      => 'Day 1',
                'duration_days'  => null,
                'notes'          => 'IV IN 250 ml D5W over 2 hours on Day 1',
                'sort_order'     => 4,
            ],
            [
                'drug_id'        => $fivefu->id,
                'category'       => 'chemotherapy',
                'dose_type'      => 'bsa_based',
                'dose_per_unit'  => 400,
                'dose_label'     => '400 mg/m²',
                'fixed_dose'     => null,
                'target_auc'     => null,
                'route'          => 'IV bolus',
                'frequency'      => 'Day 1',
                'duration_days'  => null,
                'notes'          => 'mg IV bolus immediately after LEUCOVORIN',
                'sort_order'     => 5,
            ],
            [
                'drug_id'        => $fivefu->id,
                'category'       => 'chemotherapy',
                'dose_type'      => 'bsa_based',
                'dose_per_unit'  => 2400,
                'dose_label'     => '2400 mg/m²',
                'fixed_dose'     => null,
                'target_auc'     => null,
                'route'          => 'Continuous IV infusion',
                'frequency'      => 'Day 1',
                'duration_days'  => null,
                'notes'          => 'mg IV in 250 ml D5W over 46 hours, following FLUOROURACIL bolus',
                'sort_order'     => 6,
            ],
        ];

        foreach ($drugs as $d) {
            ProtocolDrug::create(array_merge(['protocol_id' => $protocol->id], $d));
        }

        $patient = Patient::where('mrn', 'DEMO-001')->first();
        if (!$patient) {
            $patient = Patient::create([
                'mrn'                  => 'DEMO-001',
                'name'                 => 'Hamdah Masoud Alghabie',
                'gender'               => 'female',
                'nationality'          => 'Saudi',
                'date_of_birth'        => '1970-01-01',
                'height_cm'            => 150.0,
                'weight_kg'            => 73.0,
                'serum_creatinine'     => 70.00,
                'consultant_in_charge' => 'Dr. Hossam',
                'pregnant'             => 'no',
                'lactating'            => 'no',
                'has_allergy'          => false,
                'cancer_stage'         => 'III',
                'ecog_status'          => '1',
                'chemo_setting'        => 'Palliative',
            ]);
        }

        $protocol->load('protocolDrugs.drug');

        $age  = $calc->calculateAge($patient->date_of_birth->toDateTime());
        $bsa  = $calc->calculateBSA($patient->height_cm, $patient->weight_kg);
        $crcl = $calc->calculateCrCl($age, $patient->weight_kg, $patient->serum_creatinine, $patient->gender);

        $existing = Order::where('patient_id', $patient->id)
            ->where('protocol_id', $protocol->id)
            ->first();
        if ($existing) {
            $existing->orderDrugs()->delete();
            $existing->delete();
        }

        $order = Order::create([
            'patient_id'               => $patient->id,
            'protocol_id'              => $protocol->id,
            'order_number'             => 'DEMO-2026-0001',
            'cycle_number'             => 9,
            'is_same_cycle'            => false,
            'is_split_cycle'           => false,
            'cycle_day_week'           => null,
            'parent_order_id'          => null,
            'bsa'                      => $bsa,
            'crcl'                     => $crcl,
            'dose_modification_percent'=> 100,
            'dose_modification_reason' => null,
            'is_modified_protocol'     => false,
            'consultant_name'          => 'Dr. Hossam',
            'pharmacist_name'          => null,
            'nurse_name'               => null,
            'ordered_at'               => now()->subDays(2),
            'notes'                    => null,
            'status'                   => 'confirmed',
        ]);

        foreach ($protocol->protocolDrugs as $pd) {
            $dose = $calc->calculateDrugDose($pd, $bsa, $crcl, 100);
            OrderDrug::create([
                'order_id'               => $order->id,
                'protocol_drug_id'       => $pd->id,
                'drug_id'                => $pd->drug_id,
                'category'               => $pd->category,
                'calculated_dose'        => $dose['calculated'],
                'final_dose'             => $dose['final'],
                'is_included'            => true,
                'is_manually_overridden' => false,
                'override_reason'        => null,
                'cap_applied'            => $dose['cap_applied'],
                'physician_note'         => null,
                'physician_frequency'    => null,
                'physician_duration'     => null,
                'physician_dose_unit'    => null,
            ]);

            PatientCumulativeDose::updateOrCreate(
                ['patient_id' => $patient->id, 'drug_id' => $pd->drug_id],
                [
                    // 'total_dose' => DB::raw('COALESCE(total_dose, 0) + ' . $dose['final']),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('FOLFIRI Demo seeded. Order: DEMO-2026-0001, Patient: DEMO-001 (Hamdah Masoud Alghabie)');
        $this->command->info("BSA: {$bsa} m² | CrCl: {$crcl} mL/min");
    }
}
