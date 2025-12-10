import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Laravel Echo - Real-time Notifications via Reverb WebSocket
 */
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

/**
 * Real-time notification listener
 * Mendengarkan event dari channel user dan trigger refresh notifikasi Filament
 */
document.addEventListener('DOMContentLoaded', function () {
    // Ambil user ID dari meta tag yang akan kita tambahkan
    const userIdMeta = document.querySelector('meta[name="user-id"]');
    if (!userIdMeta) return;

    const userId = userIdMeta.getAttribute('content');
    if (!userId) return;

    console.log('🔔 Subscribing to notification channel for user:', userId);

    // Subscribe ke private channel user
    window.Echo.private(`App.Models.User.${userId}`)
        .listen('.sop.status.changed', (event) => {
            console.log('🎉 Real-time notification received:', event);

            // Trigger refresh pada komponen notifikasi Filament
            if (window.Livewire) {
                // Refresh semua komponen database-notifications
                window.Livewire.dispatch('databaseNotificationsSent');

                // Tampilkan browser notification juga
                if (Notification.permission === 'granted') {
                    new Notification(event.title, {
                        body: event.body,
                        icon: '/favicon.ico'
                    });
                }
            }
        });

    // Request permission untuk browser notification
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
});

