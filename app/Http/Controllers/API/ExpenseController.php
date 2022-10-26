<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Expense;
use App\User;
use App\userExpense;
use App\UserGroup;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Auth;
use Mail;
use App\Mail\SendUserInviteMail;
use App\Http\Requests\ExpenseRequest;
use App\Http\Requests\userExpenseRequest;
use Illuminate\Support\Str;
use App\Jobs\ProcessBulkExcel;
use Illuminate\Http\Response;
use App\Helper\Reply;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Datetime;
use App\Exports\ExpenseExport;
use Excel;


class ExpenseController extends Controller
{
    //
    public function createExpense(ExpenseRequest $request)
    {

        $expense = Expense::create([
            'name' => $request->name,
            'description' => $request->description,
            'uique_code' => Str::random(10),
            'category_id' => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
            'amount' => $request->amount,
            'user_id' => Auth::user()->id
        ]);

        return response()->json($expense);

    }

    public function inviteUserToExpense(Request $request, $expenseUniqueCode)
    {
      //return response()->json($request->input());
      $expense = expense::findOrFail($expenseUniqueCode);
      $input['email'] = $request->input('email');
     
       $ProductId = env('PayThru_ProductId');
       $current_timestamp= now();
       $timestamp = strtotime($current_timestamp);
       $secret = 'wuyjrj7z9j96l7av';
       $hash = hash('sha512', $timestamp . $secret);
       $amt = $expense->amount;
       $hashSign = hash('sha512', $amt . $secret);
       $PayThru_AppId = env('PayThru_ApplicationId');

       //return "AppId ".  $PayThru_AppId;
    
      $emails = $request->email;
      //return $emails;
      if($emails)
      {
      $emailArray = (explode(';', $emails));
      $count = count($emailArray);
     // return response()->json($emailArray);
      
      $payers = [];
      $totalpayable = 0;
      foreach ($emailArray as $key => $em) {
          //process each user here as each iteration gives you each email
          $user = User::where('email', $em)->first();
      
        $payable = 0;

        if($request['split_method_id'] == 1)
        {
            $payable = $expense->amount;
  
        } elseif($request['split_method_id'] == 2)
        {
          if(isset($request->percentage))
          {
            $payable = $expense->amount*$request->percentage/100;
          }elseif(isset($request->percentage_per_user))
          {
            $ppu = json_decode($request->percentage_per_user);
            $payable = $ppu->$em*$expense->amount/100;
          }
        }elseif($request['split_method_id'] == 3)
        {
            $payable = $expense->amount/$count;
        }elseif($request['split_method_id'] == 4)
        {
            $payable = $expense->amount/$count;
        }

          $info = userExpense::create([
            'principal_id' => Auth::user()->id,
            'name' => $expense->name,
            'uique_code' => $expense->uique_code,
            'email' => $em,
            'description' => $expense['description'],
            'split_method_id' => $request['split_method_id'],
            'payable' => $payable,
            'actualAmount' => $expense->amount,
            'bankName' => $request['bankName'],
            'bankCode' => $request['bankCode'],
            'account_number' => $request['account_number'],
          ]);
      
         $payers[] =  ["payerEmail" => $em, "paymentAmount" => $info->payable];
         $totalpayable = $totalpayable + $info->payable;
         
      }
      // Send payment request to paythru  
      $data = [
        'amount' => $expense->amount,
        'productId' => '26349311662373680',
        'transactionReference' => time().$expense->uique_code,
        'paymentDescription' => $expense->description,
        'paymentType' => 1,
        'sign' => $hashSign,
        'displaySummary' => true,
        'splitPayInfo' => [
            'inviteSome' => false,
            'payers' => $payers
          ]

        ];

       // return "i got here";

       //return response()->json($data);

      $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'ApplicationId' => '93cdbd1e3ae649b3b5e173ffb87d95d2993de430b81a4415b8c2f309356d2278',
        'Timestamp' => $timestamp,
        'Signature' => $hash,
      ])->post('http://sandbox.paythru.ng/cardfree/transaction/create', $data );
     return $response->body();
      if($response->failed())
      {
        return false;
      }else{
        $transaction = json_decode($response->body(), true);
        $splitResult = $transaction['splitPayResult']['result'];
        //$uxer = User::find($user);
        foreach($splitResult as $key => $slip)
        {
          Mail::to($slip['receipient'])->send(new SendUserInviteMail($slip));
        }
      }
      
