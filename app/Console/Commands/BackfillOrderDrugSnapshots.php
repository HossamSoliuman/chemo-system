<?php

namespace App\Console\Commands;

use App\Models\OrderDrug;
use Illuminate\Console\Command;

class BackfillOrderDrugSnapshots extends Command
{
    protected $signature = 'order-drugs:backfill-snapshots';

    protected $description = 'Backfill snapshot columns on order_drugs for existing records that still have a valid protocol_drug_id';

    public function handle(): int
    {
        $rows = OrderDrug::with(['drug', 'protocolDrug.drug'])
            ->whereNotNull('protocol_drug_id')
            ->get();

        $count = 0;

        foreach ($rows as $od) {
            $pd = $od->protocolDrug;
            if (!$pd) {
                continue;
            }

            $od->update([
                'snapshot_drug_name'          => $pd->drug->name,
                'snapshot_dose_type'          => $pd->dose_type,
                'snapshot_dose_per_unit'      => $pd->dose_per_unit,
                'snapshot_dose_label'         => $pd->dose_label,
                'snapshot_route'              => $pd->route,
                'snapshot_frequency'          => $pd->frequency,
                'snapshot_duration_days'      => $pd->duration_days,
                'snapshot_notes'              => $pd->notes,
                'snapshot_target_auc'         => $pd->target_auc,
                'snapshot_per_cycle_cap'      => $pd->per_cycle_cap,
                'snapshot_per_cycle_cap_unit' => $pd->per_cycle_cap_unit,
                'snapshot_lifetime_cap'       => $pd->lifetime_cap,
                'snapshot_lifetime_cap_unit'  => $pd->lifetime_cap_unit,
            ]);

            $count++;
        }

        $this->info("Backfilled {$count} order drug records.");

        return Command::SUCCESS;
    }
}
