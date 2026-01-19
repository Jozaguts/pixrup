@props(['data', 'icon'])

@php
    $rune = $data->filter(function ($rune) {
        return ($rune->rune_value['glow_up_job_id'] ?? false) === 1;
    })->first();
    $payload = $rune->rune_value;
    $defects = collect($payload['defects'] ?? [])
        ->filter(fn ($defect) => ($defect['detected'] ?? false));
@endphp

@if ($defects->isNotEmpty())
    <div class="property-overview">
        <div class="overview-title">PixVision Defect Detection Service</div>

        <table class="signals-table" width="100%" cellpadding="0" cellspacing="0">
            @foreach ($defects as $defectName => $defect)
                <tr>
                    <td class="signal-icon">
                        <img src="{{ $icon }}" alt="Defect Detected" width="16" height="16">
                    </td>
                    <td class="signal-label">
                        {{ ucwords(str_replace('_', ' ', $defectName)) }}
                    </td>

                    <td class="signal-value">
                        DETECTED
                    </td>

                    <td class="signal-detail">
                        {{ strtoupper($defect['severity'] ?? 'N/A') }}
                    </td>
                </tr>
            @endforeach
        </table>

        <x-rune-notice>
            The PixVision Defect Detection Service has identified defects in the property images. Please review the
            details above for more information.
        </x-rune-notice>
    </div>
@endif
