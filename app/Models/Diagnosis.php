<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Diagnosis extends Model
{
    protected $fillable = ['name', 'icd_code', 'description'];

    public function protocols(): HasMany
    {
        return $this->hasMany(Protocol::class);
    }
}
