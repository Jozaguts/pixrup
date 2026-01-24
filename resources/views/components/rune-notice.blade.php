@props(['title' => ''])

<div
    style="
        margin-top: 12px;
        font-size: 12px;
        line-height: 1.35;
        color: black;
        font-weight: 600;
    "
>
    <div
        style="
            font-weight: bold;
            margin-bottom: 4px;
            color: #333333;
        "
    >
        {{ $title }}
    </div>

    <div style="text-align: justify; background-color:#eceff1; padding:1rem; border-radius:4px; margin-bottom: 1rem;">
        {{ $slot }}
    </div>
</div>
