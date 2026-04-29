<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientCumulativeDose extends Model
{
    public $timestamps = false;

    protected $fillable = ['patient_id', 'drug_id', 'total_dose', 'updated_at'];

    protected $casts = [
        'total_dose' => 'decimal:4',
        'updated_at' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function drug(): BelongsTo
    {
        return $this->belongsTo(Drug::class);
    }
}
