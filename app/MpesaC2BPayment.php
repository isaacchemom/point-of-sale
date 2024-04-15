<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MpesaC2BPayment extends Model
{
    use HasFactory;

    protected $guarded = [];
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->toDateTimeString();
    }

    public function getTransTimeAttribute($value)
    {
        $time = Carbon::createFromFormat('YmdHis', $value);

        $time->setTimezone('Africa/Nairobi');
        return $time;
    }
}
