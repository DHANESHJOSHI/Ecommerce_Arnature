<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'pin_code',
        'state_id',
        'is_active',
    ];
    
    public function scopeIsActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
