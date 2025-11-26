<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadLogoRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Models\Image;

class PdfReportsController extends Controller
{
    public function index(): \Inertia\Response
    {
        return Inertia::render('reports/Index', []);
    }

    public function new(): \Inertia\Response
    {
        return Inertia::render('reports/New', []);
    }

    public function storeLogo(UploadLogoRequest $request): JsonResponse
    {
        $file = $request->file('logo');
        $userUuid = auth()->user()->uuid;

        $uniqueName = $request->get('name') ?? $file->getClientOriginalName();

        $key = "images/{$userUuid}/logos/{$uniqueName}";

        // Upload to S3
        Storage::disk('s3')->put($key, file_get_contents($file), 'public');

        // Build non-expiring public URL
        $publicUrl = Storage::disk('s3')->url($key);

        // Save metadata
        $logo = Image::create([
            'user_uuid' => $userUuid,
            'name' => $uniqueName,
            'size' => $file->getSize(),
            'uri' => $publicUrl,   // <— public URL stored
            'path' => $key,         // <— optional internal path
        ]);

        return response()->json([
            'message' => 'Logo uploaded successfully',
            'logo' => $logo,
        ]);
    }

    public function getLogos(): JsonResponse
    {
        $userUuid = auth()->user()->uuid;
        $logos = Image::orderBy('name', 'asc')
            ->get();

        return response()->json([
            'logos' => $logos,
            'uuid' => $userUuid,
        ]);

    }
}
