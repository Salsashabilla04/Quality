<?php

namespace App\Http\Controllers;

use App\Services\RecommendationService;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function suggest(Request $request, RecommendationService $service)
    {
        return response()->json(
            $service->suggest(
                $request->input('ketidaksesuaian'),
                $request->input('penyebab')
            )
        );
    }
}
