<?php

namespace App\Infrastructure\Shared;

class LocalImageToBase64
{
    public static function execute(string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        // Normalize path
        $path = self::normalizePath($path);

        if (! file_exists($path) || ! is_readable($path)) {
            return null;
        }

        $mime = mime_content_type($path);
        $content = file_get_contents($path);

        if (! $mime || ! $content) {
            return null;
        }

        $base64 = base64_encode($content);

        return "data:{$mime};base64,{$base64}";
    }

    private static function normalizePath(string $path): string
    {
        // Accept both absolute paths and public-relative paths
        if (! str_starts_with($path, '/')) {
            return public_path($path);
        }

        return $path;
    }
}
