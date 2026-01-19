@props(['cache'])
@php
 $marketData = $cache['market_snapshot'] ?? [];
 if($marketData){
     ksort($marketData);
 }
 $estimatePrice = $cache['value_estimate']['price'] ?? null;

 function handleFormat($key, $value): string
 {
    return match($key) {
       "trend30d","trend30dTotal" => number_format($value,2) . '%',
       "avgRentPerFt","avgRentPrice","avgSalePrice", "avgPricePerFt" => '$' .number_format($value, 2),
       default => $value,
    };
 }
@endphp

<div class="property-overview">
        <div class="overview-title">Spyhunt Market Analytics</div>
        <table class="overview-table" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td class="label">Estimate Price</td>
                <td class="value">${{ number_format($estimatePrice, 2) }}</td>
            </tr>
            @foreach($marketData as $label => $value)
            <tr>
                <td class="label">{{\Illuminate\Support\Str::headline($label)}}</td>
                <td class="value">{{ handleFormat($label, $value) }}</td>
            </tr>
            @endforeach
        </table>
</div>
