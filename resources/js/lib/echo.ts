import  Echo from 'laravel-echo';
import Pusher from 'pusher-js';

declare global {
    interface Window {
        Echo?: Echo;
        Pusher?: typeof Pusher;
    }
}

const bootstrapEcho = () => {
    if (typeof window === 'undefined') {
        return;
    }

    const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY;

    if (!pusherKey) {
        return;
    }

    window.Pusher = Pusher;

    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: pusherKey,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
        wsHost:
            import.meta.env.VITE_PUSHER_HOST ??
            `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1'}.pusher.com`,
        wsPort: Number(import.meta.env.VITE_PUSHER_PORT ?? 443),
        wssPort: Number(import.meta.env.VITE_PUSHER_PORT ?? 443),
        forceTLS:
            (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https' ||
            (import.meta.env.DEV ? false : true),
        enabledTransports: ['ws', 'wss'],
        disableStats: true,
        // authorizer: (channel) => ({
        //     authorize: (socketId: string, callback: (status: boolean, data?: unknown) => void) => {
        //         window
        //             .axios?.post('/broadcasting/auth', {
        //                 socket_id: socketId,
        //                 channel_name: channel.name,
        //             })
        //             .then((response) => {
        //                 callback(true, response.data);
        //             })
        //             .catch((error) => {
        //                 callback(false, error);
        //             });
        //     },
        // }),
    });
};

bootstrapEcho();
