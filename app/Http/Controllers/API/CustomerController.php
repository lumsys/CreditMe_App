<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CustomerRequest;
use App\Customer;
use App\Business;

class CustomerController extends Controller
{
    //

    public function createCustomer(CustomerRequest $request, $business_code)
    {

        $business_code = Business::where('business_code', $business_code)->select('business_code')->first();

       // return $business_code;

        $business = new Customer([
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'customer_code' => $business_code->business_code,
            'owner_id' => Auth::user()->id,
            
        ]);
        $business->save();

            
        return response()->json($business);

    }


    public function updateCustomer(Request $request, $id)
{
    $getAdmin = Auth::user();
     $getAd = $getAdmin -> usertype;
         if($getAd === 'merchant')
            {
                $update = Customer::find($id);
                $update->update($request->all());
            return response()->json($update);
            }
            else{
            return response()->json('You are not authorize to perform this action');
            }

}


}
