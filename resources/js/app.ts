import '../css/app.css';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { autoAnimatePlugin } from '@formkit/auto-animate/vue';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { initializeTheme } from './composables/useAppearance';
import './lib/http';
import { gsap, ScrollTrigger } from './lib/gsap';
import { ensureSpringer } from './lib/vendor/springer';
import { ensureStackCards, requestStackCardsUpdate } from './lib/vendor/stackCards';

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
            .use(autoAnimatePlugin)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();
