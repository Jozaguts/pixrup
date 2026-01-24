@props(['cache'])

@php
    $marketData = $cache['market_snapshot'] ?? [];
    if ($marketData) {
        ksort($marketData);
    }

    $estimatePrice = $cache['value_estimate']['price'] ?? null;

    function handleFormat($key, $value): string
    {
        return match ($key) {
            'trend30d', 'trend30dTotal' => number_format($value, 2) . '%',
            'avgRentPerFt', 'avgRentPrice', 'avgSalePrice', 'avgPricePerFt' => '$' . number_format($value, 2),
            default => $value,
        };
    }
@endphp

<section>
    <h2>SpyHunt Market Analytics</h2>

    <table cellpadding="0" cellspacing="0">
        <thead>
        <tr>
            <th>Indicator</th>
            <th>Value</th>
        </tr>
        </thead>

        <tbody>
        @foreach ($marketData as $label => $value)
            <tr>
                <td>{{ \Illuminate\Support\Str::headline($label) }}</td>
                <td>{{ handleFormat($label, $value) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</section>
