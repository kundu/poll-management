<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Poll Details - PollVote</title>
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
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 2rem;
            transition: all 0.2s ease;
        }

        .back-link:hover {
            transform: translateX(-4px);
        }

        .poll-header {
            background: var(--bg-primary);
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-md);
            text-align: center;
        }

        .poll-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1rem;
            line-height: 1.3;
        }

        .poll-description {
            font-size: 1.125rem;
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .poll-meta {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-light);
            font-size: 0.875rem;
        }

        .meta-item i {
            color: var(--primary-color);
        }

        .voting-section {
            background: var(--bg-primary);
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-md);
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .voting-options {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .voting-option {
            border: 2px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }

        .voting-option:hover {
            border-color: var(--primary-color);
            background: var(--primary-light);
        }

        .voting-option.selected {
            border-color: var(--primary-color);
            background: var(--primary-light);
        }

        .voting-option input[type="radio"] {
            margin-right: 1rem;
            transform: scale(1.2);
        }

        .voting-option label {
            font-weight: 500;
            color: var(--text-primary);
            cursor: pointer;
            margin: 0;
        }

        .vote-button {
            width: 100%;
            padding: 1rem;
            font-size: 1.125rem;
            font-weight: 600;
        }

        .results-section {
            background: var(--bg-primary);
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-md);
        }

        .results-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .result-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: var(--bg-secondary);
            border-radius: 0.75rem;
        }

        .result-bar {
            flex: 1;
            height: 2rem;
            background: var(--border-light);
            border-radius: 1rem;
            overflow: hidden;
            position: relative;
        }

        .result-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-dark));
            border-radius: 1rem;
            transition: width 0.8s ease;
        }

        .result-text {
            min-width: 200px;
            font-weight: 500;
        }

        .result-percentage {
            min-width: 80px;
            text-align: right;
            font-weight: 600;
            color: var(--primary-color);
        }

        .total-votes {
            text-align: center;
            padding: 1rem;
            background: var(--bg-tertiary);
            border-radius: 0.75rem;
            margin-top: 1rem;
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

        .error-state {
            text-align: center;
            padding: 3rem;
            color: var(--danger-color);
        }

        .error-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
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

        .voted-message {
            background: var(--success-color);
            color: white;
            padding: 1rem;
            border-radius: 0.75rem;
            text-align: center;
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .header-content {
                padding: 1rem;
            }

            .main-content {
                padding: 1rem;
            }

            .poll-header {
                padding: 1.5rem;
            }

            .poll-title {
                font-size: 1.5rem;
            }

            .voting-section,
            .results-section {
                padding: 1.5rem;
            }

            .poll-meta {
                flex-direction: column;
                gap: 1rem;
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
        <!-- Back Link -->
        <a href="/" class="back-link">
            <i class="fas fa-arrow-left"></i>
            Back to Polls
        </a>

        <!-- Loading State -->
        <div id="loadingState" class="loading-state">
            <div class="loading-spinner"></div>
            <p>Loading poll details...</p>
        </div>

        <!-- Error State -->
        <div id="errorState" class="error-state" style="display: none;">
            <div class="error-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3>Error Loading Poll</h3>
            <p id="errorMessage">Something went wrong while loading the poll.</p>
            <button class="btn btn-primary" onclick="loadPollDetails()">Try Again</button>
        </div>

        <!-- Poll Content -->
        <div id="pollContent" style="display: none;">
            <!-- Poll Header -->
            <div class="poll-header">
                <h1 class="poll-title" id="pollTitle"></h1>
                <p class="poll-description" id="pollDescription"></p>
                <div class="poll-meta">
                    <div class="meta-item">
                        <i class="fas fa-clock"></i>
                        <span id="pollCreatedAgo"></span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-list-ul"></i>
                        <span id="pollOptionsCount"></span> options
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-users"></i>
                        <span id="pollTotalVotes"></span> total votes
                    </div>
                </div>
            </div>

            <!-- Voting Section -->
            <div class="voting-section" id="votingSection">
                <h2 class="section-title">
                    <i class="fas fa-vote-yea"></i>
                    Cast Your Vote
                </h2>

                <div id="votedMessage" class="voted-message" style="display: none;">
                    <i class="fas fa-check-circle"></i>
                    You have already voted on this poll!
                </div>

                <div class="voting-options" id="votingOptions">
                    <!-- Voting options will be loaded here -->
                </div>

                <button class="btn btn-primary vote-button" onclick="submitVote()" id="voteButton">
                    <i class="fas fa-paper-plane"></i>
                    Submit Vote
                </button>
            </div>

            <!-- Results Section -->
            <div class="results-section" id="resultsSection">
                <h2 class="section-title">
                    <i class="fas fa-chart-bar"></i>
                    Current Results
                </h2>

                <div class="results-list" id="resultsList">
                    <!-- Results will be loaded here -->
                </div>

                <div class="total-votes">
                    <strong>Total Votes: <span id="resultsTotalVotes">0</span></strong>
                </div>
            </div>
        </div>
    </main>

    <!-- Alert Container -->
    <div id="alertContainer"></div>

    <!-- Authentication Modal -->
    @include('user.components.auth-modal')

    <script>
        // Global configuration
        const API_BASE_URL = 'http://localhost:8000/api';
        const POLL_ID = {{ $pollId }};

        // Poll data
        let pollData = null;
        let selectedOptionId = null;
        let hasVoted = false;

        // Check authentication on page load
        document.addEventListener('DOMContentLoaded', function() {
            checkAuth();
            loadPollDetails();
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
        async function loadPollDetails() {
            const loadingState = document.getElementById('loadingState');
            const pollContent = document.getElementById('pollContent');
            const errorState = document.getElementById('errorState');

            // Show loading
            loadingState.style.display = 'block';
            pollContent.style.display = 'none';
            errorState.style.display = 'none';

            try {
                const response = await fetch(`${API_BASE_URL}/polls/${POLL_ID}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Failed to fetch poll details');
                }

                const data = await response.json();

                if (data.success) {
                    pollData = data.data.poll;
                    hasVoted = pollData.has_voted;
                    displayPollDetails();
                    loadPollResults();
                } else {
                    throw new Error(data.message || 'Failed to load poll details');
                }

            } catch (error) {
                console.error('Error loading poll details:', error);
                showError('Failed to load poll details. Please try again.');
            }
        }

        function displayPollDetails() {
            const loadingState = document.getElementById('loadingState');
            const pollContent = document.getElementById('pollContent');

            // Hide loading
            loadingState.style.display = 'none';
            pollContent.style.display = 'block';

            // Update poll header
            document.getElementById('pollTitle').textContent = pollData.title;
            document.getElementById('pollDescription').textContent = pollData.description || 'No description available';

            const createdDate = new Date(pollData.created_at);
            document.getElementById('pollCreatedAgo').textContent = getTimeAgo(createdDate);

            const optionsCount = pollData.options ? pollData.options.length : 0;
            document.getElementById('pollOptionsCount').textContent = optionsCount;

            // Build voting options
            buildVotingOptions();

            // Update voting section visibility
            if (hasVoted) {
                document.getElementById('votedMessage').style.display = 'block';
                document.getElementById('voteButton').style.display = 'none';
            }
        }

        function buildVotingOptions() {
            const votingOptions = document.getElementById('votingOptions');

            if (!pollData.options || pollData.options.length === 0) {
                votingOptions.innerHTML = '<p style="text-align: center; color: var(--text-secondary);">No options available</p>';
                return;
            }

            let optionsHTML = '';
            pollData.options.forEach(option => {
                optionsHTML += `
                    <div class="voting-option" onclick="selectOption(${option.id})">
                        <input type="radio" name="pollOption" id="option${option.id}" value="${option.id}">
                        <label for="option${option.id}">${option.option_text}</label>
                    </div>
                `;
            });

            votingOptions.innerHTML = optionsHTML;
        }

        function selectOption(optionId) {
            selectedOptionId = optionId;

            // Update UI
            const options = document.querySelectorAll('.voting-option');
            options.forEach(option => {
                option.classList.remove('selected');
            });

            const selectedOption = document.querySelector(`input[value="${optionId}"]`).closest('.voting-option');
            selectedOption.classList.add('selected');

            // Check the radio button
            document.querySelector(`input[value="${optionId}"]`).checked = true;
        }

        async function submitVote() {
            if (!selectedOptionId) {
                showAlert('Please select an option to vote', 'warning');
                return;
            }

            const voteButton = document.getElementById('voteButton');
            const originalText = voteButton.innerHTML;

            // Show loading state
            voteButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting Vote...';
            voteButton.disabled = true;

            try {
                const headers = {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                };

                // Add authorization header if user is logged in
                const token = localStorage.getItem('auth_token');
                if (token) {
                    headers['Authorization'] = `Bearer ${token}`;
                }

                const response = await fetch(`${API_BASE_URL}/polls/${POLL_ID}/vote`, {
                    method: 'POST',
                    headers: headers,
                    body: JSON.stringify({
                        poll_option_id: selectedOptionId
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    showAlert('Vote submitted successfully!', 'success');
                    hasVoted = true;

                    // Show voted message and hide vote button
                    document.getElementById('votedMessage').style.display = 'block';
                    document.getElementById('voteButton').style.display = 'none';

                    // Refresh results
                    loadPollResults();
                } else {
                    showAlert(data.message || 'Failed to submit vote', 'error');
                }

            } catch (error) {
                console.error('Vote submission error:', error);
                showAlert('Network error. Please try again.', 'error');
            } finally {
                // Restore button state
                voteButton.innerHTML = originalText;
                voteButton.disabled = false;
            }
        }

        async function loadPollResults() {
            try {
                const response = await fetch(`${API_BASE_URL}/polls/${POLL_ID}/results`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Failed to fetch poll results');
                }

                const data = await response.json();

                if (data.success) {
                    displayPollResults(data.data);
                } else {
                    throw new Error(data.message || 'Failed to load poll results');
                }

            } catch (error) {
                console.error('Error loading poll results:', error);
                showAlert('Failed to load poll results', 'error');
            }
        }

        function displayPollResults(data) {
            const resultsList = document.getElementById('resultsList');
            const resultsTotalVotes = document.getElementById('resultsTotalVotes');

            // Update total votes
            resultsTotalVotes.textContent = data.poll.total_votes;
            document.getElementById('pollTotalVotes').textContent = data.poll.total_votes;

            // Build results
            let resultsHTML = '';
            if (data.results && data.results.length > 0) {
                data.results.forEach(result => {
                    const percentage = result.percentage || 0;
                    resultsHTML += `
                        <div class="result-item">
                            <div class="result-text">${result.option_text}</div>
                            <div class="result-bar">
                                <div class="result-fill" style="width: ${percentage}%"></div>
                            </div>
                            <div class="result-percentage">${percentage.toFixed(1)}%</div>
                        </div>
                    `;
                });
            } else {
                resultsHTML = '<p style="text-align: center; color: var(--text-secondary);">No results available</p>';
            }

            resultsList.innerHTML = resultsHTML;
        }

        function showError(message) {
            const loadingState = document.getElementById('loadingState');
            const errorState = document.getElementById('errorState');
            const errorMessage = document.getElementById('errorMessage');

            loadingState.style.display = 'none';
            errorState.style.display = 'block';
            errorMessage.textContent = message;
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


    </script>
</body>
</html>
