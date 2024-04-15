<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MpesaTransaction extends Model
{
    protected $fillable = [
        'status',
        'phone',
        'cashier_id',
        'total_amount',
    ];
    public function payment()
    {
        return $this->hasOne(MpesaPayment::class, 'payment_id');
    }
}
