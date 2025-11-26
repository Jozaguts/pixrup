<?php

namespace App\Application\Shared\Enums;
enum LogoFileType: string
{
    case PNG = 'image/png';
    case JPG = 'image/jpeg';
    case SVG = 'image/svg+xml';

    /**
     * Check if a mime matches a valid logo type.
     */
    public static function isValid(string $mime): bool
    {
        return in_array($mime, array_column(self::cases(), 'value'), true);
    }

    /**
     * Return all valid mime types
     */
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
