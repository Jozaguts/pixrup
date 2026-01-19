<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Property Report – {{ $property->title }}</title>
    <style>
        {!! file_get_contents(public_path('css/report/index.css')) !!}
        /* WRAPPER */
        .property-overview {
            margin-top: 32px;
            margin-left: 1rem;
            margin-right: 1rem;
        }

        /* SECTION TITLE */
        .overview-title {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #111827;
            padding-bottom: 8px;
            border-bottom: 2px solid #111827;
            margin-bottom: 16px;
        }

        /* TABLE */

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    {{-- Header --}}
{{--   <x-page propertyName="{{ $property->title }}" :logo="$logo">--}}
       {{-- Property Image --}}
       <x-polaroid-image :imageUrl="$property_image" imageLabel="{{ $property->title }}" />

       {{-- Property Details --}}
       <x-property-details :property="$property" :overview="$overview" />
{{--       <div class="page-break"></div>--}}
       {{-- Market Analysis --}}

       {{-- Sales History --}}
       <x-property-sales-history :sales="$sales_history"/>
       {{-- Spy Hunt --}}
       <x-spy-hunt-section :cache="$spy_hunt_cache"/>
{{--       <div class="page-break"></div>--}}
       {{-- Glow Ups --}}
       <x-glow-up-section :glowUps="$glow_up"/>

        <div class="page-break"></div>
         {{-- Defect Detection Runes --}}
       <x-rune-information-display :data="$runes" :icon="$icon" />
{{--   </x-page>--}}
</body>
</html>

