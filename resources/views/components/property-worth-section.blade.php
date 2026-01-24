@props(['worth', 'runes'])

@php
    $comparables = $worth->comparables;
@endphp

<section>
    <h2>Nearby Sales (90 days)</h2>

    <table cellpadding="0" cellspacing="0">
        <thead>
        <tr>
            <th>Address</th>
            <th>Beds</th>
            <th>Baths</th>
            <th>Sqft</th>
            <th>Distance</th>
        </tr>
        </thead>

        <tbody>
        @foreach($comparables as $comparable)
            <tr>
                <td>{{ $comparable['address']['address_full'] }}</td>
                <td>{{ $comparable['info']['beds'] }}</td>
                <td>{{ $comparable['info']['baths'] }}</td>
                <td>{{ $comparable['info']['sqft'] }}</td>
                <td>{{ number_format($comparable['info']['distance_from_subject'], 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</section>
