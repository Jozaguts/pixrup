<?php

namespace App\Interface\Properties\Http\Controllers;


use App\Application\Properties\UseCases\FetchSpyHuntDataUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\SpyHuntFilterRequest;
use Inertia\Inertia;

class SpyHuntController extends Controller
{
    public function __construct(
        private readonly FetchSpyHuntDataUseCase $fetchSpyHuntData,
    ) {}
    public function fetch(int $propertyId, SpyHuntFilterRequest $request)
    {

        try{
            $filters = $request->validated();

            $dto = $this->fetchSpyHuntData->execute($propertyId, $filters);

            return response()->json([
                'data' => $dto->toArray(),
            ]);

        }catch (\Throwable $e){
            report($e);
            return back()->with('error', 'Could not load SpyHunt data.');
        }
    }
}