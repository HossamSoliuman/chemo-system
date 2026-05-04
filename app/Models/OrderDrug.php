<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDrug extends Model
{
    protected $fillable = [
        'order_id', 'protocol_drug_id', 'drug_id', 'category',
        'calculated_dose', 'final_dose', 'is_included',
        'is_manually_overridden', 'override_reason', 'cap_applied',
        'physician_note', 'physician_frequency', 'physician_duration', 'physician_dose_unit',
        'snapshot_drug_name', 'snapshot_dose_type', 'snapshot_dose_per_unit',
        'snapshot_dose_label', 'snapshot_route', 'snapshot_frequency',
        'snapshot_duration_days', 'snapshot_notes', 'snapshot_target_auc',
        'snapshot_per_cycle_cap', 'snapshot_per_cycle_cap_unit',
        'snapshot_lifetime_cap', 'snapshot_lifetime_cap_unit',
    ];

    protected $casts = [
        'is_included' => 'boolean',
        'is_manually_overridden' => 'boolean',
        'cap_applied' => 'boolean',
        'calculated_dose' => 'decimal:4',
        'final_dose' => 'decimal:4',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function protocolDrug(): BelongsTo
    {
        return $this->belongsTo(ProtocolDrug::class);
    }

    public function drug(): BelongsTo
    {
        return $this->belongsTo(Drug::class);
    }

    public function getEffectiveDrugNameAttribute(): string
    {
        return $this->snapshot_drug_name ?? $this->drug?->name ?? '';
    }

    public function getEffectiveRouteAttribute(): ?string
    {
        return $this->snapshot_route ?? $this->protocolDrug?->route;
    }

    public function getEffectiveFrequencyAttribute(): ?string
    {
        return $this->snapshot_frequency ?? $this->protocolDrug?->frequency;
    }

    public function getEffectiveDurationDaysAttribute(): ?int
    {
        return $this->snapshot_duration_days ?? $this->protocolDrug?->duration_days;
    }

    public function getEffectiveNotesAttribute(): ?string
    {
        return $this->snapshot_notes ?? $this->protocolDrug?->notes;
    }

    public function getEffectiveDoseTypeAttribute(): ?string
    {
        return $this->snapshot_dose_type ?? $this->protocolDrug?->dose_type;
    }

    public function getEffectiveDosePerUnitAttribute(): ?float
    {
        return $this->snapshot_dose_per_unit ?? $this->protocolDrug?->dose_per_unit;
    }

    public function getEffectiveDoseLabelAttribute(): ?string
    {
        return $this->snapshot_dose_label ?? $this->protocolDrug?->dose_label;
    }

    public function getEffectiveTargetAucAttribute(): ?float
    {
        return $this->snapshot_target_auc ?? $this->protocolDrug?->target_auc;
    }

    public function getEffectivePerCycleCapAttribute(): ?float
    {
        return $this->snapshot_per_cycle_cap ?? $this->protocolDrug?->per_cycle_cap;
    }

    public function getEffectivePerCycleCapUnitAttribute(): ?string
    {
        return $this->snapshot_per_cycle_cap_unit ?? $this->protocolDrug?->per_cycle_cap_unit;
    }

    public function getEffectiveLifetimeCapAttribute(): ?float
    {
        return $this->snapshot_lifetime_cap ?? $this->protocolDrug?->lifetime_cap;
    }

    public function getEffectiveLifetimeCapUnitAttribute(): ?string
    {
        return $this->snapshot_lifetime_cap_unit ?? $this->protocolDrug?->lifetime_cap_unit;
    }
}
