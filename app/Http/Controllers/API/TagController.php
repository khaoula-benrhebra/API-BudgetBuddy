<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Http\Resources\TagResource;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function create(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $tag = Tag::create(['name' => $request->name]);

        return new TagResource($tag);
    }

    public function update(Request $request, Tag $tag)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $tag->update($request->only('name'));

        return new TagResource($tag);
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();

        return response()->json(['message' => 'Tag deleted successfully']);
    }

    public function index()
    {
        return TagResource::collection(Tag::all());
    }

    public function show(Tag $tag)
    {
        return new TagResource($tag);
    }
}