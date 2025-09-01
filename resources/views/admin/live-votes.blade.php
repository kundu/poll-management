@extends('admin.layouts.app')

@section('title', 'Live Votes')

@section('content')
<div class="live-votes-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <h1 class="page-title">Live Votes Dashboard</h1>
            <p class="page-subtitle">Real-time monitoring of all active polls and voting activity</p>
        </div>
        <div class="header-actions">
            <div class="connection-status">
                <div id="connectionStatus" class="status-indicator disconnected"></div>
                <span id="connectionText" class="status-text">Disconnected</span>
                <button id="testConnection" class="btn btn-sm btn-outline-primary ml-2" onclick="testWebSocketConnection()">
                    Test Connection
                </button>
            </div>
        </div>
    </div>



    <!-- Live Polls Grid -->
    <div class="polls-section">
        <div class="section-header">
            <h2>Live Polls</h2>
        </div>

        <div class="polls-grid" id="pollsGrid">
            <!-- Polls will be loaded here dynamically -->
        </div>

        <div class="load-more-section" id="loadMoreSection" style="display: none;">
            <div class="text-center">
                <p class="text-muted">All polls loaded</p>
            </div>
        </div>

        <div class="load-more-container" id="loadMoreContainer" style="display: none;">
            <div class="text-center">
                <button class="btn btn-primary btn-large" id="loadMoreBtn" onclick="loadMorePolls()">
                    <i class="fas fa-plus"></i>
                    Load More Polls
                </button>
            </div>
        </div>

        <div class="loading-state" id="loadingState">
            <div class="spinner"></div>
            <p>Loading active polls...</p>
        </div>

        <div class="empty-state" id="emptyState" style="display: none;">
            <div class="empty-icon">
                <i class="fas fa-poll"></i>
            </div>
            <h3>No Active Polls</h3>
            <p>There are currently no active polls to display.</p>
        </div>
    </div>
</div>

