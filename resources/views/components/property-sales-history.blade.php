@props(['sales'])

<section>
    <h2>Sales History</h2>

    <table cellpadding="0" cellspacing="0">
        <thead>
        <tr>
            <th>
                Sale Category
            </th>
            <th>
                Buyer
            </th>
            <th>
                Seller
            </th>
            <th>
                Event Date
            </th>
        </tr>
        </thead>

        <tbody>
        @foreach ($sales as $sale)
            <tr>
                <td>{{ strtoupper($sale['label']) }}</td>
                <td>{{ $sale['buyer'] }}</td>
                <td>{{ $sale['seller'] }}</td>
                <td>{{ $sale['detail'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</section>
