<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Category::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'type' => 'required',
            'created_by' =>'required'
        ]);

        $category = new Category;
        $category->fill($request->all());
        $category->save();
        return response()->json($category);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Category::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        $category->update($request->all());
        return $category;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return Category::destroy($id);
    }

     /**
     * Search for a name
     *
     * @param  str  $name
     * @return \Illuminate\Http\Response
     */
    public function search($name)
    {
        return Category::where('name', 'like', '%'.$name.'%')->get();
    }

 public function restore($name){
    Category::withTrashed()->where('name', $name)->restore();
    return response()->json(['message' => 'Category restored successfully']);
 }

 public function getNetProfit($name, $frequency)
    {
        if (!in_array($frequency, ['week', 'month', 'year'])) {
            return response()->json(['error' => 'Invalid frequency'], 400);
        }
        $categoryName = $name;
        $frequency = $frequency;
        
        $category_incomes = Category::where('name', $categoryName)
                                    ->where('type', 'incomes');
        $category_outcomes = Category::where('name', $categoryName)
                                    ->where('type', 'outcomes');
     
        $categoryId_incomes = $category_incomes->id;
        $categoryId_outcomes = $category_outcomes->id;
        $incomesCategory = Transaction::where('category_id',$categoryId_incomes)
                         ->where('paid', 1)
                         ->whereBetween('date', [now()->sub($frequency, 1), now()])
                         ->sum('amount');
       $expensesCategory = Transaction::where('category_id', $categoryId_outcomes)
                        ->where('paid', 1)
                        ->whereBetween('date', [now()->sub($frequency, 1), now()])
                        ->sum('amount');
       $netProfit = $incomesCategory- $expensesCategory;
    
       $incomesAll = Transaction::where('type', 'incomes')
                     ->whereBetween('date', [now()->sub($frequency, 1), now()])
                     ->where('paid', 1)
                     ->sum('amount');

        
        
       $expensesAll = Transaction::where('type', 'expenses')
                      ->whereBetween('date', [now()->sub($frequency, 1), now()])
                      ->sum('amount');

       $profitAll = $incomesAll - $expensesAll;

       $percentage = ($netProfit*100)/$profitAll;

       return $percentage;
    }
}
?>

