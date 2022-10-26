<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Auth;


class userExpense extends Model
{
    //

    use SoftDeletes;



    protected $fillable = [
        'expense_id',
        'principal_id',
        'user_id',
        'payable',
        'payed',
        'payed_date',
        'status',
        'split_method_id',
        'bankName',
        'bankCode',
        'account_number',
        'percentage',
        'percentage_per_user',
        'actualAmount',
        'email',
        'uique_code',
        'name',
        'description'

        
    ];

    public function UserExpense()
    {
        return $this->belongsTo(Expense::class, 'expense_id');
    }

    public function user()
    {
        return $this->hasMany(userExpense::class, 'principal_id', 'user_id');
    }

    public function Uxerexpense()
    {
        return $this->hasOne(splittingMethod::class, 'split_method_id');
    }

    protected $table = "user_expenses";

    public static function getuserExpense($request)
    {
        $dateStart = $request->input('startDate');
        $dateEnd = $request->input('endDate');
        //$Auth_user = Auth::user()->id;
        $records = userExpense::whereBetween('created_at', [$dateStart, $dateEnd])->select('Id','name', 'email', 'description', 'actualAmount', 'payable', 'bankName', 'account_number', 'created_at', 'transactionDate')->get();
        return $records;
    }
}
