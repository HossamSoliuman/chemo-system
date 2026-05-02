<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    protected $fillable = [
        'mrn', 'name', 'gender', 'nationality', 'date_of_birth',
        'height_cm', 'weight_kg', 'serum_creatinine',
        'consultant_in_charge', 'pregnant', 'lactating',
        'has_allergy', 'allergy_details',
        'cancer_stage', 'ecog_status', 'chemo_setting',
    ];

    protected $casts = [
        'date_of_birth'  => 'date',
        'height_cm'      => 'decimal:1',
        'weight_kg'      => 'decimal:2',
        'serum_creatinine' => 'decimal:2',
        'has_allergy'    => 'boolean',
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
