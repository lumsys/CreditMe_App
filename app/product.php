<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class product extends Model
{
    //

    protected $fillable = [
        //
        
        'name',
        'description',
        'unique_code',
        'category_id',
        'subcategory_id',
        'amount',
        'user_id'
    
        ];
}