@props(['sales'])
<div class="property-overview">
    <div class="overview-title">Sales History</div>

    <table class="signals-table" width="100%" cellpadding="0" cellspacing="0">
        <thead>
        <tr style="background-color:#cfd8dc">
            <td style="font-weight: 600; text-align:left;">SALE TYPE</td>
            <td style="font-weight: 600; text-align:center;">BOUGHT BY </td>
            <td style="font-weight: 600; text-align:center;">SOLD BY </td>
            <td style="font-weight: 600; text-align:right;">EVENT DATE</td>
        </tr>
        </thead>
        <tbody>
        @foreach ($sales as $sale)
            <tr>
                <td class="signal-label">
                    {{ strtoupper($sale['label']) }}
                </td>

                <td class="signal-value">
                    {{ $sale['buyer'] }}
                </td>
                <td class="signal-value">
                    {{ $sale['seller'] }}
                </td>

                <td class="signal-detail">
                    {{ $sale['detail'] }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
