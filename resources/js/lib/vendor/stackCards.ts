declare global {
    interface Window {
        __stackCardsReady?: boolean;
        __stackCardsLoadingPromise?: Promise<boolean>;
    }
}

const SCRIPT_SRC = '/vendor/stack-card.min.js';

const loadStackCardsScript = (): Promise<boolean> => {
    if (typeof window === 'undefined' || typeof document === 'undefined') {
        return Promise.resolve(false);
    }

    if (window.__stackCardsReady) {
        return Promise.resolve(true);
    }

    if (!window.__stackCardsLoadingPromise) {
        window.__stackCardsLoadingPromise = new Promise((resolve, reject) => {
            const existing = document.querySelector<HTMLScriptElement>(
                `script[src="${SCRIPT_SRC}"]`,
            );
            if (existing) {
                if (existing.dataset.loaded === 'true') {
                    window.__stackCardsReady = true;
                    resolve(true);
                } else {
                    existing.addEventListener(
                        'load',
                        () => {
                            existing.dataset.loaded = 'true';
                            window.__stackCardsReady = true;
                            resolve(true);
                        },
                        { once: true },
                    );
                    existing.addEventListener(
                        'error',
                        () => reject(new Error(`Failed to load ${SCRIPT_SRC}`)),
                        { once: true },
                    );
                }
                return;
            }

            const script = document.createElement('script');
            script.src = SCRIPT_SRC;
            script.async = true;
            script.dataset.loaded = 'false';
            script.addEventListener(
                'load',
                () => {
                    script.dataset.loaded = 'true';
                    window.__stackCardsReady = true;
                    resolve(true);
                },
                { once: true },
            );
            script.addEventListener(
                'error',
                () => reject(new Error(`Failed to load ${SCRIPT_SRC}`)),
                { once: true },
            );
            document.head.appendChild(script);
        });
    }

    return window.__stackCardsLoadingPromise;
};

export const ensureStackCards = () => loadStackCardsScript();

export const requestStackCardsUpdate = () => {
    if (typeof window === 'undefined' || !window.__stackCardsReady) {
        return;
    }

    const elements = document.querySelectorAll('.js-stack-cards');
    elements.forEach((element) => {
        element.dispatchEvent(new CustomEvent('resize-stack-cards'));
    });
};
