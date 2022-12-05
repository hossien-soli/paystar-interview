import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

window.activeBootstrapTooltips = () => {
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
};

activeBootstrapTooltips();

import Swal from 'sweetalert2';
window.Swal = Swal;

window.swalSuccess = (message) => {
    return Swal.fire({
        title: 'عملیات موفق آمیز بود!',
        html: message,
        type: 'success',
        confirmButtonText: 'تایید',
        confirmButtonClass: 'bg-primary',
    });
};

window.swalInfo = (message) => {
    return Swal.fire({
        html: '<h4 style="font-size:20px;">' + message + "</h4>",
        type: 'question',
        confirmButtonText: 'تایید',
        confirmButtonClass: 'bg-primary',
    });
};

window.swalWarning = (message,withTitle=true) => {
    if (withTitle) {
        return Swal.fire({
            title: 'یک مشکل وجود دارد!',
            html: message,
            type: 'warning',
            confirmButtonText: 'تایید',
            confirmButtonClass: 'bg-primary',
        });
    }
    else {
        return Swal.fire({
            html: '<h4 style="font-size:21px;">' + message + "</h4>",
            type: 'warning',
            confirmButtonText: 'تایید',
            confirmButtonClass: 'bg-primary',
        });
    }
};

window.swalError = (message,withTitle=true) => {
    if (withTitle) {
        return Swal.fire({
            title: 'یک مشکل وجود دارد!',
            html: message,
            type: 'error',
            confirmButtonText: 'تایید',
            confirmButtonClass: 'bg-primary',
        });
    }
    else {
        return Swal.fire({
            html: '<h4 style="font-size:21px;">' + message + "</h4>",
            type: 'error',
            confirmButtonText: 'تایید',
            confirmButtonClass: 'bg-primary',
        });
    }
};

window.swalConnectionError = () => {
    return swalError("مشکلی در هنگام ارتباط با سرور پیش آمده! لطفا دوباره تلاش کنید. (در صورت تداوم این مشکل یکبار صفحه را بارگزاری مجدد کنید.)",false);
};

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// import Pusher from 'pusher-js';
// window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     wsHost: import.meta.env.VITE_PUSHER_HOST ? import.meta.env.VITE_PUSHER_HOST : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
//     wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
//     wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
// });
