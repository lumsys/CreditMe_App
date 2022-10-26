<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\BankRequest;
use App\Bank;
use Auth;
use Illuminate\Support\Facades\Http;

class BankController extends Controller
{
    //

    public function addBank(BankRequest $request){
    $bank = new Bank();
    $bank->name=$request->input('name');
    $bank->user_id = $request->user()->id;
    $bank ->account_number=$request->input('account_number');
    $bank -> save();
    return response()->json(['success' => true, $bank]);
    }

    public function getBankPerUser()
    {
    $user = Auth::user();
    $getBankPerUser = Bank::where('user_id', $user->id)->get();
        return response()->json($getBankPerUser);
    }


    public function getAllBanks()
    {
        $getAllBanks = Bank::all();
        return response()->json($getAllBanks);
    }

    public function updateBank(Request $request, $id)
    {
        $update = Bank::find($id);;
        $update->update($request->all());
    
        return response()->json($update);
    
    }


    public function bank($id) 
    {
    $deleteBank = Bank::findOrFail($id);
   // return $deleteBank;  
    if($deleteBank)
       $deleteBank->delete(); 
    else
    return response()->json(null); 
}


public function ngnBanksApiList()
{

    $current_timestamp= now();
      $timestamp = strtotime($current_timestamp);
      $secret = env('PayThru_App_Secret');
      $hash = hash('sha512', $timestamp . $secret);
     
     // $hashSign = hash('sha512', $amt . $secret);
      $PayThru_AppId = env('PayThru_ApplicationId');
   
    $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'ApplicationId' => $PayThru_AppId,
        'Timestamp' => $timestamp,
        'Signature' => $hash,
  ])->get('http://sandbox.paythru.ng/cardfree/bankinfo/listBanks');
    //return $response;
    if($response->Successful())
    {
      $banks = json_decode($response->body(), true);
   
    }
    return $banks;

}


}
