import axios from 'axios';
import { client as precognitionClient } from 'laravel-precognition-vue';

const http = axios.create({
    withCredentials: true,
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
    },
});

const csrfToken =
    typeof window !== 'undefined'
        ? document
              .querySelector('meta[name="csrf-token"]')
              ?.getAttribute('content') ?? ''
        : '';

if (csrfToken) {
    http.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
}

precognitionClient.use(http);

declare global {
    interface Window {
        axios: typeof http;
    }
}

window.axios = http;

export default http;
