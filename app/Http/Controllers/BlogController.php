<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class BlogController extends Controller
{
    public function index(): \Inertia\Response
    {
        return  Inertia::render('blog/index');
    }

    public function show(): \Inertia\Response
    {
        $params = [

        ];
        return  Inertia::render('blog/show');
    }

}
