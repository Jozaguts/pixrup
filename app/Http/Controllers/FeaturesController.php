<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class FeaturesController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('services/index');
    }

    public function show(): Response
    {
        return Inertia::render('services/show');
    }
}
