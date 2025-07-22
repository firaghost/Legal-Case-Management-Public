import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { toast } from '@/Plugins/toast';

// Configure axios
window.axios = axios;

// Set default configuration
window.axios.defaults.withCredentials = true; // Enable sending cookies with requests
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';

// Remove any existing request interceptors that might be setting unsafe headers
const requestInterceptor = window.axios.interceptors.request.use(
    config => config,
    error => Promise.reject(error)
);

// Clear any existing interceptors to prevent duplicates
window.axios.interceptors.request.eject(requestInterceptor);

// Add a new request interceptor
window.axios.interceptors.request.use(
    config => {
        // Initialize headers if they don't exist
        config.headers = config.headers || {};
        config.headers.common = config.headers.common || {};
        
        // Skip for external URLs
        if (config.url.startsWith('http') && !config.url.includes(window.location.host)) {
            return config;
        }
        
        // Don't set unsafe headers
        ['Cookie', 'Origin', 'Referer', 'User-Agent'].forEach(header => {
            if (config.headers[header]) {
                delete config.headers[header];
            }
            if (config.headers.common && config.headers.common[header]) {
                delete config.headers.common[header];
            }
        });
        
        return config;
    },
    error => Promise.reject(error)
);

// Function to get CSRF token from meta tag
function getCSRFToken() {
    return document.head.querySelector('meta[name="csrf-token"]')?.content;
}

// Set CSRF token for all requests
const csrfToken = getCSRFToken();
if (csrfToken) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
    // Also set as a default param for POST requests
    window.axios.defaults.params = {};
    window.axios.defaults.params['_token'] = csrfToken;
} else {
    console.error('CSRF token not found in meta tag');
}

// Add request interceptor to ensure CSRF token is always set
window.axios.interceptors.request.use(config => {
    // Skip for external URLs
    if (config.url.startsWith('http') && !config.url.includes(window.location.host)) {
        return config;
    }
    
    // For all requests, ensure CSRF token is set
    const token = getCSRFToken();
    if (token) {
        config.headers['X-CSRF-TOKEN'] = token;
        if (['post', 'put', 'patch', 'delete'].includes(config.method)) {
            config.data = config.data || {};
            if (config.data instanceof FormData) {
                config.data.append('_token', token);
            } else if (typeof config.data === 'object') {
                config.data._token = token;
            }
        }
    }
    return config;
}, error => {
    return Promise.reject(error);
});

// Get auth token from storage (if using token-based auth)
const authToken = localStorage.getItem('auth_token');
if (authToken) {
    window.axios.defaults.headers.common['Authorization'] = `Bearer ${authToken}`;
}

// Configure Pusher and Echo
window.Pusher = Pusher;

// Only initialize Pusher if the required environment variables are set
const pusherConfig = {
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY || '',
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1',
    wsHost: import.meta.env.VITE_PUSHER_HOST || `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1'}.pusher.com`,
    wsPort: import.meta.env.VITE_PUSHER_PORT || 80,
    wssPort: import.meta.env.VITE_PUSHER_PORT || 443,
    forceTLS: (import.meta.env.VITE_PUSHER_SCHEME || 'https') === 'https',
    disableStats: true,
    enabledTransports: ['ws', 'wss'],
    enabled: true,
    client: {
        enabledTransports: ['ws', 'wss'],
        disableStats: true
    },
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-TOKEN': getCSRFToken() || '',
            'Authorization': authToken ? `Bearer ${authToken}` : '',
            'Accept': 'application/json',
        },
    },
    // Enable logging in development
    logToConsole: import.meta.env.DEV,
};

// Initialize Echo
window.Echo = new Echo({
    ...pusherConfig,
    // Add custom Pusher client configuration
    client: new Pusher(pusherConfig.key, {
        cluster: pusherConfig.cluster,
        wsHost: pusherConfig.wsHost,
        wsPort: pusherConfig.wsPort,
        wssPort: pusherConfig.wssPort,
        forceTLS: pusherConfig.forceTLS,
        enabledTransports: pusherConfig.enabledTransports,
        disableStats: pusherConfig.disableStats,
        auth: pusherConfig.auth,
        authEndpoint: pusherConfig.authEndpoint,
    }),
});

// Add authentication headers for axios
window.axios.interceptors.request.use((config) => {
    // Only add X-Socket-ID if Echo is connected
    if (window.Echo?.socketId()) {
        config.headers['X-Socket-ID'] = window.Echo.socketId();
    }
    
    // Add auth token if it exists
    if (authToken && !config.headers['Authorization']) {
        config.headers['Authorization'] = `Bearer ${authToken}`;
    }
    
    return config;
}, (error) => {
    return Promise.reject(error);
});

// Connection status tracking
let connectionRetryCount = 0;
const MAX_RETRIES = 5;
const RETRY_DELAY = 3000; // 3 seconds

// Connection state management
const connectionState = {
    isConnected: false,
    lastConnectionAttempt: null,
    retryCount: 0,
};

// Handle connection state changes
const handleConnectionStateChange = (state) => {
    console.log('WebSocket connection state changed:', state);
    
    switch (state) {
        case 'connected':
            connectionState.isConnected = true;
            connectionState.retryCount = 0;
            toast.success('Connected to chat');
            break;
            
        case 'disconnected':
            connectionState.isConnected = false;
            toast.warning('Disconnected from chat');
            break;
            
        case 'failed':
            connectionState.isConnected = false;
            connectionState.retryCount++;
            
            if (connectionState.retryCount <= MAX_RETRIES) {
                const delay = Math.min(RETRY_DELAY * Math.pow(2, connectionState.retryCount - 1), 30000);
                toast.warning(`Connection lost. Retrying in ${delay/1000} seconds... (${connectionState.retryCount}/${MAX_RETRIES})`);
                
                setTimeout(() => {
                    window.Echo.connector.pusher.connect();
                }, delay);
            } else {
                toast.error('Failed to connect to chat. Please refresh the page.');
            }
            break;
            
        default:
            console.log('Connection state:', state);
    }
};

// Set up Pusher connection event listeners
if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
    const { connection } = window.Echo.connector.pusher;
    
    // Connection state changes
    connection.bind('state_change', (states) => {
        console.log('Pusher connection state change:', states);
        
        if (states.current === 'connected') {
            handleConnectionStateChange('connected');
        } else if (states.current === 'unavailable' || states.current === 'failed') {
            handleConnectionStateChange('failed');
        } else if (states.current === 'disconnected') {
            handleConnectionStateChange('disconnected');
        }
    });
    
    // Connection established
    connection.bind('connected', () => {
        console.log('Pusher connected');
        handleConnectionStateChange('connected');
    });
    
    // Connection lost
    connection.bind('disconnected', () => {
        console.log('Pusher disconnected');
        handleConnectionStateChange('disconnected');
    });
    
    // Connection error
    connection.bind('error', (error) => {
        console.error('Pusher connection error:', error);
        handleConnectionStateChange('failed');
    });
    
    // Connection retry
    connection.bind('connecting', () => {
        console.log('Pusher connecting...');
    });
}



// Export connection state for use in components
export const isConnected = () => connectionState.isConnected;
export const getConnectionState = () => connectionState;
