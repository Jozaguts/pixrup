declare global {
    interface Window {
        Springer?: {
            default: (
                tension?: number,
                friction?: number,
            ) => (t: number) => number;
        };
        __springerLoadingPromise?: Promise<typeof window.Springer | null>;
    }
}

const SCRIPT_SRC = '/vendor/springer.min.js';

const loadSpringerScript = (): Promise<typeof window.Springer | null> => {
    if (typeof window === 'undefined' || typeof document === 'undefined') {
        return Promise.resolve(null);
    }

    if (window.Springer?.default) {
        return Promise.resolve(window.Springer);
    }

    if (!window.__springerLoadingPromise) {
        window.__springerLoadingPromise = new Promise((resolve, reject) => {
            const existing = document.querySelector<HTMLScriptElement>(
                `script[src="${SCRIPT_SRC}"]`,
            );
            if (existing) {
                existing.addEventListener(
                    'load',
                    () => resolve(window.Springer ?? null),
                    { once: true },
                );
                existing.addEventListener(
                    'error',
                    () => reject(new Error(`Failed to load ${SCRIPT_SRC}`)),
                    { once: true },
                );
                return;
            }

            const script = document.createElement('script');
            script.src = SCRIPT_SRC;
            script.async = true;
            script.onload = () => resolve(window.Springer ?? null);
            script.onerror = () =>
                reject(new Error(`Failed to load ${SCRIPT_SRC}`));
            document.head.appendChild(script);
        });
    }

    return window.__springerLoadingPromise;
};

export const ensureSpringer = () => loadSpringerScript();
