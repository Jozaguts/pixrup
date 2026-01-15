export const defaultRoomTypes = [
    { value: 'living_room', label: 'Living room' },
    { value: 'kitchen', label: 'Kitchen' },
    { value: 'bathroom', label: 'Bathroom' },
    { value: 'bedroom', label: 'Bedroom' },
    { value: 'facade', label: 'Exterior' },
];

export const defaultStyleOptions = [
    { value: 'modern', label: 'Modern' },
    { value: 'minimalist', label: 'Minimalist' },
    { value: 'luxury', label: 'Luxury' },
    { value: 'rustic', label: 'Rustic' },
    { value: 'outdoor_resort', label: 'Outdoor resort' },
];

export const roomTypeInstructionHints: Record<string, string> = {
    living_room: 'Example: Remove the coffee table, add a light beige sectional, and swap the rug to neutral tones.',
    kitchen: 'Example: Replace cabinet fronts with white shaker doors, add brass hardware, and remove countertop clutter.',
    bathroom: 'Example: Replace the mirror with a round black frame, update tile to light gray, and remove the bathmat.',
    bedroom: 'Example: Swap bedding for crisp white linens, add two nightstands, and remove the desk.',
    dining_room: 'Example: Add a wooden dining table with six chairs, remove the sideboard, and add warm pendant lighting.',
    facade: 'Example: Paint the exterior white, replace the front door with natural wood, and add potted plants.',
    outdoor: 'Example: Add a lounge set and umbrella, remove the plastic chairs, and add warm string lights.',
    office: 'Example: Replace the desk with a modern walnut desk, add a bookshelf, and remove clutter.',
};

export const defaultInstructionHint =
    'Example: Remove clutter, update wall colors, and add decor that matches the selected style.';

export const statusTokens: Record<
    string,
    { label: string; badge: string; dot: string; copy: string }
> = {
    pending: {
        label: 'Queued',
        badge: 'bg-background text-accent/70',
        dot: 'bg-primary/50',
        copy: 'Preparing your scene for processing.',
    },
    processing: {
        label: 'Processing',
        badge: 'bg-background text-accent/70',
        dot: 'bg-primary/50',
        copy: 'Applying materials, color, and post-production.',
    },
    done: {
        label: 'Ready',
        badge: 'bg-background text-accent/70',
        dot: 'bg-[#1DBE78]',
        copy: 'The render is ready to use.',
    },
    error: {
        label: 'Error',
        badge: 'bg-background text-accent/70',
        dot: 'bg-[#EA5455]',
        copy: 'Something went wrong with the provider. Try again.',
    },
};
