export const defaultRoomTypes = [
    { value: 'living_room', label: 'Living room' },
    { value: 'kitchen', label: 'Kitchen' },
    { value: 'bathroom', label: 'Bathroom' },
    { value: 'bedroom', label: 'Bedroom' },
    { value: 'facade', label: 'Facade' },
];

export const defaultStyleOptions = [
    { value: 'modern', label: 'Modern' },
    { value: 'minimalist', label: 'Minimalist' },
    { value: 'luxury', label: 'Luxury' },
    { value: 'rustic', label: 'Rustic' },
    { value: 'outdoor_resort', label: 'Outdoor resort' },
];

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
