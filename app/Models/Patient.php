<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    protected $fillable = [
        'mrn', 'name', 'gender', 'date_of_birth',
        'height_cm', 'weight_kg', 'serum_creatinine',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'height_cm' => 'decimal:1',
        'weight_kg' => 'decimal:2',
        'serum_creatinine' => 'decimal:3',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function cumulativeDoses(): HasMany
    {
        return $this->hasMany(PatientCumulativeDose::class);
    }

    public function getAgeAttribute(): int
    {
        return $this->date_of_birth->age;
    }
}
