<?php

namespace App\Interface\Properties\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class BillingController extends Controller
{
    public function account(): Response
    {
        return Inertia::render('billing/Account');
    }
}