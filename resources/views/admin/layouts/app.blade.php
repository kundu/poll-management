<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Poll Management</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    <style>
        :root {
            --primary-color: #10b981;
            --primary-dark: #059669;
            --primary-light: #34d399;
            --primary-lighter: #d1fae5;
            --secondary-color: #6b7280;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --background-color: #f9fafb;
            --surface-color: #ffffff;
            --border-color: #e5e7eb;
            --text-primary: #111827;
            --text-secondary: #6b7280;
            --text-muted: #9ca3af;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --radius: 0.5rem;
            --transition: all 0.2s ease-in-out;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--background-color);
            color: var(--text-primary);
            line-height: 1.6;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 260px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            z-index: 1000;
            transition: var(--transition);
            box-shadow: var(--shadow-lg);
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.25rem;
            font-weight: 700;
        }

        .sidebar-logo i {
            font-size: 1.5rem;
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .nav-item {
            margin: 0.25rem 1rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: var(--radius);
            transition: var(--transition);
            font-weight: 500;
        }

        .nav-link:hover,
        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(4px);
        }

        .nav-link i {
            width: 1.25rem;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
            transition: var(--transition);
        }

        /* Top Navigation */
        .top-nav {
            background: var(--surface-color);
            padding: 1rem 2rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow-sm);
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .user-menu {
            position: relative;
        }

        .user-button {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: var(--primary-lighter);
            color: var(--primary-dark);
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            transition: var(--transition);
            font-weight: 500;
        }

        .user-button:hover {
            background: var(--primary-light);
            color: white;
        }

        .user-avatar {
            width: 2rem;
            height: 2rem;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        /* Content Area */
        .content {
            padding: 2rem;
        }

        /* Cards */
        .card {
            background: var(--surface-color);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .card-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            background: var(--background-color);
        }

        .card-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--radius);
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.875rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-secondary {
            background: var(--secondary-color);
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .btn-danger {
            background: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.75rem;
        }

        /* Tables */
        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .table th {
            background: var(--background-color);
            font-weight: 600;
            color: var(--text-secondary);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .table tbody tr:hover {
            background: var(--background-color);
        }

        /* Status Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .badge-success {
            background: var(--primary-lighter);
            color: var(--primary-dark);
        }

        .badge-warning {
            background: #fef3c7;
            color: var(--warning-color);
        }

        .badge-danger {
            background: #fee2e2;
            color: var(--danger-color);
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn-icon {
            width: 2rem;
            height: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius);
            border: none;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.875rem;
        }

        .btn-icon:hover {
            transform: scale(1.1);
        }

        .btn-icon.edit {
            background: var(--primary-lighter);
            color: var(--primary-dark);
        }

        .btn-icon.view {
            background: #dbeafe;
            color: #2563eb;
        }

        .btn-icon.delete {
            background: #fee2e2;
            color: var(--danger-color);
        }

        .btn-icon.share {
            background: #dbeafe;
            color: #2563eb;
        }

        /* Pagination */
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
            border-radius: var(--radius);
            color: var(--text-secondary);
            text-decoration: none;
            transition: var(--transition);
        }

        .pagination-link:hover,
        .pagination-link.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--surface-color);
            padding: 1.5rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
        }

        .stat-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .stat-icon {
            width: 3rem;
            height: 3rem;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .mobile-menu-toggle {
                display: block;
            }

            .content {
                padding: 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--text-primary);
            font-size: 1.25rem;
            cursor: pointer;
        }

        /* Loading Spinner */
        .loading {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid #f3f3f3;
            border-top: 2px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-primary);
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            font-size: 0.875rem;
            transition: var(--transition);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .form-error {
            color: var(--danger-color);
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }

        /* Alert Messages */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: var(--radius);
            margin-bottom: 1rem;
            border: 1px solid transparent;
        }

        .alert-success {
            background: var(--primary-lighter);
            color: var(--primary-dark);
            border-color: var(--primary-light);
        }

        .alert-danger {
            background: #fee2e2;
            color: var(--danger-color);
            border-color: #fca5a5;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <i class="fas fa-chart-bar"></i>
                <span>Poll Admin</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('admin.polls') }}" class="nav-link {{ request()->routeIs('admin.polls') ? 'active' : '' }}">
                    <i class="fas fa-poll"></i>
                    <span>Polls</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('admin.live-votes') }}" class="nav-link {{ request()->routeIs('admin.live-votes') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Live Votes</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('admin.settings') }}" class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </div>
            <div class="nav-item">
                <button type="button" class="nav-link" onclick="logout()" style="background: none; border: none; width: 100%; text-align: left;">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </div>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navigation -->
        <div class="top-nav">
            <div class="d-flex align-items-center">
                <button class="mobile-menu-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
            </div>

            <div class="user-menu">
                <button class="user-button" onclick="toggleUserMenu()">
                    <div class="user-avatar" id="userAvatar">
                        A
                    </div>
                    <span id="userName">Admin</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content">
            <div id="alertContainer"></div>
            @yield('content')
        </div>
    </div>

    <script>
        // API Configuration
        const API_BASE_URL = '{{ url("/api") }}';

        // Utility functions
        function showAlert(message, type = 'danger') {
            const alertContainer = document.getElementById('alertContainer');
            alertContainer.innerHTML = `
                <div class="alert alert-${type}">
                    ${message}
                </div>
            `;
        }

        function getAuthToken() {
            return localStorage.getItem('auth_token');
        }

        function getUserData() {
            const userData = localStorage.getItem('user_data');
            return userData ? JSON.parse(userData) : null;
        }

        function isAuthenticated() {
            return !!getAuthToken();
        }

        function redirectToLogin() {
            window.location.href = '{{ route("admin.login") }}';
        }

        // Authentication check
        function checkAuth() {
            if (!isAuthenticated()) {
                redirectToLogin();
                return false;
            }
            return true;
        }

        // Update user info in header
        function updateUserInfo() {
            const userData = getUserData();
            if (userData) {
                const userAvatar = document.getElementById('userAvatar');
                const userName = document.getElementById('userName');

                userAvatar.textContent = userData.name ? userData.name.charAt(0).toUpperCase() : 'A';
                userName.textContent = userData.name || 'Admin';
            }
        }

        // Logout function
        async function logout() {
            if (!confirm('Are you sure you want to logout?')) {
                return;
            }

            try {
                const token = getAuthToken();
                if (token) {
                    // Call logout API
                    await fetch(`${API_BASE_URL}/logout`, {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                }
            } catch (error) {
                console.error('Logout error:', error);
            } finally {
                // Clear local storage and redirect
                localStorage.removeItem('auth_token');
                localStorage.removeItem('user_data');
                redirectToLogin();
            }
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('open');
        }

        function toggleUserMenu() {
            // Add user menu dropdown functionality if needed
        }

        // Close sidebar on mobile when clicking outside
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const mobileToggle = document.querySelector('.mobile-menu-toggle');

            if (window.innerWidth <= 768 &&
                !sidebar.contains(event.target) &&
                !mobileToggle.contains(event.target)) {
                sidebar.classList.remove('open');
            }
        });

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Check authentication
            if (!checkAuth()) {
                return;
            }

            // Update user info
            updateUserInfo();
        });
    </script>
</body>
</html>
