@props(['property', 'overview'])

@php
    $icons = [
        'home' => '<svg viewBox="0 0 24 24" width="14" height="14"><path fill="#111827" d="M12 3L2 12h3v8h5v-5h4v5h5v-8h3z"/></svg>',
        'bed' => '<svg viewBox="0 0 24 24" width="14" height="14"><path fill="#111827" d="M21 10.5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2V20h2v-2h14v2h2v-9.5zM5 14v-3h14v3z"/></svg>',
        'bath' => '<svg viewBox="0 0 24 24" width="14" height="14"><path fill="#111827" d="M7 7V6a5 5 0 0110 0v1h2v2H5V7zm12 4H5v5a4 4 0 004 4h6a4 4 0 004-4z"/></svg>',
        'area' => '<svg viewBox="0 0 24 24" width="14" height="14"><path fill="#111827" d="M3 3h8v2H5v6H3zm18 0v8h-2V5h-6V3zM3 21v-8h2v6h6v2zm18-8v8h-8v-2h6v-6z"/></svg>',
        'status' => '<svg viewBox="0 0 24 24" width="14" height="14"><path fill="#111827" d="M12 2a10 10 0 1010 10A10 10 0 0012 2zm-1 15l-5-5 1.4-1.4L11 14.2l5.6-5.6L18 10z"/></svg>',
    ];
@endphp

<section>
    <h2> Property Overview </h2>

    <table cellpadding="0" cellspacing="0">
        <thead>
        <tr>
            <th>
                Key
            </th>
            <th>
                Value
            </th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Property Type</td>
            <td>{{ $property->property_type }}</td>
        </tr>
        <tr>
            <td>Bedrooms</td>
            <td>{{ $property->bedrooms }}</td>
        <tr>
            <td >Bathrooms</td>
            <td >{{ $property->bathrooms }}</td>
        </tr>
        <tr>
            <td >Square Footage</td>
            <td >{{ number_format($property->square_footage) }} sqft</td>
        </tr>
        <tr>
            <td >Status</td>
            <td >{{ ucfirst($property->status) }}</td>
        </tr>
        </tbody>
    </table>
</section>
