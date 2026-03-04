<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Insights listing page — /insights
     */
    public function index(Request $request)
    {
        $type = $request->query('type'); // null | 'news' | 'article' | 'blog'

        // Featured post — most recent featured, fall back to most recent published
        $featured = Post::published()
            ->featured()
            ->latest('published_at')
            ->first()
            ?? Post::published()->latest('published_at')->first();

        // Grid posts — exclude the featured post
        $postsQuery = Post::published()
            ->when($featured, fn($q) => $q->where('id', '!=', $featured->id))
            ->when($type, fn($q, $t) => $q->byType($t))
            ->latest('published_at');

        $posts = $postsQuery->get();

        // All posts for JS filter (we pass all to the view, JS hides/shows by type)
        // When a ?type= query exists we also pre-filter to reduce initial payload
        $allPosts = Post::published()
            ->when($featured, fn($q) => $q->where('id', '!=', $featured->id))
            ->latest('published_at')
            ->get();

        return view('insights.index', compact('featured', 'posts', 'allPosts'));
    }

    /**
     * Single post reading page — /insights/{slug}
     */
    public function show(string $slug)
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();

        // Next post — next by published_at
        $next = Post::published()
            ->where('id', '!=', $post->id)
            ->where('published_at', '<=', $post->published_at)
            ->where('id', '<', $post->id)
            ->latest('published_at')
            ->first()
            ?? Post::published()
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->first();

        return view('insights.show', compact('post', 'next'));
    }
}
