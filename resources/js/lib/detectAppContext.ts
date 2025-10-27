export function detectAppContext() {
    if (typeof window === 'undefined') {
        return {
            isInPWA: false,
            isCapacitor: false,
            isMobile: false,
        };
    }

    const isInPWA =
        window.matchMedia?.('(display-mode: standalone)')?.matches ?? false;
    const isCapacitor = Boolean(
         
        (window as any).Capacitor,
    );
    const isMobile = /Android|iPhone|iPad/i.test(
        typeof navigator !== 'undefined' ? navigator.userAgent : '',
    );

    return { isInPWA, isCapacitor, isMobile };
}
