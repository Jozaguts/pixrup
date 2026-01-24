<figure class="polaroid">
    <div class="polaroid-caption">{{ $imageLabel ?? '' }}</div>
    <img
        src="{{ $imageUrl }}"
        alt="{{ $imageLabel ??  'Polaroid Picture' }}"
    />
</figure>
