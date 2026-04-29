<?php

namespace App\Services;

use App\Models\Drug;
use App\Models\Order;
use App\Models\Patient;
use App\Models\Protocol;
use App\Models\ProtocolDrug;
use Illuminate\Support\Collection;

class ClinicalCalculationService
{
    public function calculateBSA(float $height_cm, float $weight_kg): float
    {
        return round(sqrt(($height_cm * $weight_kg) / 3600), 4);
    }

    public function calculateCrCl(int $age, float $weight_kg, float $creatinine_umol, string $gender): float
    {
        $creatinine_mgdl = $creatinine_umol / 88.42;

        $crcl = ((140 - $age) * $weight_kg) / (72 * $creatinine_mgdl);
        if ($gender === 'female') {
            $crcl *= 0.85;
        }
        return round(max($crcl, 0), 4);
    }

    public function calculateCarboplatin(float $target_auc, float $crcl): float
    {
        return round($target_auc * ($crcl + 25), 4);
    }

    public function calculateAge(\DateTime $dob): int
    {
        return $dob->diff(new \DateTime())->y;
    }

    public function calculateDrugDose(ProtocolDrug $pd, float $bsa, float $crcl, float $modification_pct = 100): array
    {
        $calculated = 0;

        switch ($pd->dose_type) {
            case 'bsa_based':
                $calculated = $pd->dose_per_unit * $bsa;
                break;
            case 'weight_based':
                $calculated = $pd->dose_per_unit * $bsa;
                break;
            case 'crcl_based':
                $calculated = $pd->dose_per_unit * $crcl;
                break;
            case 'carboplatin_calvert':
                $calculated = $this->calculateCarboplatin($pd->target_auc, $crcl);
                break;
            case 'fixed':
                $calculated = $pd->fixed_dose;
                break;
        }

        if (in_array($pd->dose_type, ['bsa_based', 'weight_based', 'crcl_based', 'carboplatin_calvert'])) {
            $final = round($calculated * ($modification_pct / 100), 4);
        } else {
            $final = round($calculated, 4);
        }

        $cap_applied = false;
        if ($pd->per_cycle_cap && $final > $pd->per_cycle_cap) {
            $final = $pd->per_cycle_cap;
            $cap_applied = true;
        }

        return [
            'calculated' => round($calculated, 4),
            'final' => $final,
            'cap_applied' => $cap_applied,
        ];
    }

    public function checkLifetimeCaps(Patient $patient, Collection $orderDrugs): array
    {
        $warnings = [];

        foreach ($orderDrugs as $item) {
            $pd = $item['protocol_drug'];
            if (!$pd->lifetime_cap) {
                continue;
            }

            $cumulative = $patient->cumulativeDoses()
                ->where('drug_id', $pd->drug_id)
                ->value('total_dose') ?? 0;

            $newDose = $item['final'];
            $projectedTotal = $cumulative + $newDose;

            if ($projectedTotal > $pd->lifetime_cap) {
                $warnings[] = [
                    'drug' => $pd->drug,
                    'current_total' => $cumulative,
                    'new_dose' => $newDose,
                    'cap' => $pd->lifetime_cap,
                    'cap_unit' => $pd->lifetime_cap_unit,
                    'exceeded' => true,
                ];
            }
        }

        return $warnings;
    }

    public function determineCycleNumber(Patient $patient, Protocol $protocol): array
    {
        $recentOrder = Order::where('patient_id', $patient->id)
            ->where('protocol_id', $protocol->id)
            ->where('status', 'confirmed')
            ->where('ordered_at', '>=', now()->subDays(6))
            ->orderByDesc('ordered_at')
            ->first();

        if ($recentOrder) {
            return [
                'cycle_number' => $recentOrder->cycle_number,
                'is_same_cycle' => true,
                'parent_order_id' => $recentOrder->id,
            ];
        }

        $lastOrder = Order::where('patient_id', $patient->id)
            ->where('protocol_id', $protocol->id)
            ->where('status', 'confirmed')
            ->max('cycle_number');

        return [
            'cycle_number' => ($lastOrder ?? 0) + 1,
            'is_same_cycle' => false,
            'parent_order_id' => null,
        ];
    }
}
