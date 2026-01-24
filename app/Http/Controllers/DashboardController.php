<?php

namespace App\Http\Controllers;

use App\Jobs\GeneratePixVisionReport;
use App\Models\Property;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $properties = Property::with('latestWorth')
            ->take(4)
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
                    'address' => $addressSegments->implode(', '),
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

        return Inertia::render('Dashboard', [
            'properties' => $properties,
        ]);
    }

    public function generateReport(Request $request, Property $property): \Illuminate\Http\RedirectResponse
    {
        GeneratePixVisionReport::dispatchSync($property, $request->user());

        return redirect('/properties')
            ->with('success', 'Report generation started. You will be notified when it is ready.');
    }
}
