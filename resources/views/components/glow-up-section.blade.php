@props(['glowUps' => [], 'runes' => [], 'icon' => ''])
{{-- Glow Ups Section --}}
<section>
    @foreach($glowUps as $key => $glowUp)
        <h2 style="border-bottom: solid gray 2px; padding-bottom: 1rem; padding-left: 1rem; padding-right: 1rem;">Glow
            Up - {{$key + 1}} of {{ count($glowUps) }}</h2>
        <x-polaroid-image :imageUrl="$glowUp->before_url" imageLabel="BEFORE"/>

        <x-polaroid-image :imageUrl="$glowUp->after_url" imageLabel="AFTER"/>

        @php
            $rune = $runes['pix_glow_up']['defect_detection_'.$glowUp->id] ?? null;
        @endphp

        <x-pix-vision-rune-report :items="$rune['items']" description="Pix Vision Image inspected the before image and has detected visible issues in the property."/>
    @endforeach
</section>
