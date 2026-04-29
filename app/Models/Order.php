<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'patient_id', 'protocol_id', 'order_number', 'cycle_number',
        'is_same_cycle', 'parent_order_id', 'bsa', 'crcl',
        'dose_modification_percent', 'dose_modification_reason',
        'is_modified_protocol', 'consultant_name', 'pharmacist_name',
        'nurse_name', 'ordered_at', 'notes', 'status',
    ];

    protected $casts = [
        'is_same_cycle' => 'boolean',
        'is_modified_protocol' => 'boolean',
        'ordered_at' => 'datetime',
        'bsa' => 'decimal:4',
        'crcl' => 'decimal:4',
        'dose_modification_percent' => 'decimal:2',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function protocol(): BelongsTo
    {
        return $this->belongsTo(Protocol::class);
    }

    public function parentOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'parent_order_id');
    }

    public function orderDrugs(): HasMany
    {
        return $this->hasMany(OrderDrug::class);
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (Order $order) {
            if (empty($order->order_number)) {
                $year = now()->year;
                $count = static::whereYear('created_at', $year)->count() + 1;
                $order->order_number = 'ORD-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
