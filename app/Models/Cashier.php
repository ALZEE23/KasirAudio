<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cashier extends Model
{
    protected $fillable = [
        'buyer_id',
        'total_buy',
        'quantity',
        'capital'
    ];

    public function buyer()
    {
        return $this->belongsTo(Buyer::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
