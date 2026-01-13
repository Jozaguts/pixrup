import type { GlowUpJobPayload } from '@/types';

type RealtimeEventMap = {
    'glowup:job': GlowUpJobPayload;
};

const realtimeEventTarget = new EventTarget();

export const emitRealtimeEvent = <K extends keyof RealtimeEventMap>(
    type: K,
    detail: RealtimeEventMap[K],
): void => {
    realtimeEventTarget.dispatchEvent(new CustomEvent(type, { detail }));
};

export const onRealtimeEvent = <K extends keyof RealtimeEventMap>(
    type: K,
    handler: (detail: RealtimeEventMap[K]) => void,
): (() => void) => {
    const listener = (event: Event) => {
        handler((event as CustomEvent).detail as RealtimeEventMap[K]);
    };

    realtimeEventTarget.addEventListener(type, listener as EventListener);

    return () => {
        realtimeEventTarget.removeEventListener(type, listener as EventListener);
    };
};
