<?php

namespace App\Http\Controllers\GlowUp;

use App\Application\GlowUp\Services\GlowUpJobService;
use App\Domain\Shared\Exceptions\FeatureLimitExceededException;
use App\Http\Controllers\Controller;
use App\Http\Requests\GlowUp\AttachGlowUpResultRequest;
use App\Http\Requests\GlowUp\CreateGlowUpJobRequest;
use App\Http\Resources\GlowUp\GlowUpJobResource;
use App\Models\GlowupJob;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class GlowUpJobController extends Controller
{
    public function __construct(
        private readonly GlowUpJobService $service,
    ) {
    }

    public function index(Property $property): JsonResponse
    {
        $jobs = $property
            ->glowupJobs()
            ->latest()
            ->take(25)
            ->get();

        return GlowUpJobResource::collection($jobs)->response();
    }

    public function history(): JsonResponse
    {
        $userId = auth()->id();

        abort_unless($userId !== null, Response::HTTP_FORBIDDEN);

        $query = GlowupJob::query()
            ->where('user_id', $userId)
            ->latest()
            ->take(50)
            ->get();

        return GlowUpJobResource::collection($query)->response();
    }

    public function show(Property $property, GlowupJob $glowupJob): JsonResponse
    {
        $this->assertJobProperty($property, $glowupJob);

        return (new GlowUpJobResource($glowupJob))->response();
    }

    public function store(
        CreateGlowUpJobRequest $request,
        Property $property,
    ): JsonResponse|RedirectResponse {
        $image = $request->file('image');

        if ($image === null) {
            return $this->respondWithError(
                $request,
                'Selecciona una imagen para generar el GlowUp.',
            );
        }

        $user = $request->user();

        abort_unless($user instanceof User, Response::HTTP_FORBIDDEN);

        try {
            $job = $this->service->create(
                $property,
                $user,
                $image,
                $request->validatedPayload(),
            );
        } catch (FeatureLimitExceededException $exception) {
            return $this->respondWithError(
                $request,
                $exception->getMessage(),
                Response::HTTP_FORBIDDEN,
            );
        }

        $resource = (new GlowUpJobResource($job))->resolve();

        if ($request->expectsJson()) {
            return response()->json(
                [
                    'job' => $resource,
                    'message' => 'Tu GlowUp está en cola ✨',
                ],
                Response::HTTP_CREATED,
            );
        }

        return redirect()
            ->route('properties.show', $property)
            ->with('status', 'glowup-queued')
            ->with('glowupJob', $resource);
    }

    public function attach(
        AttachGlowUpResultRequest $request,
        GlowupJob $glowupJob,
    ): JsonResponse|RedirectResponse {
        if ($glowupJob->after_url === null) {
            return $this->respondWithError(
                $request,
                'Genera el resultado antes de adjuntarlo.',
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        $this->assertJobOwner($request->user(), $glowupJob);

        $payload = $request->validatedPayload();

        $this->service->attachResult(
            $glowupJob,
            $payload['action'],
            $payload['notes'],
        );

        if ($request->expectsJson()) {
            return response()->json([
                'job' => (new GlowUpJobResource($glowupJob->refresh()))->resolve(),
                'message' => 'Resultado guardado correctamente.',
            ]);
        }

        return back()->with('status', 'glowup-attached');
    }

    private function assertJobProperty(Property $property, GlowupJob $job): void
    {
        abort_unless(
            (int) $job->property_id === (int) $property->getKey(),
            Response::HTTP_NOT_FOUND,
        );
    }

    private function assertJobOwner(?User $user, GlowupJob $job): void
    {
        abort_unless(
            $user !== null && (int) $user->getKey() === (int) $job->user_id,
            Response::HTTP_FORBIDDEN,
        );
    }

    private function respondWithError(
        CreateGlowUpJobRequest|AttachGlowUpResultRequest $request,
        string $message,
        int $status = Response::HTTP_BAD_REQUEST,
    ): JsonResponse|RedirectResponse {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], $status);
        }

        return back()->withErrors(['glowup' => $message]);
    }
}
