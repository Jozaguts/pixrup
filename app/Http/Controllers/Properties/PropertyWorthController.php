<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Services\Appraisal\PropertyWorthService;
use Illuminate\Http\RedirectResponse;

class PropertyWorthController extends Controller
{
    public function __construct(
        protected PropertyWorthService $service,
    ) {}

    public function fetch(Property $property): RedirectResponse
    {
        $this->service->fetch($property);

        return redirect()
            ->route('properties.show', $property)
            ->with('status', 'worth-ready');
    }

    public function report(Property $property): RedirectResponse
    {
        $worth = $property->latestWorth;

        if (! $worth) {
            return redirect()
                ->route('properties.show', $property)
                ->withErrors([
                    'worth' => 'Generate an appraisal before adding it to the report.',
                ]);
        }

        // Placeholder for future PixrSeal integration.

        return redirect()
            ->route('properties.show', $property)
            ->with('status', 'worth-report');
    }
}
