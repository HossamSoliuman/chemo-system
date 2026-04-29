<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Drug extends Model
{
    protected $fillable = ['name', 'unit', 'description'];

    public function protocolDrugs(): HasMany
    {
        return $this->hasMany(ProtocolDrug::class);
    }

    public function orderDrugs(): HasMany
    {
        return $this->hasMany(OrderDrug::class);
    }
}
