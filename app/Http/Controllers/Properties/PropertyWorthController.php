<?php

namespace App\Http\Controllers\Properties;

use App\Application\Usage\Services\MonthlyPropertyUsageService;
use App\Domain\Shared\Exceptions\FeatureLimitExceededException;
use App\Domain\Usage\Enums\UsageAction;
use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\User;
use App\Services\Appraisal\PropertyWorthService;
use Illuminate\Http\RedirectResponse;

class PropertyWorthController extends Controller
{
    public function __construct(
        protected PropertyWorthService $service,
        private readonly MonthlyPropertyUsageService $usageService,
    ) {}

    /**
     * @throws FeatureLimitExceededException
     */
    public function fetch(Property $property): RedirectResponse
    {
        $user = request()->user();
        if ($user instanceof User) {
            $this->usageService->ensureUsage($user, $property, UsageAction::APPRAISAL);
        }
        $this->service->fetch($property);

        return redirect()
            ->route('properties.show', $property)
            ->with('status', 'worth-ready');
    }

    public function report(Property $property): RedirectResponse
    {
        $user = request()->user();

        if ($user instanceof User) {
            try {
                $this->usageService->ensureUsage($user, $property, UsageAction::REPORT);
            } catch (FeatureLimitExceededException $exception) {
                return redirect()
                    ->route('properties.show', $property)
                    ->withErrors(['worth' => $exception->getMessage()]);
            }
        }

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
