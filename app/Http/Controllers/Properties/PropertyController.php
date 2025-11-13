<?php

namespace App\Http\Controllers\Properties;

use App\Application\Usage\Services\UsageSummaryService;
use App\Http\Controllers\Controller;
use App\Http\Resources\GlowUp\GlowUpJobResource;
use App\Models\Property;
use App\Models\PropertyPhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PropertyController extends Controller
{
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
            'fetched_at' => optional($latestWorth->fetched_at)->toIso8601String(),
        ] : null;

        $glowUpJobs = $property->glowupJobs()->latest()->take(10)->get();
        $glowUpJobsPayload = GlowUpJobResource::collection($glowUpJobs)->toArray(request());

        $authedUser = auth()->user();
        $usageSummary = $authedUser
            ? app(UsageSummaryService::class)->forUser($authedUser)->toArray()
            : null;

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
                'bedrooms' => data_get($property->metadata, 'summary.bedrooms'),
                'bathrooms' => data_get($property->metadata, 'summary.bathrooms'),
                'livingArea' => data_get($property->metadata, 'summary.livingArea'),
                'lotSize' => data_get($property->metadata, 'summary.lotSize'),
                'yearBuilt' => data_get($property->metadata, 'summary.yearBuilt'),
                'propertyType' => data_get($property->metadata, 'summary.propertyType'),
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
                    ['id' => 'vision', 'label' => '3D Tour', 'module' => 'pixrVision'],
                    ['id' => 'seal', 'label' => 'Report', 'module' => 'pixrSeal'],
                    ['id' => 'collab', 'label' => 'Collab', 'module' => 'pixrCollab'],
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
                    'pixrVision' => [
                        'endpoint' => "/api/properties/{$property->id}/vision",
                        'status' => 'ready',
                        'last_run_at' => now()->subDays(5)->toIso8601String(),
                    ],
                    'pixrSeal' => [
                        'endpoint' => "/api/properties/{$property->id}/report",
                        'status' => $latestWorth ? 'ready' : 'draft',
                        'last_run_at' => null,
                    ],
                    'pixrCollab' => [
                        'endpoint' => "/api/properties/{$property->id}/collab/token",
                        'status' => 'ready',
                        'last_run_at' => now()->subMinutes(5)->toIso8601String(),
                    ],
                ],
            ],
            'glowUp' => [
                'jobs' => $glowUpJobsPayload,
                'usage' => $usageSummary
                    ? [
                        'used' => $usageSummary['used'],
                        'limit' => $usageSummary['limit'],
                        'reset_at' => $usageSummary['resets_at'],
                    ]
                    : null,
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

    /**
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'address' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'state' => ['nullable', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:30'],
            'country' => ['nullable', 'string', 'max:120'],
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'place_id' => ['nullable', 'string', 'max:255'],
            'photos.*' => ['nullable', 'file', 'image', 'max:8192'],
        ]);
        $property = DB::transaction(function () use ($validated, $request): Property {
            $property = Property::create([
                'title' => $validated['address'],
                'status' => 'in-progress',
                'address' => $validated['address'],
                'city' => $validated['city'] ?? null,
                'state' => $validated['state'] ?? null,
                'postal_code' => $validated['postal_code'] ?? null,
                'country' => $validated['country'] ?? null,
                'lat' => $validated['lat'],
                'lng' => $validated['lng'],
                'place_id' => $validated['place_id'] ?? null,
                'metadata' => [
                    'source' => 'ui',
                    'created_via' => 'wizard',
                ],
            ]);
            $this->storePhotos($request, $property);

            return $property;
        });

        return redirect()
            ->route('dashboard')
            ->with('status', 'property-created');
    }

    protected function storePhotos(Request $request, Property $property): void
    {
        if (! $request->hasFile('photos')) {
            return;
        }

        $files = $request->file('photos');

        foreach ($files as $file) {
            if (! $file) {
                continue;
            }
            $user = $request->user();
            if (! $user){
                throw  new \RuntimeException('');
            }

            $path = $file->store("/users/{$request->user()->id}/properties/{$property->id}");

            $url = Storage::url($path);

            PropertyPhoto::create([
                'property_id' => $property->id,
                'path' => $url,
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
            ]);
        }
    }
}
