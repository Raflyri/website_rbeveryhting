<?php

namespace App\Http\Controllers;

use App\Models\Base64ApiEndpoint;
use Illuminate\Http\Request;

class Base64ConverterController extends Controller
{
    public function index()
    {
        // Get all active endpoints, ordered by sort_order
        $endpoints = Base64ApiEndpoint::active()
            ->ordered()
            ->get();

        // Group by category for the frontend filter
        $groupedEndpoints = $endpoints->groupBy('category');

        return view('tools.base64-converter', compact('endpoints', 'groupedEndpoints'));
    }
}
