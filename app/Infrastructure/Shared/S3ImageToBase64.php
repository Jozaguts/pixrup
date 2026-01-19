<?php

namespace App\Infrastructure\Shared;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class S3ImageToBase64
{
    public static function execute(string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        // Only handle public URLs
        if (! Str::startsWith($url, ['http://', 'https://'])) {
            return null;
        }

        try {
            $response = Http::timeout(5)->get($url);

            if (! $response->successful()) {
                return null;
            }

            $content = $response->body();
            $mime = $response->header('Content-Type');

            if (! $content || ! $mime) {
                return null;
            }

            $base64 = base64_encode($content);

            return "data:{$mime};base64,{$base64}";
        } catch (\Throwable $e) {
            return null;
        }
    }
}
