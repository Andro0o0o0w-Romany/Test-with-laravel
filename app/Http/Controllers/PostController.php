<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $posts = Auth::user()->posts()->withTrashed()->orderBy('Pinned', 'desc')->get();
            return response()->json($posts);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'Title' => 'required|string|max:255',
                'Body' => 'required|string',
                'Cover_image' => 'required|image',
                'Pinned' => 'required|boolean',
                'tags' => 'array', // Validate that tags is an array
                'tags.*' => 'exists:tags,id', // Validate that each tag exists in the tags table
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            // Handle the file upload
            if ($request->hasFile('Cover_image')) {
                $coverImagePath = $request->file('Cover_image')->store('Cover_images', 'public');
                $request->merge(['Cover_image' => $coverImagePath]);
            }

            $post = Auth::user()->posts()->create($request->all());

            // Attach tags to the post
            if ($request->has('tags')) {
                $post->tags()->attach($request->tags);
            }

            return response()->json($post->load('tags'), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        try {
            if ($post->user_id !== Auth::id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            return response()->json($post->load('tags'));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        try {
            if ($post->user_id !== Auth::id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $validator = Validator::make($request->all(), [
                'Title' => 'required|string|max:255',
                'Body' => 'required|string',
                'Cover_image' => 'nullable|image',
                'Pinned' => 'required|boolean',
                'tags' => 'array', // Validate that tags is an array
                'tags.*' => 'exists:tags,id', // Validate that each tag exists in the tags table
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            // Handle the file upload
            if ($request->hasFile('Cover_image')) {
                $coverImagePath = $request->file('Cover_image')->store('Cover_images', 'public');
                $request->merge(['Cover_image' => $coverImagePath]);
            }

            $post->update($request->all());

            // Sync tags with the post
            if ($request->has('tags')) {
                $post->tags()->sync($request->tags);
            }

            return response()->json($post->load('tags'));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        try {
            if ($post->user_id !== Auth::id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $post->delete();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        try {
            $post = Post::withTrashed()->find($id);

            if ($post->user_id !== Auth::id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $post->restore();
            return response()->json($post->load('tags'));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
        }
    }
}