<!-- Laravel Echo WebSocket Connection Script -->
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    // WebSocket configuration
    const REVERB_APP_KEY = '{{ config("broadcasting.connections.reverb.key") }}';
    const REVERB_HOST = '{{ config("broadcasting.connections.reverb.options.host") }}';
    const REVERB_PORT = '{{ config("broadcasting.connections.reverb.options.port") }}';
    const REVERB_SCHEME = '{{ config("broadcasting.connections.reverb.options.scheme") }}';

    let pusher = null;
    let channel = null;
    let isConnected = false;

    // Pagination variables
    let currentPage = 1;
    let hasMorePolls = true;
    const pollsPerPage = 10;

    // Test WebSocket connection
    function testWebSocketConnection() {
        console.log('Testing WebSocket connection...');
        console.log('Reverb Config:', {
            appKey: REVERB_APP_KEY,
            host: REVERB_HOST,
            port: REVERB_PORT,
            scheme: REVERB_SCHEME
        });

        if (pusher) {
            pusher.disconnect();
        }
        initializeWebSocket();
    }

    // Initialize WebSocket connection using Pusher
    function initializeWebSocket() {
                try {
            console.log('Attempting to connect to Reverb WebSocket...');
            console.log('Configuration:', {
                appKey: REVERB_APP_KEY,
                host: REVERB_HOST,
                port: REVERB_PORT,
                scheme: REVERB_SCHEME
            });
            updateConnectionStatus(false, 'Connecting...');

            // Initialize Pusher with Reverb configuration
            pusher = new Pusher(REVERB_APP_KEY, {
                wsHost: REVERB_HOST,
                wsPort: REVERB_PORT,
                wssPort: REVERB_PORT,
                forceTLS: REVERB_SCHEME === 'https',
                enabledTransports: ['ws', 'wss'],
                disableStats: true,
                cluster: 'mt1', // Required by Pusher but ignored by Reverb
                encrypted: REVERB_SCHEME === 'https',
            });

            console.log('Pusher instance created successfully');

                        // Connection event handlers
            pusher.connection.bind('connecting', function() {
                console.log('WebSocket connecting...');
                updateConnectionStatus(false, 'Connecting...');
            });

            pusher.connection.bind('connected', function() {
                console.log('WebSocket connected successfully');
                console.log('Connection state:', pusher.connection.state);
                isConnected = true;
                updateConnectionStatus(true, 'Connected');

                // Subscribe to admin-votes channel
                subscribeToChannel();
            });

            pusher.connection.bind('disconnected', function() {
                console.log('WebSocket disconnected');
                isConnected = false;
                updateConnectionStatus(false, 'Disconnected');
            });

            pusher.connection.bind('error', function(error) {
                console.error('WebSocket error:', error);
                console.error('Error details:', {
                    type: error.type,
                    error: error.error,
                    data: error.data
                });
                isConnected = false;
                updateConnectionStatus(false, 'Connection Error');
            });

            pusher.connection.bind('failed', function() {
                console.error('WebSocket connection failed');
                console.error('Connection state:', pusher.connection.state);
                isConnected = false;
                updateConnectionStatus(false, 'Connection Failed');
            });

            pusher.connection.bind('unavailable', function() {
                console.error('WebSocket unavailable');
                isConnected = false;
                updateConnectionStatus(false, 'Unavailable');
            });

        } catch (error) {
            console.error('Failed to initialize WebSocket:', error);
            updateConnectionStatus(false, 'Connection Failed');
            isConnected = false;
        }
    }

        // Subscribe to admin-votes channel
    function subscribeToChannel() {
        if (pusher && isConnected) {
            try {
                console.log('Attempting to subscribe to admin-votes channel...');
                channel = pusher.subscribe('admin-votes');

                channel.bind('pusher:subscription_succeeded', function(data) {
                    console.log('Successfully subscribed to admin-votes channel');
                    console.log('Subscription data:', data);
                });

                channel.bind('pusher:subscription_error', function(error) {
                    console.error('Subscription error:', error);
                });

                channel.bind('vote.cast', function(data) {
                    console.log('Vote update received:', data);
                    handleVoteUpdate(data);
                });

                console.log('Channel subscription initiated');
            } catch (error) {
                console.error('Failed to subscribe to channel:', error);
            }
        } else {
            console.warn('Cannot subscribe: pusher not connected');
        }
    }

    // Close WebSocket connection
    function closeWebSocket() {
        if (pusher) {
            pusher.disconnect();
            pusher = null;
            channel = null;
        }
    }



        // Handle vote updates from WebSocket
    function handleVoteUpdate(data) {
        console.log('Vote update received:', data);

        // Update the specific poll option
        updatePollOption(data.poll_id, data.option_id, data.vote_count, data.total_votes, data.percentage);

        // Update stats
        updateStats();

        // Show notification
        showVoteNotification(data);

        // Animate connection indicator to show live activity
        animateConnectionIndicator();
    }

    // Update poll option display
    function updatePollOption(pollId, optionId, voteCount, totalVotes, percentage) {
        const optionElement = document.querySelector(`[data-poll-id="${pollId}"][data-option-id="${optionId}"]`);
        if (optionElement) {
            // Update vote count
            const voteCountElement = optionElement.querySelector('.option-vote-count');
            if (voteCountElement) {
                voteCountElement.textContent = voteCount;
            }

            // Update percentage
            const percentageElement = optionElement.querySelector('.option-percentage');
            if (percentageElement) {
                percentageElement.textContent = `${percentage.toFixed(1)}%`;
            }

            // Update progress bar
            const progressBar = optionElement.querySelector('.option-progress');
            if (progressBar) {
                progressBar.style.width = `${percentage}%`;
            }

            // Update total votes for the poll
            const pollTotalElement = document.querySelector(`[data-poll-id="${pollId}"] .poll-total-votes`);
            if (pollTotalElement) {
                pollTotalElement.textContent = totalVotes;
            }

            // Add highlight animation
            optionElement.classList.add('vote-update-highlight');
            setTimeout(() => {
                optionElement.classList.remove('vote-update-highlight');
            }, 2000);
        }
    }

        // Update connection status
    function updateConnectionStatus(connected, text) {
        const statusIndicator = document.getElementById('connectionStatus');
        const statusText = document.getElementById('connectionText');

        if (connected) {
            statusIndicator.className = 'status-indicator connected animated';
            statusText.textContent = text;
        } else {
            statusIndicator.className = 'status-indicator disconnected';
            statusText.textContent = text;
        }
    }

    // Animate connection indicator for live activity
    function animateConnectionIndicator() {
        const statusIndicator = document.getElementById('connectionStatus');
        if (statusIndicator && statusIndicator.classList.contains('connected')) {
            // Add a quick flash animation
            statusIndicator.classList.add('activity-flash');
            setTimeout(() => {
                statusIndicator.classList.remove('activity-flash');
            }, 300);
        }
    }

    // Show vote notification
    function showVoteNotification(data) {
        const notification = document.createElement('div');
        notification.className = 'vote-notification';
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-vote-yea"></i>
                <span>New vote on "${data.poll_title}" - ${data.option_text}</span>
            </div>
        `;

        document.body.appendChild(notification);

        // Remove notification after 5 seconds
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }

    // Load active polls with existing vote data
    async function loadActivePolls(resetPagination = true) {
        try {
            const loadingState = document.getElementById('loadingState');
            const emptyState = document.getElementById('emptyState');
            const pollsGrid = document.getElementById('pollsGrid');
            const loadMoreBtn = document.getElementById('loadMoreBtn');

            if (resetPagination) {
                currentPage = 1;
                hasMorePolls = true;
                pollsGrid.innerHTML = '';
            }

            loadingState.style.display = 'block';
            emptyState.style.display = 'none';

            const response = await fetch(`${API_BASE_URL}/admin/polls/vote-summary?status=1&per_page=${pollsPerPage}&page=${currentPage}`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${getAuthToken()}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Failed to fetch polls');
            }

            const data = await response.json();

            if (data.success && data.data.polls.length > 0) {
                displayPolls(data.data.polls, !resetPagination);
                updateStats();

                                // Check if there are more polls
                hasMorePolls = data.data.polls.length === pollsPerPage;
                const loadMoreContainer = document.getElementById('loadMoreContainer');
                loadMoreContainer.style.display = hasMorePolls ? 'block' : 'none';

                // Show "All polls loaded" message when no more polls
                const loadMoreSection = document.getElementById('loadMoreSection');
                if (!hasMorePolls && !resetPagination) {
                    loadMoreSection.style.display = 'block';
                } else {
                    loadMoreSection.style.display = 'none';
                }

                if (!resetPagination) {
                    currentPage++;
                }
            } else {
                if (resetPagination) {
                    emptyState.style.display = 'block';
                }
                                hasMorePolls = false;
                const loadMoreContainer = document.getElementById('loadMoreContainer');
                loadMoreContainer.style.display = 'none';

                // Show "All polls loaded" message
                const loadMoreSection = document.getElementById('loadMoreSection');
                loadMoreSection.style.display = 'block';
            }

            loadingState.style.display = 'none';

        } catch (error) {
            console.error('Error loading polls:', error);
            showAlert('Failed to load active polls', 'danger');
            loadingState.style.display = 'none';
        }
    }

    // Display polls in grid
    function displayPolls(polls, append = false) {
        const pollsGrid = document.getElementById('pollsGrid');

        polls.forEach(poll => {
            const pollCard = createPollCard(poll);
            pollsGrid.appendChild(pollCard);
        });

        console.log(`${append ? 'Appended' : 'Loaded'} ${polls.length} polls`);
    }

        // Create individual poll card
    function createPollCard(poll) {
        const pollCard = document.createElement('div');
        pollCard.className = 'poll-card';
        pollCard.setAttribute('data-poll-id', poll.id);

        // Use vote summary data (already calculated on backend)
        const totalVotes = poll.total_votes || 0;

        pollCard.innerHTML = `
            <div class="poll-header">
                <h3 class="poll-title">${poll.title}</h3>
                <div class="poll-meta">
                    <span class="poll-status active">Active</span>
                    <span class="poll-total-votes">${totalVotes} votes</span>
                </div>
            </div>
            <div class="poll-description">${poll.description || 'No description'}</div>
            <div class="poll-options">
                ${poll.options ? poll.options.map(option => {
                    const voteCount = option.votes_count || 0;
                    const percentage = option.percentage || 0;

                    return `
                        <div class="option-item" data-poll-id="${poll.id}" data-option-id="${option.id}">
                            <div class="option-header">
                                <span class="option-text">${option.option_text}</span>
                                <div class="option-stats">
                                    <span class="option-vote-count">${voteCount}</span>
                                    <span class="option-percentage">${percentage}%</span>
                                </div>
                            </div>
                            <div class="option-progress-bar">
                                <div class="option-progress" style="width: ${percentage}%"></div>
                            </div>
                        </div>
                    `;
                }).join('') : '<p>No options available</p>'}
            </div>
        `;

        return pollCard;
    }

    // Update stats
    function updateStats() {
        // This would be updated with real-time data
        // For now, we'll just update the display
    }

    // Load more polls
    function loadMorePolls() {
        if (hasMorePolls) {
            const loadMoreBtn = document.getElementById('loadMoreBtn');
            const originalText = loadMoreBtn.innerHTML;

            // Show loading state
            loadMoreBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
            loadMoreBtn.disabled = true;

            loadActivePolls(false).then(() => {
                // Restore button state
                loadMoreBtn.innerHTML = originalText;
                loadMoreBtn.disabled = false;
            }).catch(() => {
                // Restore button state on error
                loadMoreBtn.innerHTML = originalText;
                loadMoreBtn.disabled = false;
            });
        }
    }

    // Refresh polls
    function refreshPolls() {
        loadActivePolls(true); // true = reset pagination
    }

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
        // Check authentication
        if (!checkAuth()) {
            return;
        }

        // Load initial data
        loadActivePolls();

        // Initialize WebSocket connection
        initializeWebSocket();



        // Add event listener for page unload
        window.addEventListener('beforeunload', closeWebSocket);
    });
</script>

<style>
    .live-votes-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--border-color);
    }

    .header-content h1 {
        margin: 0 0 0.5rem 0;
        color: var(--text-primary);
    }

    .page-subtitle {
        color: var(--text-secondary);
        margin: 0;
    }

    .connection-status {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: var(--background-color);
        border-radius: var(--radius);
        border: 1px solid var(--border-color);
    }

    .status-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: var(--text-muted);
    }

    .status-indicator.connected {
        background: var(--success-color);
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
    }

    .status-indicator.connected.animated {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
        }
    }

    .status-indicator.activity-flash {
        animation: flash 0.3s ease-in-out;
    }

    @keyframes flash {
        0% {
            background: var(--success-color);
            transform: scale(1);
        }
        50% {
            background: #22c55e;
            transform: scale(1.2);
        }
        100% {
            background: var(--success-color);
            transform: scale(1);
        }
    }

    .status-indicator.disconnected {
        background: var(--danger-color);
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2);
    }

    .status-text {
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--text-secondary);
    }



    .polls-section {
        background: var(--surface-color);
        border-radius: var(--radius);
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
    }

    .section-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .section-header h2 {
        margin: 0;
        color: var(--text-primary);
    }



    .polls-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 1.5rem;
        padding: 1.5rem;
    }

    .poll-card {
        background: var(--background-color);
        border-radius: var(--radius);
        padding: 1.5rem;
        border: 1px solid var(--border-color);
        transition: var(--transition);
    }

    .poll-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .poll-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .poll-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
        flex: 1;
        margin-right: 1rem;
    }

    .poll-meta {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 0.25rem;
    }

    .poll-status {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 0.25rem 0.5rem;
        border-radius: 9999px;
    }

    .poll-status.active {
        background: var(--primary-lighter);
        color: var(--primary-dark);
    }

    .poll-total-votes {
        font-size: 0.875rem;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .poll-description {
        color: var(--text-secondary);
        margin-bottom: 1.5rem;
        line-height: 1.5;
    }

    .poll-options {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .option-item {
        background: var(--surface-color);
        border-radius: var(--radius);
        padding: 1rem;
        border: 1px solid var(--border-color);
        transition: var(--transition);
    }

    .option-item.vote-update-highlight {
        animation: highlightPulse 2s ease-in-out;
    }

    @keyframes highlightPulse {
        0%, 100% { background: var(--surface-color); }
        50% { background: var(--primary-lighter); }
    }

    .option-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
    }

    .option-text {
        font-weight: 500;
        color: var(--text-primary);
        flex: 1;
    }

    .option-stats {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .option-vote-count {
        font-weight: 600;
        color: var(--primary-color);
        font-size: 0.875rem;
    }

    .option-percentage {
        font-weight: 500;
        color: var(--text-secondary);
        font-size: 0.875rem;
        min-width: 3rem;
        text-align: right;
    }

    .option-progress-bar {
        height: 8px;
        background: var(--border-color);
        border-radius: 4px;
        overflow: hidden;
    }

    .option-progress {
        height: 100%;
        background: linear-gradient(90deg, var(--primary-color) 0%, var(--primary-light) 100%);
        border-radius: 4px;
        transition: width 0.5s ease-in-out;
    }

    .loading-state {
        text-align: center;
        padding: 3rem;
        color: var(--text-secondary);
    }

    .spinner {
        width: 2rem;
        height: 2rem;
        border: 2px solid var(--border-color);
        border-top: 2px solid var(--primary-color);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 1rem;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        color: var(--text-secondary);
    }

    .empty-icon {
        font-size: 3rem;
        color: var(--text-muted);
        margin-bottom: 1rem;
    }

    .load-more-section {
        padding: 2rem;
        text-align: center;
        border-top: 1px solid var(--border-color);
    }

    .load-more-container {
        padding: 2rem;
        text-align: center;
        border-top: 1px solid var(--border-color);
        background: var(--surface-color);
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: var(--radius);
        font-weight: 500;
        transition: var(--transition);
        text-decoration: none;
        border: none;
        cursor: pointer;
    }

    .btn-primary {
        background: var(--primary-color);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
    }

    .btn-large {
        padding: 0.75rem 2rem;
        font-size: 1rem;
        font-weight: 600;
    }

    .text-center {
        text-align: center;
    }

    .text-muted {
        color: var(--text-muted);
        font-size: 0.875rem;
    }

    .vote-notification {
        position: fixed;
        top: 2rem;
        right: 2rem;
        background: var(--success-color);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: var(--radius);
        box-shadow: var(--shadow-lg);
        z-index: 10000;
        animation: slideInRight 0.3s ease-out;
    }

    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    .notification-content {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .notification-content i {
        font-size: 1.125rem;
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }

        .polls-grid {
            grid-template-columns: 1fr;
            padding: 1rem;
        }

        .poll-header {
            flex-direction: column;
            gap: 0.75rem;
            align-items: flex-start;
        }

        .option-header {
            flex-direction: column;
            gap: 0.5rem;
            align-items: flex-start;
        }

        .option-stats {
            width: 100%;
            justify-content: space-between;
        }
    }
</style>
@endsection
