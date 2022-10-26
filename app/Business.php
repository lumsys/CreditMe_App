<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    //

    protected $fillable = [
        'business_name',
        'business_code',
        'owner_id',
        'business_email',
        'business_address',
        'business_logo'
        
        
    ];
}
