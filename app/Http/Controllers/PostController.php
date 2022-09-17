<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        return PostResource::collection(
            Post::latest()->paginate()
        );
    }

    public function show(Post $post)
    {
        return new PostResource($post);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => ['required', 'string'],
            'body' => ['required', 'string'],
            'from' => ['required', 'string'],
            'created_at' => ['nullable', 'date'],
        ]);

        $validated = collect($validated)->filter(fn($value) => !is_null($value))->all();

        $post = Post::create($validated);

        return new PostResource($post);
    }
}
