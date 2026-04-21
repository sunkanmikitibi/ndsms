<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    // User Context
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Portal Dashboard Stats
    Route::get('/stats', function (Request $request) {
        $user = $request->user();
        return response()->json([
            'requests_count' => \App\Models\StreetApplication::where('user_id', $user->id)->count() + 
                               \App\Models\Address::where('user_id', $user->id)->count(),
            'approved_count' => \App\Models\StreetApplication::where('user_id', $user->id)->where('status', 'approved')->count(),
            'pending_count' => \App\Models\StreetApplication::where('user_id', $user->id)->where('status', 'pending')->count(),
        ]);
    });

    // My Requests
    Route::get('/requests', function (Request $request) {
        $user = $request->user();
        return response()->json([
            'streets' => \App\Models\StreetApplication::where('user_id', $user->id)->latest()->get(),
            'addresses' => \App\Models\Address::where('user_id', $user->id)->latest()->get(),
        ]);
    });

    // Verification Engine (Public Access)
    Route::get('/verify', function (Request $request) {
        $type = $request->query('type');
        $query = $request->query('query');
        
        if ($type === 'street') {
            return \App\Models\Street::where('code', $query)->first() ?: response()->json(['error' => 'Not found'], 404);
        }
        
        return \App\Models\Address::where('house_number', $query)->first() ?: response()->json(['error' => 'Not found'], 404);
    });

    // Payments History
    Route::get('/payments', function (Request $request) {
        return \App\Models\Payment::where('user_id', $request->user()->id)->latest()->get();
    });
});
