<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Protocol extends Model
{
    protected $fillable = ['diagnosis_id', 'name', 'description', 'cycle_duration_days', 'tests_reminder'];

    public function diagnosis(): BelongsTo
    {
        return $this->belongsTo(Diagnosis::class);
    }

    public function protocolDrugs(): HasMany
    {
        return $this->hasMany(ProtocolDrug::class)->orderBy('sort_order');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
