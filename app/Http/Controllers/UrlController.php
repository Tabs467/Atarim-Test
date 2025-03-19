<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Url;
use Illuminate\Support\Str;

class UrlController extends Controller
{
    /**
     * Store an encoded URL
     */
    public function store(Request $request)
    {  
        $validated = $request->validate([
            'url' => 'required|max:255',
        ]);

        $unencoded_url = $validated['url'];

        $existing_url = Url::where('unencoded', $unencoded_url)->first();
        if ($existing_url) {
            return response()->json([
                'status' => 'success',
                'encodedUrl' => "http://short.est/" . $existing_url->encoded,
            ], 200);
        }
        else {
            do {
                $encoded_url = Str::random(10);
                $collision = Url::where('encoded', $encoded_url)->exists();
            } while ($collision);
    
            $url = Url::create([
                'encoded' => $encoded_url,
                'unencoded' => $unencoded_url,
                'expires_at' => now()->addDays(10),
            ]);
    
            return response()->json([
                'status' => 'success',
                'encodedUrl' => "http://short.est/" . $encoded_url,
            ], 201);
        }
    }

    /**
     * Retrieve a decoded URL
     */
    public function show(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required',
        ]);

        // Remove "http://short.est/", if it exists
        $encoded_string = Str::replaceFirst("http://short.est/", '', $validated['url']);

        $url = Url::where('encoded', $encoded_string)->first();

        if ($url) {
            return response()->json([
                'status' => 'success',
                'decodedUrl' => $url->unencoded,
            ], 200);
        }
        else {
            return response()->json([
                'status' => 'error',
            ], 404);
        }
        
    }
}
