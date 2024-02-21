/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from "axios";
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from "laravel-echo";

let isProduction = import.meta.env.VITE_WS_CONNECT_PRODUCTION === "true";

// import Pusher from "pusher-js";
// window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: "pusher",
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? "mt1",
//     wsHost: window.location.hostname,
//     wsPort: isProduction ? 6002 : 6001,
//     forceTLS: false,
//     encrypted: isProduction,
//     disableStats: false,
//     enabledTransports: ['ws', 'wss'],
//     disabledTransports: ["sockjs", "xhr_polling", "xhr_streaming"];
// });
