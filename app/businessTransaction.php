<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class businessTransaction extends Model
{
    //
    protected $fillable = [
        //
        
                'principal_id',
                'name',
                'unique_code',
                'email',
                'amount',
                'description',
                'moto_id',
                'bankName',
                'bankCode',
                'account_number',
        
    
        ];
}