      //return [$info, $transaction];
      return response()->json($transaction);
      } 
     
       
    }

  
    //Calling PayThru gateway for transaction response updates
    public function webhookResponse()
    {
        $response = file_get_contents('http://azatme.eduland.ng/api/updateStatus');
        $data = json_decode($response->getBody());
        if($data->status == 1) {
            $statusUpdate = userExpense::where('expense_id', $data->transactionReference)->where('user_id', $data->customerId)->update([
            'transactionDate' => $data->transactionDate,
            'fiName' => $data->fiName,
            'status' => $data->status,
            'amount' => $data->amount,
            'customerName' => $data->customerName,
            'paymentReference' => $data->paymentReference,
            'payThruReference' => $data->payThruReference,
            'paymentReference' => $data->paymentReference,
            'merchantReference' => $data->merchantReference,
        ]);
        }     
    }

  private function userEmailToId($email){
      return User::select('id')->where('email',$email)->first()->value('id');  
  }

public function allExpensesPerUser()
{

  $pageNumber = 50;
  $getAuthUser = Auth::user();
  $getUserExpenses = UserExpense::where('principal_id', $getAuthUser->id)->latest()->paginate($pageNumber);
  return response()->json($getUserExpenses);

}

public function getRandomUserExpense($email)
{
$getUserExpense = userExpense::where('principal_id', Auth::user()->id)->where('email', $email)->get();
return response()->json($getUserExpense);

}

public function getAllExpenses()
{
  $getAdmin = Auth::user();
  $getAd = $getAdmin -> usertype;
  if($getAd === 'admin')
  {
  $getAllExpenses = UserExpense::all();
  return response()->json($getAllExpenses);
}
else{
   return response()->json('Auth user is not an admin');
}
}

public function countExpensesPerUser()
{
  $getAuthUser = Auth::user();
  $getUserExpenses = UserExpense::where('principal_id', $getAuthUser->id)->count();
  return response()->json($getUserExpenses);
}

public function updateExpense(Request $request, $id)
{
    $update = Expense::find($id);
    $update->update($request->all());
    return response()->json($update);

}

public function deleteInvitedExpenseUser($user_id)
{

$deleteInvitedExpenseUser = userExpense::findOrFail($user_id);
if($deleteInvitedExpenseUser)
   $deleteInvitedExpenseUser->delete();
else
return response()->json(null);
}

 public function deleteExpense($id)
        {
        $deleteExpense = expense::findOrFail($id);
        $getDeletedExpense = expense::where('user_id', Auth::user()->id)->where('id', $deleteExpense);
        if($deleteExpense)
        $deleteExpense->delete();
        else
        return response()->json(null);
        }

    public function BulkUploadInviteUsersToExpense(Request $request, $expenseId)
    {
      
        $expense = expense::findOrFail($expenseId);
        $request->validate([
          'file' => 'required|file'
        ]);
        $file = $request->file('file');
        $extension = $file->extension();
        $file_name = 'user_to_expense_' . time() . '.' . $extension;
        $file->storeAs(
            'excel bulk import', $file_name
        );
        $auth_user_id = Auth::user()->id;
        $result = ProcessBulkExcel::dispatchNow($file_name, $expense, $auth_user_id);
       // dd($result);
        if ($result) {
            $message = "Excel record is been uploaded";
            return response()->json($message);
        } else {
            $message = "Try file upload again";
            return response()->json($message);
        }
    }

    public function exportExpenseToExcel(Request $request)
    {
      $fileName = 'azatme_report'.'_'.Carbon::now() . '.' . 'xlsx';
      $userExpense = userExpense::getuserExpense($request);
      return Excel::download(new ExpenseExport($userExpense), $fileName);
    }

    public function exportExpenseToCsv(Request $request)
    {
      $fileName = 'azatme_report'.'_'.Carbon::now() . '.' . 'csv';
      $userExpense = userExpense::getuserExpense($request);
      return Excel::download(new ExpenseExport($userExpense), $fileName);
    }
}
