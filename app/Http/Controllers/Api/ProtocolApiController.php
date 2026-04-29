<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Protocol;
use Illuminate\Http\Request;

class ProtocolApiController extends Controller
{
    public function byDiagnosis(Request $request)
    {
        $protocols = Protocol::with(['protocolDrugs.drug'])
            ->where('diagnosis_id', $request->diagnosis_id)
            ->orderBy('name')
            ->get();

        return response()->json($protocols);
    }
}
