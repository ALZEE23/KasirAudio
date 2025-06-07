<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'product_id',
        'cashier_id',
        'quantity'
    ];

    public function cashier()
    {
        return $this->belongsTo(Cashier::class);
    }
}
