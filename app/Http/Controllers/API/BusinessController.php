<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\BusinessRequest;
use App\Business;
use Illuminate\Support\Str;
use Auth;

class BusinessController extends Controller
{
    //

    public function createBusiness(Request $request)
    {

            $business = new Business([
            'business_name' => $request->business_name,
            'business_email' => $request->business_email,
            'business_code' => Str::random(10),
            'business_address' => $request->business_address,
            'owner_id' => Auth::user()->id,
        ]);
            if($request->business_logo && $request->business_logo->isValid())
            {
                $file_name = time().'.'.$request->business_logo->extension();
                $request->business_logo->move(public_path('images'),$file_name);
                $path = "images/$file_name";
                $business->business_logo = 'http://127.0.0.1:8000/'.$path;
            }

            $business->save();
            
        return response()->json($business);

     }

     public function updateBusiness(Request $request, $id)
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

public function listAllCustomer()
{
  $pageNumber = 50;
  $business_code = Business::where('business_code', $business_code)->select('business_code')->first();
  $getAllCustomer = Customer::where('customer_code', $business_code)->latest()->paginate($pageNumber);
  return response()->json($getUserExpenses);

}





}
