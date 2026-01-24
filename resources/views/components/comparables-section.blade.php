@props(['cache'])

@php
    $comps = $cache['comps']['sale'] ??  []
@endphp

<section>
    <h2>SpyHunt Sale Comparables</h2>

    <table cellpadding="0" cellspacing="0">
        <thead>
        <tr>
            <th>Address</th>
            <th>Price</th>
            <th>Beds</th>
            <th>Baths</th>
            <th>Sqft</th>
            <th>Distance</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($comps as $sale)
            <tr>
                <td>{{ $sale['address'] ?? '-' }}</td>
                <td>{{ isset($sale['price']) ? '$' . number_format($sale['price']) : '-' }}</td>
                <td>{{ $sale['bedrooms'] ?? '-' }}</td>
                <td>{{ $sale['bathrooms'] ?? '-' }}</td>
                <td>{{ isset($sale['sqft']) ? number_format($sale['sqft']) : '-' }}</td>
                <td>{{ isset($sale['distance']) ? number_format($sale['distance'], 2) : '-' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</section>
