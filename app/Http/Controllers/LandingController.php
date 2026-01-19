<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Billing\Services\PublicPricingCatalogService;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Features;

class LandingController extends Controller
{
    public function __invoke(PublicPricingCatalogService $pricingCatalog): Response
    {
        return Inertia::render('welcome/index', [
            'canRegister' => Features::enabled(Features::registration()),
            'pricingPlans' => $pricingCatalog->catalog(),
        ]);
    }
}
