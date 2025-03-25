<?php
namespace App\Http\Controllers;

use App\Http\Resources\GroupResource;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller {
    
    public function index() {
        return GroupResource::collection(Auth::user()->groups);
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'currency' => 'string|max:10',
        ]);

        $group = Group::create([
            'name' => $request->name,
            'currency' => $request->currency ?? 'EUR',
        ]);

        $group->users()->attach(Auth::id());

        return new GroupResource($group);
    }

    public function show(Group $group) {
        if (!$group->users->contains(Auth::id())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return new GroupResource($group);
    }

    public function destroy(Group $group) {
        if (!$group->users->contains(Auth::id())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Vérifier si le solde est à 0 avant suppression (ajout à faire)
        $group->delete();
        return response()->json(['message' => 'Groupe supprimé'], 200);
    }
}
