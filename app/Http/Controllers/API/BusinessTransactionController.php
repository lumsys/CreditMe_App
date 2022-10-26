<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ExpenseRequest;
use App\Product;
use App\Customer;
use App\businessTransaction;
use App\Invoice;
use Auth;
use Illuminate\Support\Str;

class BusinessTransactionController extends Controller
{
    //

    public function creatProduct(Request $request)
    {

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'unique_code' => Str::random(10),
            'category_id' => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
            'amount' => $request->amount,
            'user_id' => Auth::user()->id
        ]);

        return response()->json($product);

    }


    public function startBusinessTransaction(Request $request, $product_id)
    {
      //return response()->json($request->input());
      $product = Product::findOrFail($product_id);
      $input['email'] = $request->input('email');
      $emails = $request->email;
      if($emails)
      {
      $emailArray = (explode(';', $emails));
      $count = count($emailArray);
      foreach ($emailArray as $key => $em) {
          //process each user here as each iteration gives you each email
      $user = Customer::where('customer_email', $em)->first();
     $user->customer_email;
        if($request['moto_id'] == 1)
        {
            $info = businessTransaction::create([
                'principal_id' => Auth::user()->id,
                'name' => $product->name,
                'unique_code' => $product->uique_code,
                'email' => $user->customer_email,
                'amount' => $product->amount,
                'description' => $product['description'],
                'moto_id' => $request['moto_id'],
                'bankName' => $request['bankName'],
                'bankCode' => $request['bankCode'],
                'account_number' => $request['account_number'],
              ]);
            $info->save();

            $businessTrans = businessTransaction::find($product_id);
            $cusEmail = $businessTrans->email;
            $getUser = Customer::where('customer_email', $cusEmail)->first();
            return response()->json(["status" => "success", $info, $getUser], 200);
        }elseif($request['moto_id'] == 2)
        {
            $latest = Invoice::latest()->first();
            if (!$latest) {
                $string = 0;
                }
            $string = $latest->invoice_number +1;
            return $string;
            
            $current = \Carbon\Carbon::now();
            $invoiceMeth = Invoice::create([
                'principal_id' => Auth::id(),
                'name' => $product->name,
                'uique_code' => $product->unique_code,
                'email' => $em,
                'description' => $product['description'],
                'moto_id' => $request['moto_id'],
                'Amount' => $product->amount,
                'bankName' => $request['bankName'],
                'bankCode' => $request['bankCode'],
                'invoice_number' => $string,
                'qty' => $request->qty,
                'vat' => $request->amount*10/2,
                'due_days' => $request->due_days,
                'due_date' => $current->addDays($request->due_days),
                'issue_date' => \Carbon\Carbon::now(),      
        ]);
        $invoiceMeth->save();

        $InvoiceTrans = Invoice::find($product_id);
        $cusInvoEmail = $businessTrans->em;
        $getUserInvo = User::where('email', $cusInvoEmail)->first();

        return response()->json(["status" => "success", $invoiceMeth, $getUserInvo], 200);
           
    }
}
}
}

}
 
 