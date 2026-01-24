@props(['title', 'items' => [], 'description' => 'Pix Vision has analyzed the market data and generated the following insights regarding the property. These insights are based on advanced machine learning algorithms and are intended to provide additional context for your evaluation. Please consider these insights alongside other factors when making your decision.'])

<div style="border-radius: 5px; border: 1px solid #bfdbfe; background-color: #eff6ff; padding: 16px; margin:0 5mm"
     xmlns="http://www.w3.org/1999/html">
    <span style="font-size:14px; font-weight: 600; margin-bottom: 1rem">
        {{$title ?? 'Pix Vision Insights Report'}}
    </span>
    <div style="display: flex; gap: 12px; margin-top:1rem">
        {{-- Info Icon --}}
        <div style="width: 100%;">
            <p style="font-size: 12px; color: #1e40af">
                {{ $description }}
            </p>

            {{-- Full-width table --}}
            <div style="margin-left: -16px; margin-right: -16px;">
                <ul style="list-style: none; margin: 0; padding: 0;">
                    @foreach($items as $item)
                        @php $item['confidence'] *= 100 @endphp
                        <li style="display: flex; justify-content: space-between; gap: 16px; padding: 12px 16px; font-size: 12px; {{ !$loop->last ? 'border-bottom: 1px solid #bfdbfe' : '' }}">
                            <span style="color: #1e40af;">
                                {{ $item['summary'] }}
                            </span>
                            <span style="font-weight: 500; color:
                                @if($item['confidence'] >= 90)
                                    #16a34a
                                @elseif($item['confidence'] >= 75)
                                    #d97706
                                @else
                                    #dc2626
                                @endif
                            ;"><small>(model confidence {{ $item['confidence'] }}%)</small>
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
