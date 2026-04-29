<?php

namespace Database\Seeders;

use App\Models\Diagnosis;
use App\Models\Drug;
use App\Models\Protocol;
use App\Models\ProtocolDrug;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    
    public function run(): void
    {
        $drugs = $this->seedDrugs();
        $diagnoses = $this->seedDiagnoses();
        $this->seedProtocols($diagnoses, $drugs);
    }

    private function seedDrugs(): array
    {
        $drugData = [
            ['name' => 'Doxorubicin', 'unit' => 'mg', 'description' => 'Anthracycline antibiotic — lifetime cap 550 mg/m²'],
            ['name' => 'Cyclophosphamide', 'unit' => 'mg'],
            ['name' => 'Fluorouracil (5-FU)', 'unit' => 'mg'],
            ['name' => 'Methotrexate', 'unit' => 'mg'],
            ['name' => 'Carboplatin', 'unit' => 'mg'],
            ['name' => 'Paclitaxel', 'unit' => 'mg'],
            ['name' => 'Docetaxel', 'unit' => 'mg'],
            ['name' => 'Cisplatin', 'unit' => 'mg'],
            ['name' => 'Gemcitabine', 'unit' => 'mg'],
            ['name' => 'Oxaliplatin', 'unit' => 'mg'],
            ['name' => 'Irinotecan', 'unit' => 'mg'],
            ['name' => 'Vincristine', 'unit' => 'mg'],
            ['name' => 'Etoposide', 'unit' => 'mg'],
            ['name' => 'Epirubicin', 'unit' => 'mg', 'description' => 'Anthracycline — lifetime cap 900 mg/m²'],
            ['name' => 'Capecitabine', 'unit' => 'mg'],
            ['name' => 'Ondansetron', 'unit' => 'mg'],
            ['name' => 'Granisetron', 'unit' => 'mg'],
            ['name' => 'Dexamethasone', 'unit' => 'mg'],
            ['name' => 'Aprepitant', 'unit' => 'mg'],
            ['name' => 'Mesna', 'unit' => 'mg'],
            ['name' => 'Filgrastim (G-CSF)', 'unit' => 'mcg'],
            ['name' => 'Leucovorin (Folinic Acid)', 'unit' => 'mg'],
            ['name' => 'Bevacizumab', 'unit' => 'mg'],
            ['name' => 'Rituximab', 'unit' => 'mg'],
            ['name' => 'Trastuzumab', 'unit' => 'mg'],
        ];

        $result = [];
        foreach ($drugData as $data) {
            $result[$data['name']] = Drug::create($data);
        }
        return $result;
    }

    private function seedDiagnoses(): array
    {
        $data = [
            ['name' => 'Breast Cancer', 'icd_code' => 'C50'],
            ['name' => 'Non-Hodgkin Lymphoma', 'icd_code' => 'C85'],
            ['name' => 'Colorectal Cancer', 'icd_code' => 'C18'],
            ['name' => 'Lung Cancer (NSCLC)', 'icd_code' => 'C34'],
            ['name' => 'Ovarian Cancer', 'icd_code' => 'C56'],
            ['name' => 'Gastric Cancer', 'icd_code' => 'C16'],
        ];
        $result = [];
        foreach ($data as $d) {
            $result[$d['name']] = Diagnosis::create($d);
        }
        return $result;
    }

    private function seedProtocols(array $diagnoses, array $drugs): void
    {
        $protocols = [
            [
                'name' => 'AC (Doxorubicin + Cyclophosphamide)',
                'diagnosis' => 'Breast Cancer',
                'cycle_duration_days' => 21,
                'description' => 'Standard AC regimen for breast cancer. 4 cycles.',
                'drugs' => [
                    ['name' => 'Ondansetron', 'category' => 'pre_medication', 'dose_type' => 'fixed', 'fixed_dose' => 8, 'route' => 'IV', 'frequency' => 'Day 1, 30 min before chemo'],
                    ['name' => 'Dexamethasone', 'category' => 'pre_medication', 'dose_type' => 'fixed', 'fixed_dose' => 8, 'route' => 'IV', 'frequency' => 'Day 1, 30 min before chemo'],
                    ['name' => 'Doxorubicin', 'category' => 'chemotherapy', 'dose_type' => 'bsa_based', 'dose_per_unit' => 60, 'route' => 'IV', 'frequency' => 'Day 1', 'lifetime_cap' => 550, 'lifetime_cap_unit' => 'mg/m²'],
                    ['name' => 'Cyclophosphamide', 'category' => 'chemotherapy', 'dose_type' => 'bsa_based', 'dose_per_unit' => 600, 'route' => 'IV', 'frequency' => 'Day 1'],
                    ['name' => 'Mesna', 'category' => 'post_medication', 'dose_type' => 'bsa_based', 'dose_per_unit' => 400, 'route' => 'IV', 'frequency' => 'Day 1, given with and 4h after CTX'],
                ],
            ],
            [
                'name' => 'EC (Epirubicin + Cyclophosphamide)',
                'diagnosis' => 'Breast Cancer',
                'cycle_duration_days' => 21,
                'description' => 'EC regimen for breast cancer.',
                'drugs' => [
                    ['name' => 'Ondansetron', 'category' => 'pre_medication', 'dose_type' => 'fixed', 'fixed_dose' => 8, 'route' => 'IV', 'frequency' => 'Day 1'],
                    ['name' => 'Dexamethasone', 'category' => 'pre_medication', 'dose_type' => 'fixed', 'fixed_dose' => 8, 'route' => 'IV', 'frequency' => 'Day 1'],
                    ['name' => 'Epirubicin', 'category' => 'chemotherapy', 'dose_type' => 'bsa_based', 'dose_per_unit' => 90, 'route' => 'IV', 'frequency' => 'Day 1', 'lifetime_cap' => 900, 'lifetime_cap_unit' => 'mg/m²'],
                    ['name' => 'Cyclophosphamide', 'category' => 'chemotherapy', 'dose_type' => 'bsa_based', 'dose_per_unit' => 600, 'route' => 'IV', 'frequency' => 'Day 1'],
                ],
            ],
            [
                'name' => 'R-CHOP',
                'diagnosis' => 'Non-Hodgkin Lymphoma',
                'cycle_duration_days' => 21,
                'description' => 'R-CHOP regimen for B-cell NHL.',
                'drugs' => [
                    ['name' => 'Ondansetron', 'category' => 'pre_medication', 'dose_type' => 'fixed', 'fixed_dose' => 8, 'route' => 'IV', 'frequency' => 'Day 1'],
                    ['name' => 'Dexamethasone', 'category' => 'pre_medication', 'dose_type' => 'fixed', 'fixed_dose' => 8, 'route' => 'IV', 'frequency' => 'Day 1'],
                    ['name' => 'Rituximab', 'category' => 'chemotherapy', 'dose_type' => 'bsa_based', 'dose_per_unit' => 375, 'route' => 'IV', 'frequency' => 'Day 1'],
                    ['name' => 'Cyclophosphamide', 'category' => 'chemotherapy', 'dose_type' => 'bsa_based', 'dose_per_unit' => 750, 'route' => 'IV', 'frequency' => 'Day 1'],
                    ['name' => 'Doxorubicin', 'category' => 'chemotherapy', 'dose_type' => 'bsa_based', 'dose_per_unit' => 50, 'route' => 'IV', 'frequency' => 'Day 1', 'lifetime_cap' => 550, 'lifetime_cap_unit' => 'mg/m²'],
                    ['name' => 'Vincristine', 'category' => 'chemotherapy', 'dose_type' => 'fixed', 'fixed_dose' => 1.4, 'per_cycle_cap' => 2, 'per_cycle_cap_unit' => 'mg', 'route' => 'IV', 'frequency' => 'Day 1'],
                    ['name' => 'Dexamethasone', 'category' => 'chemotherapy', 'dose_type' => 'fixed', 'fixed_dose' => 40, 'route' => 'PO', 'frequency' => 'Days 1–5'],
                ],
            ],
            [
                'name' => 'FOLFOX',
                'diagnosis' => 'Colorectal Cancer',
                'cycle_duration_days' => 14,
                'description' => 'FOLFOX-4 for colorectal cancer.',
                'drugs' => [
                    ['name' => 'Ondansetron', 'category' => 'pre_medication', 'dose_type' => 'fixed', 'fixed_dose' => 8, 'route' => 'IV', 'frequency' => 'Day 1'],
                    ['name' => 'Dexamethasone', 'category' => 'pre_medication', 'dose_type' => 'fixed', 'fixed_dose' => 8, 'route' => 'IV', 'frequency' => 'Day 1'],
                    ['name' => 'Oxaliplatin', 'category' => 'chemotherapy', 'dose_type' => 'bsa_based', 'dose_per_unit' => 85, 'route' => 'IV', 'frequency' => 'Day 1'],
                    ['name' => 'Leucovorin (Folinic Acid)', 'category' => 'chemotherapy', 'dose_type' => 'bsa_based', 'dose_per_unit' => 200, 'route' => 'IV', 'frequency' => 'Days 1–2'],
                    ['name' => 'Fluorouracil (5-FU)', 'category' => 'chemotherapy', 'dose_type' => 'bsa_based', 'dose_per_unit' => 400, 'route' => 'IV', 'frequency' => 'Day 1 bolus, then 600 mg/m² infusion Days 1–2'],
                ],
            ],
            [
                'name' => 'FOLFIRI',
                'diagnosis' => 'Colorectal Cancer',
                'cycle_duration_days' => 14,
                'description' => 'FOLFIRI for colorectal cancer (2nd line).',
                'drugs' => [
                    ['name' => 'Ondansetron', 'category' => 'pre_medication', 'dose_type' => 'fixed', 'fixed_dose' => 8, 'route' => 'IV', 'frequency' => 'Day 1'],
                    ['name' => 'Dexamethasone', 'category' => 'pre_medication', 'dose_type' => 'fixed', 'fixed_dose' => 8, 'route' => 'IV', 'frequency' => 'Day 1'],
                    ['name' => 'Irinotecan', 'category' => 'chemotherapy', 'dose_type' => 'bsa_based', 'dose_per_unit' => 180, 'route' => 'IV', 'frequency' => 'Day 1'],
                    ['name' => 'Leucovorin (Folinic Acid)', 'category' => 'chemotherapy', 'dose_type' => 'bsa_based', 'dose_per_unit' => 400, 'route' => 'IV', 'frequency' => 'Day 1'],
                    ['name' => 'Fluorouracil (5-FU)', 'category' => 'chemotherapy', 'dose_type' => 'bsa_based', 'dose_per_unit' => 400, 'route' => 'IV', 'frequency' => 'Day 1 bolus, then 2400 mg/m² over 46h'],
                ],
            ],
            [
                'name' => 'Carboplatin + Paclitaxel (TC)',
                'diagnosis' => 'Ovarian Cancer',
                'cycle_duration_days' => 21,
                'description' => 'Carboplatin AUC 5 + Paclitaxel — standard for ovarian cancer.',
                'drugs' => [
                    ['name' => 'Dexamethasone', 'category' => 'pre_medication', 'dose_type' => 'fixed', 'fixed_dose' => 20, 'route' => 'IV', 'frequency' => 'Day 1, 30 min before paclitaxel'],
                    ['name' => 'Ondansetron', 'category' => 'pre_medication', 'dose_type' => 'fixed', 'fixed_dose' => 8, 'route' => 'IV', 'frequency' => 'Day 1'],
                    ['name' => 'Paclitaxel', 'category' => 'chemotherapy', 'dose_type' => 'bsa_based', 'dose_per_unit' => 175, 'route' => 'IV', 'frequency' => 'Day 1 over 3h'],
                    ['name' => 'Carboplatin', 'category' => 'chemotherapy', 'dose_type' => 'carboplatin_calvert', 'target_auc' => 5, 'route' => 'IV', 'frequency' => 'Day 1 after paclitaxel'],
                ],
            ],
            [
                'name' => 'Gemcitabine + Carboplatin',
                'diagnosis' => 'Lung Cancer (NSCLC)',
                'cycle_duration_days' => 21,
                'description' => 'Gemcitabine + Carboplatin for NSCLC.',
                'drugs' => [
                    ['name' => 'Granisetron', 'category' => 'pre_medication', 'dose_type' => 'fixed', 'fixed_dose' => 3, 'route' => 'IV', 'frequency' => 'Days 1 & 8'],
                    ['name' => 'Dexamethasone', 'category' => 'pre_medication', 'dose_type' => 'fixed', 'fixed_dose' => 8, 'route' => 'IV', 'frequency' => 'Days 1 & 8'],
                    ['name' => 'Gemcitabine', 'category' => 'chemotherapy', 'dose_type' => 'bsa_based', 'dose_per_unit' => 1250, 'route' => 'IV', 'frequency' => 'Days 1 & 8'],
                    ['name' => 'Carboplatin', 'category' => 'chemotherapy', 'dose_type' => 'carboplatin_calvert', 'target_auc' => 5, 'route' => 'IV', 'frequency' => 'Day 1 only'],
                ],
            ],
            [
                'name' => 'Docetaxel (single agent)',
                'diagnosis' => 'Breast Cancer',
                'cycle_duration_days' => 21,
                'description' => 'Single-agent Docetaxel for breast cancer.',
                'drugs' => [
                    ['name' => 'Dexamethasone', 'category' => 'pre_medication', 'dose_type' => 'fixed', 'fixed_dose' => 8, 'route' => 'PO', 'frequency' => 'Twice daily for 3 days starting the day before'],
                    ['name' => 'Ondansetron', 'category' => 'pre_medication', 'dose_type' => 'fixed', 'fixed_dose' => 8, 'route' => 'IV', 'frequency' => 'Day 1'],
                    ['name' => 'Docetaxel', 'category' => 'chemotherapy', 'dose_type' => 'bsa_based', 'dose_per_unit' => 100, 'route' => 'IV', 'frequency' => 'Day 1 over 1h'],
                    ['name' => 'Filgrastim (G-CSF)', 'category' => 'post_medication', 'dose_type' => 'fixed', 'fixed_dose' => 300, 'route' => 'SC', 'frequency' => 'Days 5–14'],
                ],
            ],
        ];

        foreach ($protocols as $pData) {
            $diagnosis = $diagnoses[$pData['diagnosis']];
            $protocol = Protocol::create([
                'name' => $pData['name'],
                'diagnosis_id' => $diagnosis->id,
                'description' => $pData['description'] ?? null,
                'cycle_duration_days' => $pData['cycle_duration_days'],
            ]);

            foreach ($pData['drugs'] as $index => $drugData) {
                $drug = $drugs[$drugData['name']];
                ProtocolDrug::create([
                    'protocol_id' => $protocol->id,
                    'drug_id' => $drug->id,
                    'category' => $drugData['category'],
                    'dose_type' => $drugData['dose_type'],
                    'dose_per_unit' => $drugData['dose_per_unit'] ?? null,
                    'fixed_dose' => $drugData['fixed_dose'] ?? null,
                    'target_auc' => $drugData['target_auc'] ?? null,
                    'per_cycle_cap' => $drugData['per_cycle_cap'] ?? null,
                    'per_cycle_cap_unit' => $drugData['per_cycle_cap_unit'] ?? null,
                    'lifetime_cap' => $drugData['lifetime_cap'] ?? null,
                    'lifetime_cap_unit' => $drugData['lifetime_cap_unit'] ?? null,
                    'route' => $drugData['route'] ?? null,
                    'frequency' => $drugData['frequency'] ?? null,
                    'notes' => $drugData['notes'] ?? null,
                    'sort_order' => $index,
                ]);
            }
        }
    }
}
