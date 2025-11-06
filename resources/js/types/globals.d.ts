import { AppPageProps } from '@/types/index';

// Extend ImportMeta interface for Vite...
declare module 'vite/client' {
    interface ImportMetaEnv {
        readonly VITE_APP_NAME: string;
        [key: string]: string | boolean | undefined;
    }

    interface ImportMeta {
        readonly env: ImportMetaEnv;
        readonly glob: <T>(pattern: string) => Record<string, () => Promise<T>>;
    }
}

declare module '@inertiajs/core' {
    interface PageProps extends InertiaPageProps, AppPageProps {}
}

declare module 'vue' {
    interface ComponentCustomProperties {
        $inertia: typeof Router;
        $page: Page;
        $headManager: ReturnType<typeof createHeadManager>;
    }
}

declare global {
    interface Window {
        gsap: typeof import('gsap').gsap;
        ScrollTrigger: typeof import('gsap/ScrollTrigger').ScrollTrigger;
        ensureSpringer: typeof import('../lib/vendor/springer').ensureSpringer;
        ensureStackCards: typeof import('../lib/vendor/stackCards').ensureStackCards;
        requestStackCardsUpdate: typeof import('../lib/vendor/stackCards').requestStackCardsUpdate;
        Springer?: { default: (tension?: number, friction?: number) => (t: number) => number };
        __stackCardsReady?: boolean;
    }
}
