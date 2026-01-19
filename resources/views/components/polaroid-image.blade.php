<figure class="polaroid">
    <img
        src="{{ $imageUrl }}"
        alt="{{ $imageLabel ??  'Polaroid Picture' }}"
    />
    <div class="polaroid-caption">{{ $imageLabel ?? '' }}</div>
</figure>
