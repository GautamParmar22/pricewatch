<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PriceChangeResource;
use Illuminate\Http\Request;

class PriceChangeController extends Controller
{
    /**
     * List pricing changes detected.
     */
    public function index(Request $request)
    {
        // GP - 19-08-2026 code comment - List changes API
        
        $workspace = $request->user()->workspaces()->first();
        if (!$workspace) {
            return response()->json(['error' => 'No active workspace found.'], 404);
        }

        $changes = $workspace->changes()->latest('detected_at')->get();

        return PriceChangeResource::collection($changes);
    }
}
