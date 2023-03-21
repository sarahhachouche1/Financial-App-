<?php

namespace App\Http\Controllers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Transaction;
use App\Models\Admin;
use App\Models\Category;
use Carbon\Carbon;
use App\Jobs\SendEmailJob;


class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function index()
{
    $transactions = Transaction::all();
    error_log("dummy");
    $result = [];

    foreach ($transactions as $transaction) {
        $category = $transaction->category->name;
        $type = $transaction->category->type;
        $created_by = optional($transaction->admins->first())->pivot->admin_id;
        $name = Admin::find($created_by)->name;

        $data = [
            'id' => $transaction->id,
            'category' => $category,
            'title' => $transaction->title,
            'amount' => number_format($transaction->amount, 4),
            'type' => $type . "  ". $transaction->type,
            'date' => $transaction->date,
            'paid' => $transaction->Paid,
            'created_by' => $name
        ];
        $result[] = $data;
    }

    return response($result)
    ;
}


    
       

    /**
     * Store a newly created resource in storage.
     */
      public function store(Request $request): JsonResponse
     {
        $rules = [
         'title' => 'required|max:100',
         'description' => 'nullable|max:255',
         'amount' => 'required|numeric',
         'currency' => 'required|max:4',
         'type' => 'required|in:fixed,recurring',
         'category_id' => 'required|exists:categories,id',
         'email' => 'nullable|sarahhachouche7@gmail.com'
       ];
       $currency = $request->input('currency');
       $amount = $request->input('amount');
   
       if ($currency == 'LL') {
           $amount = $amount / 100000;   
       } elseif ($currency == 'Euro') {
               $amount = $amount * 1.18;       
       } 
       if ($request->input('type') === 'recurring') {
            $rules['start_date'] = 'required|date|after_or_equal:today';
            $rules['end_date'] = 'required|after_or_equal:start_date';
            $rules['frequency'] = 'required|in:weekly,monthly,yearly';
         } else {
          $rules['start_date'] = 'nullable';
          $rules['end_date'] = 'nullable';
          $rules['frequency'] ='nullable';
          $rules['date'] = 'required|date|after_or_equal:today';
       }
       $email = $request->input('email');
       
if (!$request->has('email') || is_null($email)) {
     $email='sarahhachouche7@gmail.com';
}
        $validatedData = $request->validate($rules);
        if ($request->input('type') === 'recurring') {
            $transaction = new Transaction([
             'title' => $validatedData['title'],
             'description' => $validatedData['description'],
             'amount' => $validatedData['amount'],
             'currency' => $validatedData['currency'],
             'type' => $validatedData['type'],
             'frequency' => $validatedData['frequency'],
             'start_date' => $validatedData['start_date'],
             'end_date' => $validatedData['end_date'],
             'category_id' => $validatedData['category_id'],
             'email' =>$email,
            ]);
            $details = [
              'email' => $email,
               'subject' => 'Payments Reminder',
              'message' => 'This is an automatic email. Please do not forget to make your payments.',
            ];
              // Convert the start and end dates to Carbon instances
               $startDate = Carbon::parse($request->input('start_date'));
               $endDate = Carbon::parse($request->input('end_date'));

             // Ensure that the start date is before the end date
            if ($startDate->gte($endDate)) {
               return response()->json(['error' => 'Invalid date range'], 400);
            }
            $currentDate = $startDate;
            while ($currentDate->lte($endDate)) {
               $transaction = new Transaction([
                 'title' => $validatedData['title'],
                 'description' => $validatedData['description'],
                 'amount' => $amount,
                 'currency' => 'USD',
                 'type' => $validatedData['type'],
                 'frequency' => $validatedData['frequency'],
                  'date' => $currentDate,
                 'start_date' => $validatedData['start_date'],
                 'end_date' => $validatedData['end_date'],
                 'email' => $email,
                 'category_id' => $validatedData['category_id'],
            
              ]);
              $transaction->save();
               // Increment the current date by the chosen frequency
             switch ($request->input('frequency')) {
                case 'weekly':
                  $currentDate->addWeek();
                  break;
                case 'monthly':
                  $currentDate->addMonth();
                   break;
                case 'yearly':
                  $currentDate->addYear();
                  break;
               default:
                 return response()->json(['error' => 'Invalid frequency'], 400);
        }
          $adminId = $request->user()->id;
          $transaction->admins()->attach($adminId);
   
    }
            

             // Calculate the first send date
             switch ($request->input('frequency')) {
                case 'weekly':
                  $sendAt = $startDate->copy()->addWeek();
                  break;
                case 'monthly':
                  $sendAt = $startDate->copy()->addMonth();
                  break;
                case 'yearly':
                   $sendAt = $startDate->copy()->addYear();
                   break;
                default:
                  return response()->json(['error' => 'Invalid frequency'], 400);
             } 

             // Schedule the job to send the email every week
            while ($sendAt->lte($endDate)) {
               SendEmailJob::dispatch($details)->delay($sendAt);
              // Increment the send date by the chosen frequency
               switch ($request->input('frequency')) {
                   case 'weekly':
                      $sendAt->addWeek();
                      break;
                  case 'monthly':
                     $sendAt->addMonth();
                     break;
                  case 'yearly':
                     $sendAt->addYear();
                     break;
               }
            }
          }
          else{
              $transaction = new Transaction([
             'title' => $validatedData['title'],
             'description' => $validatedData['description'],
             'amount' => $amount,
             'currency' => 'USD',
             'type' => $validatedData['type'],
             'date' => $validatedData['date'],
             'category_id' => $validatedData['category_id'],
             ]);
             $transaction->save();
             $adminId = $request->user()->id;
             $transaction->admins()->attach($adminId);
   
          }
          $category = $transaction->category;
          $name = $category->name;
          $type=$category->type;
          $data = array(
            'id' => $transaction->id,
            'category'=>$name,
            'title' => $transaction->title,
            'amount' => $transaction->amount,
            'type' =>$transaction->type . " " . $type,
            'date' => $transaction->date,
            'paid' =>$transaction->paid,
            'created_by' => $request->user()->id,

          );
          

      return response()->json( ['data' => $data]);
}

   
    /**
     * Display the specified resource.
     */
     public function show($name = null, $type = null): JsonResponse
{
    $query = Category::query();
    if ($name !== null) {
        $query->where('name', $name);
    }

    if ($type !== null) {
        $query->where('type', $type);
    }

    $categories = $query->get();

    $transactions = collect();
    foreach ($categories as $category) {
        $transactions = $transactions->merge($category->transactions);
    }

    return response()->json([
        'transactions' => $transactions
    ]);
}


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
  
         $transaction = Transaction::find($id);
         if (!$transaction) {
           return response()->json(['message' => 'Transaction not found'], 404);
         }
  
         $allowedFields = ['title', 'description', 'currency', 'amount', 'email', 'Paid'];
         $data = $request->only($allowedFields);
         $data = array_filter($data, function ($value) {
             return $value !== null;
         });
     
         if (empty($data)) {
           return response()->json(['message' => 'No fields to update'], 400);
         }
  
         $transaction->update($data); 
        
         $adminId = $request->user()->id;
       
        $transaction->admins()->attach($adminId, [
          'updated_by' => $adminId,
       ]);
        
       
        return response($transaction, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,$id): JsonResponse
    {
        $transaction= Transaction::find($id);
        
        $adminId = $request->user()->id;
        $transaction->admins()->attach($adminId, [
            'deleted_by' => $adminId,
        ]);

        if (!$transaction) {
          return response()->json(['message' => 'Record not found.'], 404);
        }
        $transaction->delete();
        return response()->json(['message' => 'Record deleted successfully.'], 200);
 
    }
}
?>
