<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Page;

class PageController extends Controller
{
    public function show($slug)
    {
        $page = Page::where('slug', $slug)->where('is_published', true)->firstOrFail();

        return view('page', compact('page'));
    }
}
