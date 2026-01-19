import { autoAnimatePlugin } from '@formkit/auto-animate/vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import '../css/app.css';
import { initializeTheme } from './composables/useAppearance';
import './lib/echo';
import { gsap, ScrollTrigger } from './lib/gsap';
import './lib/http';
import { ensureSpringer } from './lib/vendor/springer';
import {
    ensureStackCards,
    requestStackCardsUpdate,
} from './lib/vendor/stackCards';
import 'leaflet/dist/leaflet.css';
import 'vue-map-ui/dist/style.css';
import 'vue-map-ui/dist/theme-all.css';
// @ts-expect-error vue3-easy-data-table ships broken types
import Vue3EasyDataTable from 'vue3-easy-data-table';
import 'vue3-easy-data-table/dist/style.css';
import { createPinia } from 'pinia';
import { PiniaColada } from '@pinia/colada';
import { registerSW } from 'virtual:pwa-register';
window.gsap = gsap;
window.ScrollTrigger = ScrollTrigger;
window.ensureSpringer = ensureSpringer;
window.ensureStackCards = ensureStackCards;
window.requestStackCardsUpdate = requestStackCardsUpdate;

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(createPinia())
            .use(PiniaColada,{
                    queryOptions: {
                            // 🔄 auto refetch every 5 minutes
                            gcTime:300_000,
                    },
                })
            .use(autoAnimatePlugin)
            .component('data-table', Vue3EasyDataTable)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();

if (import.meta.env.PROD) {
    registerSW({ immediate: true });
}
