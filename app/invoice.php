<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    //
    protected $fillable = [
        //
        
        'principal_id',
        'name',
        'uique_code',
        'email',
        'description',
        'moto_id',
        'Amount',
        'bankName',
        'bankCode',
        'invoice_number',
        'qty',
        'vat',
        'due_days',
        'due_date',
        'issue_date',  
    
        ];
}