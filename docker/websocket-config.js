// WebSocket Configuration for Docker Environment
// This file contains the WebSocket configuration for the Poptin Poll Management System

const WebSocketConfig = {
    // Development environment
    development: {
        host: 'localhost',
        port: 8080,
        scheme: 'ws',
        appId: '748313',
        appKey: 'aeg2d2cvyxnltq9mfayp',
        appSecret: 'xj6c42tuecdwsvjysxlh'
    },

    // Production environment
    production: {
        host: window.location.hostname,
        port: 8080,
        scheme: 'wss',
        appId: '748313',
        appKey: 'aeg2d2cvyxnltq9mfayp',
        appSecret: 'xj6c42tuecdwsvjysxlh'
    }
};

// Get current environment
const getCurrentEnvironment = () => {
    return window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
        ? 'development'
        : 'production';
};

// Get WebSocket configuration for current environment
const getWebSocketConfig = () => {
    const env = getCurrentEnvironment();
    return WebSocketConfig[env];
};

// Create WebSocket URL
const createWebSocketUrl = () => {
    const config = getWebSocketConfig();
    return `${config.scheme}://${config.host}:${config.port}`;
};

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        WebSocketConfig,
        getCurrentEnvironment,
        getWebSocketConfig,
        createWebSocketUrl
    };
} else {
    // Browser environment
    window.WebSocketConfig = {
        WebSocketConfig,
        getCurrentEnvironment,
        getWebSocketConfig,
        createWebSocketUrl
    };
}
