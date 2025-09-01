<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PollVote - Public Polls</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #10b981;
            --primary-dark: #059669;
            --primary-light: #d1fae5;
            --secondary-color: #6b7280;
            --accent-color: #f59e0b;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #3b82f6;

            --text-primary: #111827;
            --text-secondary: #6b7280;
            --text-light: #9ca3af;

            --bg-primary: #ffffff;
            --bg-secondary: #f9fafb;
            --bg-tertiary: #f3f4f6;

            --border-color: #e5e7eb;
            --border-light: #f3f4f6;

            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-secondary);
            color: var(--text-primary);
            line-height: 1.6;
        }

        .header {
            background: var(--bg-primary);
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            text-decoration: none;
        }

        .auth-buttons {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .btn {
            padding: 0.5rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.875rem;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-outline {
            background: transparent;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .btn-outline:hover {
            background: var(--primary-color);
            color: white;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-name {
            font-weight: 500;
            color: var(--text-primary);
        }

        .btn-logout {
            background: var(--danger-color);
            color: white;
            padding: 0.5rem 1rem;
        }

        .btn-logout:hover {
            background: #dc2626;
        }

        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1rem;
        }

        .page-subtitle {
            font-size: 1.125rem;
            color: var(--text-secondary);
            max-width: 600px;
            margin: 0 auto;
        }

        .polls-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .poll-card {
            background: var(--bg-primary);
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: var(--shadow-md);
            transition: all 0.3s ease;
            cursor: pointer;
            border: 1px solid var(--border-light);
        }

        .poll-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
            border-color: var(--primary-color);
        }

        .poll-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.75rem;
            line-height: 1.4;
        }

        .poll-description {
            color: var(--text-secondary);
            margin-bottom: 1rem;
            line-height: 1.6;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .poll-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.875rem;
            color: var(--text-light);
        }

        .poll-options-count {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .poll-created-ago {
            font-style: italic;
        }

        .loading-state {
            text-align: center;
            padding: 3rem;
            color: var(--text-secondary);
        }

        .loading-spinner {
            border: 3px solid var(--border-light);
            border-top: 3px solid var(--primary-color);
            border-radius: 50%;
            width: 40px;
            height: 40px;
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
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }

        .pagination-link {
            padding: 0.5rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            text-decoration: none;
            color: var(--text-secondary);
            transition: all 0.2s ease;
        }

        .pagination-link:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .pagination-link.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .pagination-link.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .alert {
            position: fixed;
            top: 1rem;
            right: 1rem;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            color: white;
            font-weight: 500;
            z-index: 1000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            max-width: 400px;
        }

        .alert.show {
            transform: translateX(0);
        }

        .alert-success {
            background: var(--success-color);
        }

        .alert-error {
            background: var(--danger-color);
        }

        .alert-info {
            background: var(--info-color);
        }

        .alert-warning {
            background: var(--warning-color);
        }

        @media (max-width: 768px) {
            .header-content {
                padding: 1rem;
            }

            .main-content {
                padding: 1rem;
            }

            .page-title {
                font-size: 2rem;
            }

            .polls-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .poll-card {
                padding: 1rem;
            }

            .auth-buttons {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <a href="/" class="logo">
                <i class="fas fa-poll"></i> PollVote
            </a>

            <div class="auth-buttons" id="authButtons">
                <button class="btn btn-outline" onclick="openAuthModal('login')">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
                <button class="btn btn-primary" onclick="openAuthModal('register')">
                    <i class="fas fa-user-plus"></i> Sign Up
                </button>
            </div>

            <div class="user-info" id="userInfo" style="display: none;">
                <span class="user-name" id="userName"></span>
                <button class="btn btn-logout" onclick="logout()">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="page-header">
            <h1 class="page-title">Discover and Vote on Polls</h1>
            <p class="page-subtitle">Participate in community polls and share your opinion on various topics. Create an account to track your voting history and participate in exclusive polls.</p>
        </div>

        <!-- Loading State -->
        <div id="loadingState" class="loading-state">
            <div class="loading-spinner"></div>
            <p>Loading polls...</p>
        </div>

        <!-- Polls Grid -->
        <div id="pollsGrid" class="polls-grid" style="display: none;">
            <!-- Polls will be loaded here via AJAX -->
        </div>

        <!-- Empty State -->
        <div id="emptyState" class="empty-state" style="display: none;">
            <div class="empty-icon">
                <i class="fas fa-inbox"></i>
            </div>
            <h3>No polls found</h3>
            <p>There are currently no active polls available.</p>
        </div>

        <!-- Pagination -->
        <div id="paginationContainer" class="pagination" style="display: none;">
            <!-- Pagination will be loaded here via AJAX -->
        </div>
    </main>

    <!-- Alert Container -->
    <div id="alertContainer"></div>

    <!-- Authentication Modal -->
    @include('user.components.auth-modal')

    <script>
        // Global configuration
        const API_BASE_URL = 'http://localhost:8000/api';

        // Current page state
        let currentPage = 1;
        let currentFilters = {
            per_page: 12
        };

        // Check authentication on page load
        document.addEventListener('DOMContentLoaded', function() {
            checkAuth();
            loadPolls();
        });

        // Authentication functions
        function checkAuth() {
            const token = localStorage.getItem('auth_token');
            const userData = localStorage.getItem('user_data');

            if (token && userData) {
                const user = JSON.parse(userData);
                showUserInfo(user);
            } else {
                showAuthButtons();
            }
        }

        // Make functions globally accessible for auth modal
        window.showUserInfo = function(user) {
            document.getElementById('authButtons').style.display = 'none';
            document.getElementById('userInfo').style.display = 'flex';
            document.getElementById('userName').textContent = user.name;
        }

        window.showAuthButtons = function() {
            document.getElementById('authButtons').style.display = 'flex';
            document.getElementById('userInfo').style.display = 'none';
        }

        function logout() {
            const token = localStorage.getItem('auth_token');

            if (token) {
                fetch(`${API_BASE_URL}/logout`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }).catch(error => {
                    console.error('Logout error:', error);
                });
            }

            localStorage.removeItem('auth_token');
            localStorage.removeItem('user_data');
            showAuthButtons();
            showAlert('Logged out successfully', 'success');
        }

        // Poll loading functions
        async function loadPolls(page = 1) {
            const loadingState = document.getElementById('loadingState');
            const pollsGrid = document.getElementById('pollsGrid');
            const emptyState = document.getElementById('emptyState');

            // Show loading
            loadingState.style.display = 'block';
            pollsGrid.style.display = 'none';
            emptyState.style.display = 'none';

            try {
                // Build query parameters
                const params = new URLSearchParams({
                    page: page,
                    per_page: currentFilters.per_page
                });

                // Make API call
                const response = await fetch(`${API_BASE_URL}/polls?${params.toString()}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Failed to fetch polls');
                }

                const data = await response.json();

                if (data.success) {
                    displayPolls(data.data);
                } else {
                    throw new Error(data.message || 'Failed to load polls');
                }

            } catch (error) {
                console.error('Error loading polls:', error);
                showAlert('Failed to load polls. Please try again.', 'error');

                // Show empty state on error
                loadingState.style.display = 'none';
                pollsGrid.style.display = 'none';
                emptyState.style.display = 'block';
            }
        }

        function displayPolls(data) {
            const loadingState = document.getElementById('loadingState');
            const pollsGrid = document.getElementById('pollsGrid');
            const emptyState = document.getElementById('emptyState');

            // Hide loading
            loadingState.style.display = 'none';

            if (!data.polls || data.polls.length === 0) {
                emptyState.style.display = 'block';
                pollsGrid.style.display = 'none';
                return;
            }

            // Show polls grid
            pollsGrid.style.display = 'grid';
            emptyState.style.display = 'none';

            // Build polls HTML
            let pollsHTML = '';
            data.polls.forEach(poll => {
                const createdDate = new Date(poll.created_at);
                const timeAgo = getTimeAgo(createdDate);
                const optionsCount = poll.options ? poll.options.length : 0;

                pollsHTML += `
                    <div class="poll-card" onclick="openPollDetails(${poll.id})">
                        <h3 class="poll-title">${poll.title}</h3>
                        <p class="poll-description">${poll.description || 'No description available'}</p>
                        <div class="poll-meta">
                            <div class="poll-options-count">
                                <i class="fas fa-list-ul"></i>
                                ${optionsCount} option${optionsCount !== 1 ? 's' : ''}
                            </div>
                            <div class="poll-created-ago">
                                <i class="fas fa-clock"></i>
                                ${timeAgo}
                            </div>
                        </div>
                    </div>
                `;
            });

            document.getElementById('pollsGrid').innerHTML = pollsHTML;

            // Update pagination
            updatePagination(data.pagination);
        }

        function updatePagination(pagination) {
            const paginationContainer = document.getElementById('paginationContainer');

            if (!pagination || pagination.last_page <= 1) {
                paginationContainer.style.display = 'none';
                return;
            }

            paginationContainer.style.display = 'flex';

            let paginationHTML = '';

            // Previous button
            if (pagination.current_page > 1) {
                paginationHTML += `<a href="#" class="pagination-link" onclick="loadPolls(${pagination.current_page - 1})">Previous</a>`;
            } else {
                paginationHTML += `<span class="pagination-link disabled">Previous</span>`;
            }

            // Page numbers
            for (let i = 1; i <= pagination.last_page; i++) {
                if (i === pagination.current_page) {
                    paginationHTML += `<span class="pagination-link active">${i}</span>`;
                } else {
                    paginationHTML += `<a href="#" class="pagination-link" onclick="loadPolls(${i})">${i}</a>`;
                }
            }

            // Next button
            if (pagination.current_page < pagination.last_page) {
                paginationHTML += `<a href="#" class="pagination-link" onclick="loadPolls(${pagination.current_page + 1})">Next</a>`;
            } else {
                paginationHTML += `<span class="pagination-link disabled">Next</span>`;
            }

            paginationContainer.innerHTML = paginationHTML;
        }

        function getTimeAgo(date) {
            const now = new Date();
            const diffInSeconds = Math.floor((now - date) / 1000);

            if (diffInSeconds < 60) {
                return 'Just now';
            } else if (diffInSeconds < 3600) {
                const minutes = Math.floor(diffInSeconds / 60);
                return `${minutes} minute${minutes !== 1 ? 's' : ''} ago`;
            } else if (diffInSeconds < 86400) {
                const hours = Math.floor(diffInSeconds / 3600);
                return `${hours} hour${hours !== 1 ? 's' : ''} ago`;
            } else if (diffInSeconds < 2592000) {
                const days = Math.floor(diffInSeconds / 86400);
                return `${days} day${days !== 1 ? 's' : ''} ago`;
            } else if (diffInSeconds < 31536000) {
                const months = Math.floor(diffInSeconds / 2592000);
                return `${months} month${months !== 1 ? 's' : ''} ago`;
            } else {
                const years = Math.floor(diffInSeconds / 31536000);
                return `${years} year${years !== 1 ? 's' : ''} ago`;
            }
        }

        function openPollDetails(pollId) {
            window.location.href = `/polls/${pollId}`;
        }

        // Make showAlert globally accessible for auth modal
        window.showAlert = function(message, type = 'info') {
            const alertContainer = document.getElementById('alertContainer');
            const alert = document.createElement('div');
            alert.className = `alert alert-${type}`;
            alert.textContent = message;

            alertContainer.appendChild(alert);

            // Show alert
            setTimeout(() => alert.classList.add('show'), 100);

            // Hide and remove alert
            setTimeout(() => {
                alert.classList.remove('show');
                setTimeout(() => alert.remove(), 300);
            }, 3000);
        }

        // Make loadPolls globally accessible for auth modal
        window.loadPolls = loadPolls;
    </script>
</body>
</html>
