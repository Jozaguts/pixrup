<?php

namespace App\Interface\Properties\Http\Controllers;


use App\Application\Properties\SpyHunt\UseCases\FetchSpyHuntDataUseCase;
use App\Domain\Shared\Exceptions\FeatureLimitExceededException;
use App\Http\Controllers\Controller;
use App\Http\Requests\SpyHuntFilterRequest;

class SpyHuntController extends Controller
{
    public function __construct(
        private readonly FetchSpyHuntDataUseCase $fetchSpyHuntData,
    ) {}
    public function fetch(int $propertyId, SpyHuntFilterRequest $request)
    {

        try{
            $filters = $request->validated();

            $user = $request->user();

            $force = $request->boolean('force');
            $dto = $this->fetchSpyHuntData->execute($propertyId, $user, $filters, $force);

            return response()->json([
                'data' => $dto->toArray(),
            ]);

        } catch (FeatureLimitExceededException $e) {
            $context = $e->context();
            $code = ($context['reason'] ?? null) === 'subscription_inactive' ? 'subscription' : 'limit';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'code' => $code,
                ], 403);
            }

            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Could not load SpyHunt data.');
        }
    }
}
