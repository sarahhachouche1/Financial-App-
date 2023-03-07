<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CalculateNetProfit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(){

       $profitGoal = ProfitGoal::where('start_date', '<=', now())
          ->where('end_date', '>=', now())
          ->first();

       $netProfit = Transaction::where('transaction_date', '>=', $profitGoal->start_date)
          ->where('transaction_date', '<=', now())
          ->sum('amount');
       $percentage = round($netProfit / $profitGoal->goal_amount * 100, 2);
       $message = "Your net profit is {$percentage}% of your profit goal for this year.";

       $response = Http::post('https://profit-goal', [
        'message' => $message,
      ]);
    }
}
