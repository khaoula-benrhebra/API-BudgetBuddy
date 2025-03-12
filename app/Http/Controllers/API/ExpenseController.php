<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Models\Expense;
use App\Models\Tag;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
   
   
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric',
        ]);

        $user = Auth::user();

        $expense = $user->expenses()->create([
            'name' => $request->name,
            'amount' => $request->amount,
        ]);

        return response()->json($expense, 201);
    }


public function update(Request $request, Expense $expense)
{
    if ($expense->user_id !== Auth::id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $request->validate([
        'name' => 'string|max:255',
        'amount' => 'numeric',
    ]);

    $expense->update($request->only('name', 'amount'));

    return response()->json($expense);
}

    
public function destroy(Expense $expense)
{

    if ($expense->user_id !== Auth::id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }
    
    $expense->delete();
    return response()->json(['message' => 'Expense deleted successfully']); // Correction ici
}


   public function attachTagsToExpense(Request $request, Expense $expense){

    $request->validate([
        'tags' => 'array|required',
        'tags.*' => 'exists:tags,id'
    ]);

    $expense->tags()->attach($request->tags);

        return response()->json($expense->load('tags'));
   }
   
   public function show(Expense $expense)
    {
        return response()->json($expense->load('tags'));
    }

    
    public function index()
    {
        $expenses = auth()->user()->expenses()->with('tags')->get();
        return response()->json($expenses);
    }
}
