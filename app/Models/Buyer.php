<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buyer extends Model
{
    protected $fillable = [
        'name',
        'phone_number',
        'car_number',
        'car_type'
    ];

    public function cashiers()
    {
        return $this->hasMany(Cashier::class);
    }
}
