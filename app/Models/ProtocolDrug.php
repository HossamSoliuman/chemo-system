<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProtocolDrug extends Model
{
    protected $fillable = [
        'protocol_id', 'drug_id', 'category', 'dose_type',
        'dose_per_unit', 'fixed_dose', 'target_auc',
        'per_cycle_cap', 'per_cycle_cap_unit',
        'lifetime_cap', 'lifetime_cap_unit',
        'route', 'frequency', 'notes', 'sort_order',
    ];

    protected $casts = [
        'dose_per_unit' => 'decimal:4',
        'fixed_dose' => 'decimal:4',
        'target_auc' => 'decimal:2',
        'per_cycle_cap' => 'decimal:4',
        'lifetime_cap' => 'decimal:4',
    ];

    public function protocol(): BelongsTo
    {
        return $this->belongsTo(Protocol::class);
    }

    public function drug(): BelongsTo
    {
        return $this->belongsTo(Drug::class);
    }

    public function orderDrugs(): HasMany
    {
        return $this->hasMany(OrderDrug::class);
    }
}
