<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Profit_Goal;
use App\Models\Category;
class ProfitGoalsController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'goal_amount' => 'required|numeric',
        ]);
     
        $profitGoal = new Profit_Goal;
        $profitGoal->fill($validatedData);
        $profitGoal->save();
        return response()->json([
            'message' => 'Profit goal created successfully',
            'data' => $profitGoal
        ], 201);
    }

    /**
     * Display the specified resource.
     */

    public function CalculateProfit()
    {
        $mostRecentProfitGoal = Profit_Goal::latest()->first();
        $start_date = $mostRecentProfitGoal->start_date;
        $end_date = $mostRecentProfitGoal->end_date;
        $profit_goal=$mostRecentProfitGoal->goal_ammount;

        $income = Category::where('type', 'income')
        ->with('transactions')
        ->whereHas('transactions', function ($query) use ($start_date, $end_date) {
          $query->whereBetween('created_at', [$start_date, $end_date]);
        })
        ->sum('transactions.amount');

       $expenses = Category::where('type', 'expense')
        ->with('transactions')
        ->whereHas('transactions', function ($query) use ($start_date, $end_date) {
        $query->whereBetween('created_at', [$start_date, $end_date]);
       })
       ->sum('transactions.amount');
   
       $net_profit = $income - $expenses;
       $percentage= ($net_profit*100)/$profit_goal;
       return $percentage;
    }
}

