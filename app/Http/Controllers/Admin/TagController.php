<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTagRequest;
use App\Http\Requests\Admin\UpdateTagRequest;
use App\Http\Resources\Admin\TagResource;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        $tag = Tag::paginate(10);
        return TagResource::collection($tag);
    }

    public function store(StoreTagRequest $request)
    {
        $tag = Tag::create($request->validated());
        return new TagResource($tag);
    }

    public function show($id)
    {
        $tag = Tag::find($id);

        if (!$tag) {
            return response()->json([
                'message' => 'Tag not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new TagResource($tag)
        ]);
    }

    public function update(UpdateTagRequest $request, $id)
    {
        $tag = Tag::findOrFail($id);
        $tag->update($request->validated());
        return new TagResource($tag);
    }

    public function destroy($id)
    {   
        $tag = Tag::findOrFail($id);
        $tag->delete();
        return response()->json([
            'success' => true,
            'message' => 'Tag deleted successfully'
        ]);
    }
}
