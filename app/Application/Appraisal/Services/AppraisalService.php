<?php

/**
 * Description: File implementing the AppraisalService orchestrating property worth retrieval.
 * Parameters: None.
 * Returns: Void.
 * Expected Result: Provides application-layer logic for fetching, caching, and persisting valuations.
 */

namespace App\Application\Appraisal\Services;

use App\Application\Appraisal\DTOs\PropertyWorthDTO;
use App\Application\Usage\Services\MonthlyPropertyUsageService;
use App\Domain\Appraisal\Providers\AppraisalProviderInterface;
use App\Domain\Appraisal\Repositories\PropertyWorthRepositoryInterface;
use App\Domain\Usage\Enums\UsageAction;
use App\Models\Property;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Description: Coordinate provider calls, caching, and persistence for property appraisals.
 * Parameters: None.
 * Returns: Not applicable.
 * Expected Result: Returns normalized valuation DTOs while respecting plan limits and cache.
 */
class AppraisalService
{
    private const CACHE_TTL_HOURS = 24;

    /**
     * Description: Inject dependencies required to retrieve property appraisals.
     * Parameters: PropertyWorthRepositoryInterface $repository Persistence port; AppraisalProviderInterface $provider External provider adapter; MonthlyPropertyUsageService $usageService Usage guard.
     * Returns: void.
     * Expected Result: Service ready to fetch valuations with caching and limit checks.
     */
    public function __construct(
        private readonly PropertyWorthRepositoryInterface $repository,
        private readonly AppraisalProviderInterface $provider,
        private readonly MonthlyPropertyUsageService $usageService,
    ) {
    }

    /**
     * Description: Fetch a valuation for the specified property with caching and plan enforcement.
     * Parameters: Property $property Eloquent model describing the property to evaluate.
     * Returns: PropertyWorthDTO
     * Expected Result: Returns cached valuation when fresh, otherwise fetches from provider and persists.
     */
    public function fetchValuation(Property $property): PropertyWorthDTO
    {
        $threshold = Carbon::now()->subHours(self::CACHE_TTL_HOURS);
        $cached = $this->repository->findLatestWithin($property->getKey(), $threshold);

        if ($cached !== null) {
            return $cached->toDto(Carbon::now());
        }

        $user = $this->currentUser();
        if ($user !== null) {
            $this->usageService->ensureUsage($user, $property, UsageAction::APPRAISAL);
        }

        $valuation = $this->provider->fetchValue($property);
        $stored = $this->repository->saveFromDto($property->getKey(), $valuation);

        return $stored->toDto();
    }

    /**
     * Description: Retrieve the currently authenticated user if available.
     * Parameters: None.
     * Returns: ?User
     * Expected Result: Provides user instance for plan-limit evaluation or null for guests.
     */
    private function currentUser(): ?User
    {
        $user = Auth::user();

        return $user instanceof User ? $user : null;
    }
}
