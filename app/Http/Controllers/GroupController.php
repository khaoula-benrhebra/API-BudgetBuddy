<?php
namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\GroupResource;

class GroupController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'devise' => 'required|string',
            'members' => 'required|array', 
        ]);

        $group = Group::create([
            'name' => $validated['name'],
            'devise' => $validated['devise'],
        ]);

        $group->users()->attach($validated['members']);

        return new GroupResource($group);
    }

    public function index()
    {
        $groups = auth()->user()->groups;
        return GroupResource::collection($groups);
    }

    public function show($id)
    {
        $group = Group::findOrFail($id);
        return new GroupResource($group);
    }

    public function destroy($id)
    {
        $group = Group::findOrFail($id);

        if ($group->expenses->isEmpty()) {
            $group->delete();
            return response()->json(null, 204);
        }

        return response()->json(['message' => 'Cannot delete group with existing balances'], 400);
    }
}
