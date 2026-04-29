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
}
