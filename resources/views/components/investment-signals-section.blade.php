@props(['overview'])

<section>
    <h2>Key Investment Signal</h2>

    <table cellpadding="0" cellspacing="0">
        <thead>
        <tr>
            <th>Key</th>
            <th>Value</th>
            <th>Detail</th>
        </tr>
        </thead>

        <tbody>
        @foreach ($overview as $overviewSignal)
            <tr>
                <td>{{ $overviewSignal['label'] }}</td>
                <td>{{ $overviewSignal['value'] }}</td>
                <td>{{ $overviewSignal['detail'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</section>
