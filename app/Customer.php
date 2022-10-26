<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    //

    protected $fillable = [
        'customer_name',
        'customer_code',
        'customer_email',
        'customer_phone'
        
    ];
}
