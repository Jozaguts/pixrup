<?php

namespace App\Interface\Properties\Http\Controllers;

use App\Application\Properties\DTOs\CreatePropertyDTO;
use App\Application\Properties\Overview\UseCases\CreatePropertyOverviewUseCase;
use App\Application\Properties\UseCases\CreatePropertyUseCase;
use App\Application\Usage\Services\UsageSummaryService;
use App\Http\Controllers\Controller;
use App\Http\Resources\GlowUp\GlowUpJobResource;
use App\Interface\Properties\Http\Requests\CreatePropertyRequest;
use App\Models\Property;
use App\Services\Runes\ComparablesDensityRune;
use App\Services\Runes\MarketSpreadRune;
use App\Services\Runes\MarketVelocityRune;
use App\Services\Runes\PriceReductionPressureRune;
use App\Services\Runes\PriceVsMarketRune;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PropertyController extends Controller
{

    public function index(): Response
    {
        $properties = Property::with('latestWorth')
            ->get()
            ->map(function (Property $property) {
                $status = in_array($property->status, ['in-progress', 'ready', 'pending', 'draft'], true)
                    ? $property->status
                    : 'in-progress';

                $addressSegments = collect([
                    $property->address,
                    collect([$property->city, $property->state])->filter()->implode(', '),
                    $property->postal_code,
                    $property->country,
                ])->filter();

                return [
                    'id' => $property->id,
                    'title' => $property->title ?? $property->address,
                    'formattedAddress' => $addressSegments->implode(', '),
                    'address' => $property->address,
                    'city' => $property->city . ', ' . $property->state,
                    'status' => $status,
                    'estimatedValue' => $property->latestWorth?->value,
                    'progress' => null,
                    'thumbnail' => $property?->photos()?->latest()?->first()?->path,
                    'links' => [
                        'view' => route('properties.show', $property, absolute: false),
                        'report' => null,
                    ],
                ];
            })
            ->values();
        return Inertia::render('properties/Index', [
            'properties' => $properties,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('properties/New');
    }

    public function show(Property $property): Response
    {
        $property->load('latestWorth');

        $latestWorth = $property->latestWorth;

        $worthPayload = $latestWorth ? [
            'id' => $latestWorth->id,
            'value' => $latestWorth->value,
            'confidence' => $latestWorth->confidence,
            'comparables' => $latestWorth->comparables,
            'trend' => $latestWorth->trend,
            'provider' => $latestWorth->provider,
            'value_low' => $latestWorth->value_low,
            'value_high' => $latestWorth->value_high,
            'fetched_at' => optional($latestWorth->fetched_at)->toIso8601String(),
        ] : null;


        $glowUpJobs = $property->glowupJobs()->latest()->take(10)->get();
        $glowUpJobsPayload = GlowUpJobResource::collection($glowUpJobs)->toArray(request());
        $glowUpAttachments = data_get($property->metadata, 'glowup.attachments', []);
        if (! is_array($glowUpAttachments)) {
            $glowUpAttachments = [];
        }

        $authedUser = auth()->user();
        $usageSummary = $authedUser
            ? app(UsageSummaryService::class)->forUser($authedUser)->toArray()
            : null;
        $glowUpUsage = null;
        if ($usageSummary !== null) {
            $rendersUsage = $usageSummary['usage']['renders'] ?? null;
            if (is_array($rendersUsage)) {
                $glowUpUsage = [
                    ...$rendersUsage,
                    'reset_at' => $usageSummary['resets_at'] ?? null,
                ];
            }
        }

        $propertyData = [
            'id' => $property->id,
            'title' => $property->title ?? $property->address,
            'status' => $property->status ?? 'in-progress',
            'address' => [
                'line1' => $property->address,
                'city' => $property->city,
                'state' => $property->state,
                'postal_code' => $property->postal_code,
            ],
            'owner' => [
                'name' => 'Workspace Owner',
                'email' => auth()->user()?->email,
            ],
            'summary' => [
                'bedrooms' => $property->bedrooms,
                'bathrooms' => $property->bathrooms,
                'livingArea' => data_get($property->metadata, 'summary.livingArea'),
                'squareFootage' => $property->square_footage,
                'yearBuilt' => data_get($property->metadata, 'summary.yearBuilt'),
                'propertyType' => $property->property_type
            ],
            'pricing' => [
                'acquisition' => data_get($property->metadata, 'pricing.acquisition'),
                'currentEstimate' => $latestWorth?->value,
                'potentialAfterGlow' => $latestWorth ? round($latestWorth->value * 1.06) : null,
            ],
            'last_updated' => optional($property->updated_at)->toIso8601String(),
            'last_updated_human' => optional($property->updated_at)->diffForHumans(),
            'tags' => data_get($property->metadata, 'tags', []),
            'worth' => $worthPayload,
            'workspace' => [
                'actions' => [
                    ['id' => 'appraise', 'label' => 'Appraise', 'module' => 'pixrWorth'],
                    ['id' => 'glowUp', 'label' => 'Glow-Up', 'module' => 'pixrGlowUp'],
                    ['id' => 'spyHunt', 'label' => 'SpyHunt', 'module' => 'pixrSpyHunt'],
//                    ['id' => 'vision', 'label' => '3D Tour', 'module' => 'pixrVision'],
//                    ['id' => 'seal', 'label' => 'Report', 'module' => 'pixrSeal'],
//                    ['id' => 'collab', 'label' => 'Collab', 'module' => 'pixrCollab'],
                ],
                'modules' => [
                    'overview' => [
                        'endpoint' => "/api/properties/{$property->id}",
                        'status' => 'ready',
                        'last_run_at' => optional($property->updated_at)->toIso8601String(),
                    ],
                    'pixrWorth' => [
                        'endpoint' => route('properties.worth.fetch', $property, absolute: false),
                        'status' => $latestWorth ? 'ready' : 'needs-action',
                        'last_run_at' => optional($latestWorth?->fetched_at)->toIso8601String(),
                    ],
                    'pixrGlowUp' => [
                        'endpoint' => "/api/properties/{$property->id}/glowup/jobs",
                        'status' => 'ready',
                        'last_run_at' => now()->subHours(12)->toIso8601String(),
                    ],
                    'pixrSpyHunt' => [
                        'endpoint' => "/api/properties/{$property->id}/spyhunt",
                        'status' => 'processing',
                        'last_run_at' => now()->subMinutes(45)->toIso8601String(),
                    ],
//                    'pixrVision' => [
//                        'endpoint' => "/api/properties/{$property->id}/vision",
//                        'status' => 'ready',
//                        'last_run_at' => now()->subDays(5)->toIso8601String(),
//                    ],
//                    'pixrSeal' => [
//                        'endpoint' => "/api/properties/{$property->id}/report",
//                        'status' => $latestWorth ? 'ready' : 'draft',
//                        'last_run_at' => null,
//                    ],
//                    'pixrCollab' => [
//                        'endpoint' => "/api/properties/{$property->id}/collab/token",
//                        'status' => 'ready',
//                        'last_run_at' => now()->subMinutes(5)->toIso8601String(),
//                    ],
                ],
            ],
            'glowUp' => [
                'jobs' => $glowUpJobsPayload,
                'usage' => $glowUpUsage,
                'attachments' => $glowUpAttachments,
                'options' => [
                    'room_types' => config('glowup.room_types', []),
                    'styles' => config('glowup.styles', []),
                ],
                'limits' => [
                    'max_upload_size_mb' => config('glowup.max_upload_size_mb', 10),
                ],
            ],
        ];

        return Inertia::render('properties/Show', [
            'property' => $propertyData,
            'usage' => $usageSummary,
        ]);
    }

    public function store(CreatePropertyRequest $request, CreatePropertyUseCase $useCase): RedirectResponse
    {
        $dto = new CreatePropertyDTO(
            auth()->user()->id,
            $request->input('address'),
            $request->input('status'),
            $request->input('address'),
            $request->input('city'),
            $request->input('state'),
            $request->input('postal_code'),
            $request->input('country'),
            $request->input('lat'),
            $request->input('lng'),
            $request->input('place_id'),
            $request->input('metadata'),
            $request->input('property_type'),
            $request->input('bedrooms'),
            $request->input('bathrooms'),
            $request->input('square_footage'),
        );

        $useCase->execute($dto, $request->file('photos'));

        return redirect()
            ->route('dashboard')
            ->with('status', 'property-created');
    }

    public function overview(Property $property, CreatePropertyOverviewUseCase $useCase, Request $request): JsonResponse
    {
        $force = $request->boolean('force');
        $overview = $useCase->execute($property->toEntity(), $force);

        return response()->json($overview);
    }

    public function signal(Property $property): JsonResponse
    {
        $service = new PriceVsMarketRune($property);
        $service_second= new MarketSpreadRune($property);
        $densityRune=  new ComparablesDensityRune($property);
        $marketVelocityRune = new MarketVelocityRune($property);
        $priceReductionPressureRune = new PriceReductionPressureRune($property);

        $runes =[
            'comparables_density' => $densityRune->toArray(),
            'market_spread' => $service_second->toArray(),
            'price_vs_market' => $service->toArray(),
            'market_velocity' => $marketVelocityRune->toArray(),
            'price_reduction_pressure' => $priceReductionPressureRune->toArray(),
        ];


        dd($runes);
    }
}
