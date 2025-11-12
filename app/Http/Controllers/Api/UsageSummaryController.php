<?php

namespace App\Http\Controllers\Api;

use App\Application\Usage\Services\UsageSummaryService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UsageSummaryController extends Controller
{
    public function __construct(
        private readonly UsageSummaryService $usageSummary,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        abort_unless($user !== null, Response::HTTP_UNAUTHORIZED);

        return response()->json($this->usageSummary->forUser($user)->toArray());
    }
}
