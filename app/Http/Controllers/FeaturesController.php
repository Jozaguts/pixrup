<?php

namespace App\Http\Controllers;

use App\Models\Feature;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FeaturesController extends Controller
{
    public function index(): JsonResponse
    {
       return response()->json(Feature::all());
    }

    public function show(Request $request): Response
    {
        return Inertia::render('services/show');
    }
}
