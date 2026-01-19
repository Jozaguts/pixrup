@props(['glowUps' => []])
{{-- Glow Ups Section --}}
<div class="property-overview">

<div class="overview-title">Glow Ups</div>
    @foreach($glowUps as $glowUp)
        <x-polaroid-image :imageUrl="$glowUp->before_url" imageLabel="Before" />

        <x-polaroid-image :imageUrl="$glowUp->after_url" imageLabel="After" />
    @endforeach
</div>
