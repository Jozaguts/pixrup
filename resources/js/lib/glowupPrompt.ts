type RoomType =
    | 'living_room'
    | 'kitchen'
    | 'bathroom'
    | 'bedroom'
    | 'dining_room'
    | 'facade'
    | 'outdoor'
    | 'office';

type Style =
    | 'modern'
    | 'minimalist'
    | 'luxury'
    | 'rustic'
    | 'industrial'
    | 'scandinavian'
    | 'mediterranean'
    | 'outdoor_resort';

const ROOM_LABEL: Record<RoomType, string> = {
    living_room: 'Living room',
    kitchen: 'Kitchen',
    bathroom: 'Bathroom',
    bedroom: 'Bedroom',
    dining_room: 'Dining room',
    facade: 'Exterior',
    outdoor: 'Outdoor / Patio',
    office: 'Office / Studio',
};

const STYLE_LABEL: Record<Style, string> = {
    modern: 'Modern',
    minimalist: 'Minimalist',
    luxury: 'Luxury',
    rustic: 'Rustic',
    industrial: 'Industrial',
    scandinavian: 'Scandinavian',
    mediterranean: 'Mediterranean',
    outdoor_resort: 'Outdoor resort',
};

const STYLE_HINTS: Partial<Record<Style, string>> = {
    modern: 'clean lines, neutral palette, subtle textures, open layout, large windows',
    minimalist:
        'decluttered, functional forms, few materials, lots of negative space',
    luxury: 'premium materials, refined finishes, elegant accents, sophisticated lighting',
    rustic: 'natural wood, stone textures, warm earthy tones, handcrafted feel',
    industrial:
        'exposed concrete, metal frames, visible piping, loft vibe, raw finishes',
    scandinavian:
        'light woods, soft neutrals, cozy textiles, functional simplicity',
    mediterranean:
        'stucco, arches, terracotta, sun-washed colors, natural light',
    outdoor_resort:
        'lush greenery, comfy lounge areas, water features, relaxed ambience',
};

const ROOM_HINTS: Partial<Record<RoomType, string>> = {
    living_room: 'comfortable seating, focal wall, balanced decor, area rug',
    kitchen:
        'practical layout, quality cabinetry, task lighting, clean surfaces',
    bathroom:
        'spa-like, clean fixtures, moisture-safe materials, soft lighting',
    bedroom: 'cozy linens, balanced lighting, calm palette, minimal clutter',
    dining_room: 'inviting table setup, ambient lighting, harmonious decor',
    facade: 'curb appeal, balanced proportions, realistic exterior materials and shadows',
    outdoor: 'weather-appropriate materials, plants, natural textures',
    office: 'ergonomic desk, shelving, organized workspace, soft acoustic elements',
};

type BuildPromptOptions = {
    room: RoomType;
    style: Style;
    includeNegatives?: boolean;
    extraModifiers?: string[];
};

export function buildPrompt({
    room,
    style,
    includeNegatives = true,
    extraModifiers = [],
}: BuildPromptOptions) {
    const roomLabel = ROOM_LABEL[room];
    const styleLabel = STYLE_LABEL[style];

    const core =
        `Enhance the provided photo with subtle, realistic upgrades for a ${styleLabel} ${roomLabel}. ` +
        'Preserve the original layout, perspective, depth, proportions, and camera angle. ' +
        'Do NOT change the structure, room shape, walls, doors, windows, or furniture placement. ' +
        'Improve materials, finishes, lighting, textures, and overall aesthetics only. ' +
        'Keep all existing objects in their original positions. ' +
        'Photorealistic, natural lighting, realistic shadows.';

    const roomHints = ROOM_HINTS[room]
        ? ` Room cues: ${ROOM_HINTS[room]}.`
        : '';
    const styleHints = STYLE_HINTS[style]
        ? ` Style cues: ${STYLE_HINTS[style]}.`
        : '';

    const modifiers =
        extraModifiers.length > 0
            ? ` Additional modifiers: ${extraModifiers.join(', ')}.`
            : '';

    const positive = `${core}${roomHints}${styleHints}${modifiers}`.trim();

    const negative = includeNegatives
        ? 'new layout, new room, altered geometry, incorrect perspective, warped walls, moved furniture, extra objects, missing objects, fantasy, CGI look, cartoon, illustration, dramatic redesign'
        : '';

    return { positive, negative };
}

export type { RoomType, Style };
