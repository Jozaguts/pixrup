@props(['runes'])
<section>
    <h2>PixVision Signals</h2>

    <table>
        <thead>
            <tr>
                <th>Signal</th>
                <th>Confidence</th>
                <th>Summary</th>
            </tr>
        </thead>
        <tbody>
            @foreach($runes['pix_worth'] as $rune)
                <tr>
                    <td>{{ $rune['name'] }}</td>
                    <td>{{ $rune['confidence'] }}</td>
                    <td style="text-align: right">{{ $rune['summary'] ?? '' }}</td>
                </tr>
            @endforeach

    </table>
</section>
