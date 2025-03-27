<?php

namespace App\Http\Controllers\API;

use App\Models\Tag;
use App\Models\Group;
use App\Models\Expense;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ExpenseResource;

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

        return new ExpenseResource($expense);
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

        return new ExpenseResource($expense);
    }
    
    public function destroy(Expense $expense)
    {
        if ($expense->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $expense->delete();
        return response()->json(['message' => 'Expense deleted successfully']); 
    }

    public function attachTagsToExpense(Request $request, Expense $expense){
        $request->validate([
            'tags' => 'array|required',
            'tags.*' => 'exists:tags,id'
        ]); 

        $expense->tags()->attach($request->tags);

        return new ExpenseResource($expense->load('tags'));
    }
   
    public function show(Expense $expense)
    {
        return new ExpenseResource($expense->load('tags'));
    }
    
    public function index()
    {
        $expenses = auth()->user()->expenses()->with('tags')->get();
        return ExpenseResource::collection($expenses);
    }


    ///les dépenses partagées

    public function addGroupExpense(Request $request, $groupId)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'paid_by' => 'required|array',
            'paid_by.*.user_id' => 'exists:users,id',
            'paid_by.*.amount' => 'numeric|min:0',
            'split_type' => 'in:equal,percentage,custom',
        ]);
    
        $group = Group::findOrFail($groupId);
    
       
       
        if (!$group->users->contains(Auth::id())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
    
      
        $expense = Expense::create([
            'name' => $validatedData['name'],
            'amount' => $validatedData['amount'],
            'user_id' => Auth::id(),
            'group_id' => $group->id
        ]);
    
       
        $totalPaid = 0;
        foreach ($validatedData['paid_by'] as $payer) {
            $expense->participants()->attach($payer['user_id'], [
                'amount_paid' => $payer['amount'],
                'split_type' => $validatedData['split_type'] ?? 'equal'
            ]);
            $totalPaid += $payer['amount'];
        }
    
       
        if (round($totalPaid, 2) !== round($validatedData['amount'], 2)) {
            $expense->delete();
            return response()->json(['message' => 'Total amount paid does not match expense amount'], 400);
        }
    
        return new ExpenseResource($expense->load('participants'));
    }

    public function listGroupExpenses($groupId)
    {
        $group = Group::findOrFail($groupId);

        
        if (!$group->users->contains(Auth::id())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $expenses = $group->expenses()->with('participants')->get();
        return ExpenseResource::collection($expenses);
    }

    public function deleteGroupExpense($groupId, $expenseId)
    {
        $group = Group::findOrFail($groupId);
        $expense = Expense::findOrFail($expenseId);

        
        if (!$group->users->contains(Auth::id()) || $expense->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $expense->delete();
        return response()->json(['message' => 'Expense deleted successfully']);
    }

}