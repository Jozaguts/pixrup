@props(['rune', 'icon'])

@if (count($rune['defects']) > 0)

    <section>
        <x-rune-notice>
            The PixVision Defect Detection Service has identified defects in the property images.
            Please review the details below for more information.
        </x-rune-notice>

        <h2>PixVision Defect Detection</h2>

        <table cellpadding="0" cellspacing="0">
            <thead>
            <tr>
                <th>Key</th>
                <th>Value</th>
                <th>Severity</th>
            </tr>
            </thead>

            <tbody>
            @foreach ($rune['defects'] as $defectName => $defect)
                <tr>
                    <td>
                        {{ ucwords(str_replace('_', ' ', $defectName)) }}
                    </td>
                    <td>
                        {{ $defect['detected'] ? 'DETECTED' : 'CLEAR' }}
                    </td>
                    <td>
                        {{ strtoupper($defect['severity'] ?? 'N/A') }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </section>
@endif
