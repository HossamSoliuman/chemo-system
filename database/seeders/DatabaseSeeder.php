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
        $protocols = $this->seedProtocols($diagnoses, $drugs);
        $patients = $this->seedPatients();
        $this->seedOrders($patients, $protocols, $drugs);
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

    private function seedProtocols(array $diagnoses, array $drugs): array
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

        $result = [];
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
            $result[$pData['name']] = $protocol;
        }
        return $result;
    }

    private function seedPatients(): array
    {
        $data = [
            ['mrn' => 'MRN-001', 'name' => 'Fatima Al-Rashidi',  'gender' => 'female', 'date_of_birth' => '1972-03-15', 'height_cm' => 162.0, 'weight_kg' => 68.50, 'serum_creatinine' => 72.00],
            ['mrn' => 'MRN-002', 'name' => 'Ahmed Hassan',       'gender' => 'male',   'date_of_birth' => '1958-07-22', 'height_cm' => 175.0, 'weight_kg' => 82.00, 'serum_creatinine' => 95.00],
            ['mrn' => 'MRN-003', 'name' => 'Mona Youssef',       'gender' => 'female', 'date_of_birth' => '1980-11-08', 'height_cm' => 158.0, 'weight_kg' => 60.00, 'serum_creatinine' => 65.00],
            ['mrn' => 'MRN-004', 'name' => 'Karim Nabil',        'gender' => 'male',   'date_of_birth' => '1965-05-30', 'height_cm' => 178.0, 'weight_kg' => 90.00, 'serum_creatinine' => 110.00],
            ['mrn' => 'MRN-005', 'name' => 'Layla Ibrahim',      'gender' => 'female', 'date_of_birth' => '1975-09-14', 'height_cm' => 165.0, 'weight_kg' => 72.00, 'serum_creatinine' => 80.00],
            ['mrn' => 'MRN-006', 'name' => 'Omar Sharaf',        'gender' => 'male',   'date_of_birth' => '1952-01-20', 'height_cm' => 172.0, 'weight_kg' => 75.00, 'serum_creatinine' => 135.00],
            ['mrn' => 'MRN-007', 'name' => 'Sara Mahmoud',       'gender' => 'female', 'date_of_birth' => '1988-06-03', 'height_cm' => 160.0, 'weight_kg' => 55.00, 'serum_creatinine' => 58.00],
            ['mrn' => 'MRN-008', 'name' => 'Tarek Farouk',       'gender' => 'male',   'date_of_birth' => '1970-12-11', 'height_cm' => 180.0, 'weight_kg' => 95.00, 'serum_creatinine' => 88.00],
            ['mrn' => 'MRN-009', 'name' => 'Hana Gamal',         'gender' => 'female', 'date_of_birth' => '1963-04-25', 'height_cm' => 155.0, 'weight_kg' => 64.00, 'serum_creatinine' => 92.00],
            ['mrn' => 'MRN-010', 'name' => 'Youssef Badawi',     'gender' => 'male',   'date_of_birth' => '1978-08-17', 'height_cm' => 170.0, 'weight_kg' => 78.00, 'serum_creatinine' => 76.00],
            ['mrn' => 'MRN-011', 'name' => 'Nadia Fouad',        'gender' => 'female', 'date_of_birth' => '1969-02-28', 'height_cm' => 163.0, 'weight_kg' => 70.00, 'serum_creatinine' => 85.00],
            ['mrn' => 'MRN-012', 'name' => 'Bassem Khalil',      'gender' => 'male',   'date_of_birth' => '1974-10-05', 'height_cm' => 176.0, 'weight_kg' => 85.00, 'serum_creatinine' => 78.00],
        ];

        $result = [];
        foreach ($data as $row) {
            $result[$row['mrn']] = \App\Models\Patient::create($row);
        }
        return $result;
    }

    private function seedOrders(array $patients, array $protocols, array $drugs): void
    {
        $calc = new \App\Services\ClinicalCalculationService();
        $counter = 1;

        $scenarios = [
            ['patient' => 'MRN-001', 'protocol' => 'AC (Doxorubicin + Cyclophosphamide)',  'cycles' => 4, 'days_ago' => 90,  'interval' => 21, 'status' => 'printed',    'consultant' => 'Dr. Samira Osman',   'pharmacist' => 'Pharm. Laila Saleh'],
            ['patient' => 'MRN-001', 'protocol' => 'Docetaxel (single agent)',              'cycles' => 1, 'days_ago' => 3,   'interval' => 21, 'status' => 'draft',      'consultant' => 'Dr. Samira Osman',   'pharmacist' => 'Pharm. Laila Saleh',  'notes' => 'Sequential therapy post-AC.'],
            ['patient' => 'MRN-002', 'protocol' => 'FOLFOX',                                'cycles' => 6, 'days_ago' => 85,  'interval' => 14, 'status' => 'confirmed',  'consultant' => 'Dr. Hassan Mostafa', 'pharmacist' => 'Pharm. Ahmed Rady'],
            ['patient' => 'MRN-003', 'protocol' => 'Carboplatin + Paclitaxel (TC)',         'cycles' => 3, 'days_ago' => 65,  'interval' => 21, 'status' => 'confirmed',  'consultant' => 'Dr. Maha Samy',      'pharmacist' => 'Pharm. Laila Saleh'],
            ['patient' => 'MRN-004', 'protocol' => 'R-CHOP',                                'cycles' => 4, 'days_ago' => 85,  'interval' => 21, 'status' => 'confirmed',  'consultant' => 'Dr. Hassan Mostafa', 'pharmacist' => 'Pharm. Omar Gamal'],
            ['patient' => 'MRN-005', 'protocol' => 'EC (Epirubicin + Cyclophosphamide)',    'cycles' => 3, 'days_ago' => 45,  'interval' => 21, 'status' => 'confirmed',  'consultant' => 'Dr. Samira Osman',   'pharmacist' => 'Pharm. Laila Saleh'],
            ['patient' => 'MRN-006', 'protocol' => 'Gemcitabine + Carboplatin',             'cycles' => 2, 'days_ago' => 25,  'interval' => 21, 'status' => 'confirmed',  'consultant' => 'Dr. Khalid Amer',    'pharmacist' => 'Pharm. Ahmed Rady',   'mod_reason' => 'Elevated creatinine — CrCl adjusted'],
            ['patient' => 'MRN-007', 'protocol' => 'Docetaxel (single agent)',              'cycles' => 2, 'days_ago' => 45,  'interval' => 21, 'status' => 'confirmed',  'consultant' => 'Dr. Maha Samy',      'pharmacist' => 'Pharm. Laila Saleh'],
            ['patient' => 'MRN-008', 'protocol' => 'FOLFIRI',                               'cycles' => 3, 'days_ago' => 42,  'interval' => 14, 'status' => 'confirmed',  'consultant' => 'Dr. Hassan Mostafa', 'pharmacist' => 'Pharm. Omar Gamal'],
            ['patient' => 'MRN-009', 'protocol' => 'AC (Doxorubicin + Cyclophosphamide)',  'cycles' => 2, 'days_ago' => 30,  'interval' => 21, 'status' => 'confirmed',  'consultant' => 'Dr. Samira Osman',   'pharmacist' => 'Pharm. Ahmed Rady'],
            ['patient' => 'MRN-010', 'protocol' => 'FOLFOX',                                'cycles' => 2, 'days_ago' => 28,  'interval' => 14, 'status' => 'confirmed',  'consultant' => 'Dr. Hassan Mostafa', 'pharmacist' => 'Pharm. Ahmed Rady'],
            ['patient' => 'MRN-011', 'protocol' => 'Carboplatin + Paclitaxel (TC)',         'cycles' => 4, 'days_ago' => 88,  'interval' => 21, 'status' => 'confirmed',  'consultant' => 'Dr. Maha Samy',      'pharmacist' => 'Pharm. Laila Saleh'],
            ['patient' => 'MRN-012', 'protocol' => 'R-CHOP',                                'cycles' => 2, 'days_ago' => 22,  'interval' => 21, 'status' => 'confirmed',  'consultant' => 'Dr. Hassan Mostafa', 'pharmacist' => 'Pharm. Omar Gamal'],
            ['patient' => 'MRN-003', 'protocol' => 'Docetaxel (single agent)',              'cycles' => 1, 'days_ago' => 2,   'interval' => 21, 'status' => 'draft',      'consultant' => 'Dr. Maha Samy',      'pharmacist' => 'Pharm. Laila Saleh',  'notes' => 'Continuation — cycle 4 planned.'],
        ];

        foreach ($scenarios as $scenario) {
            $patient = $patients[$scenario['patient']];
            $protocol = $protocols[$scenario['protocol']];
            $protocol->load('protocolDrugs.drug');

            $age  = $calc->calculateAge($patient->date_of_birth->toDateTime());
            $bsa  = $calc->calculateBSA($patient->height_cm, $patient->weight_kg);
            $crcl = $calc->calculateCrCl($age, $patient->weight_kg, $patient->serum_creatinine, $patient->gender);

            for ($cycle = 1; $cycle <= $scenario['cycles']; $cycle++) {
                $daysAgo   = $scenario['days_ago'] - (($cycle - 1) * $scenario['interval']);
                $orderedAt = now()->subDays($daysAgo);
                $year      = $orderedAt->year;
                $orderNum  = 'ORD-' . $year . '-' . str_pad($counter++, 4, '0', STR_PAD_LEFT);

                $isLast = $cycle === $scenario['cycles'];
                if ($scenario['status'] === 'draft') {
                    $status = $isLast ? 'draft' : 'printed';
                } else {
                    $status = $isLast ? $scenario['status'] : 'printed';
                }

                $order = \App\Models\Order::create([
                    'patient_id'               => $patient->id,
                    'protocol_id'              => $protocol->id,
                    'order_number'             => $orderNum,
                    'cycle_number'             => $cycle,
                    'is_same_cycle'            => false,
                    'parent_order_id'          => null,
                    'bsa'                      => $bsa,
                    'crcl'                     => $crcl,
                    'dose_modification_percent' => 100,
                    'dose_modification_reason' => $scenario['mod_reason'] ?? null,
                    'is_modified_protocol'     => false,
                    'consultant_name'          => $scenario['consultant'],
                    'pharmacist_name'          => $scenario['pharmacist'],
                    'nurse_name'               => null,
                    'ordered_at'               => $orderedAt,
                    'notes'                    => $scenario['notes'] ?? null,
                    'status'                   => $status,
                ]);

                foreach ($protocol->protocolDrugs as $pd) {
                    $dose = $calc->calculateDrugDose($pd, $bsa, $crcl, 100);
                    \App\Models\OrderDrug::create([
                        'order_id'             => $order->id,
                        'protocol_drug_id'     => $pd->id,
                        'drug_id'              => $pd->drug_id,
                        'category'             => $pd->category,
                        'calculated_dose'      => $dose['calculated'],
                        'final_dose'           => $dose['final'],
                        'is_included'          => true,
                        'is_manually_overridden' => false,
                        'override_reason'      => null,
                        'cap_applied'          => $dose['cap_applied'],
                    ]);

                    if ($status !== 'draft') {
                        \App\Models\PatientCumulativeDose::updateOrCreate(
                            ['patient_id' => $patient->id, 'drug_id' => $pd->drug_id],
                            [
                                'total_dose' => \Illuminate\Support\Facades\DB::raw('COALESCE(total_dose, 0) + ' . $dose['final']),
                                'updated_at' => $orderedAt,
                            ]
                        );
                    }
                }
            }
        }
    }
}
