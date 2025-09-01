<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Login - Poll Management</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #10b981;
            --primary-dark: #059669;
            --primary-light: #34d399;
            --primary-lighter: #d1fae5;
            --secondary-color: #6b7280;
            --background-color: #f9fafb;
            --surface-color: #ffffff;
            --border-color: #e5e7eb;
            --text-primary: #111827;
            --text-secondary: #6b7280;
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
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
        }

        .login-card {
            background: var(--surface-color);
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            text-align: center;
            padding: 2rem 2rem 1rem;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
        }

        .login-logo {
            width: 4rem;
            height: 4rem;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
        }

        .login-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .login-subtitle {
            font-size: 0.875rem;
            opacity: 0.9;
        }

        .login-body {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-primary);
            font-size: 0.875rem;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            font-size: 0.875rem;
            transition: var(--transition);
            background: var(--surface-color);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .form-input.error {
            border-color: #ef4444;
        }

        .form-error {
            color: #ef4444;
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }

        .btn {
            width: 100%;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--radius);
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-primary:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .loading {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: var(--radius);
            margin-bottom: 1rem;
            border: 1px solid transparent;
            font-size: 0.875rem;
        }

        .alert-danger {
            background: #fee2e2;
            color: #ef4444;
            border-color: #fca5a5;
        }

        .alert-success {
            background: var(--primary-lighter);
            color: var(--primary-dark);
            border-color: var(--primary-light);
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .input-with-icon {
            padding-left: 2.5rem;
        }

        .footer-text {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text-secondary);
            font-size: 0.75rem;
        }

        .footer-text a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .footer-text a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h1 class="login-title">Poll Management</h1>
                <p class="login-subtitle">Admin Dashboard</p>
            </div>

            <div class="login-body">
                <div id="alertContainer"></div>

                <form id="loginForm">
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <i class="fas fa-envelope input-icon"></i>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-input input-with-icon"
                                placeholder="Enter your email"
                                required
                                autocomplete="email"
                                autofocus
                            >
                        </div>
                        <div class="form-error" id="emailError"></div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <i class="fas fa-lock input-icon"></i>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-input input-with-icon"
                                placeholder="Enter your password"
                                required
                                autocomplete="current-password"
                            >
                        </div>
                        <div class="form-error" id="passwordError"></div>
                    </div>

                    <button type="submit" class="btn btn-primary" id="loginBtn">
                        <span id="loginText">Sign In</span>
                        <span id="loginSpinner" class="loading" style="display: none;"></span>
                    </button>
                </form>

                <div class="footer-text">
                    <p>Default Admin Credentials:</p>
                    <p><strong>Email:</strong> admin@example.com</p>
                    <p><strong>Password:</strong> admin123</p>
                </div>
            </div>
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

        function clearErrors() {
            document.getElementById('emailError').textContent = '';
            document.getElementById('passwordError').textContent = '';
            document.getElementById('alertContainer').innerHTML = '';
        }

        function setFieldError(fieldName, message) {
            const errorElement = document.getElementById(fieldName + 'Error');
            if (errorElement) {
                errorElement.textContent = message;
            }
        }

        function clearFieldError(fieldName) {
            const errorElement = document.getElementById(fieldName + 'Error');
            if (errorElement) {
                errorElement.textContent = '';
            }
        }

        // Login form submission
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            // Clear previous errors
            clearErrors();

            // Get form data
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            // Show loading state
            const loginBtn = document.getElementById('loginBtn');
            const loginText = document.getElementById('loginText');
            const loginSpinner = document.getElementById('loginSpinner');

            loginBtn.disabled = true;
            loginText.style.display = 'none';
            loginSpinner.style.display = 'inline-block';

            try {
                // Make API call to login
                const response = await fetch(`${API_BASE_URL}/login`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        email: email,
                        password: password
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // Store token and user data
                    localStorage.setItem('auth_token', data.data.token);
                    localStorage.setItem('user_data', JSON.stringify(data.data.user));

                    // Show success message
                    showAlert('Login successful! Redirecting...', 'success');

                    // Redirect to dashboard
                    setTimeout(() => {
                        window.location.href = '{{ route("admin.dashboard") }}';
                    }, 1000);

                } else {
                    // Handle login errors
                    if (data.errors) {
                        // Field-specific errors
                        if (data.errors.email) {
                            setFieldError('email', data.errors.email[0]);
                        }
                        if (data.errors.password) {
                            setFieldError('password', data.errors.password[0]);
                        }
                    } else if (data.message) {
                        // General error message
                        showAlert(data.message);
                    } else {
                        showAlert('Login failed. Please try again.');
                    }
                }

            } catch (error) {
                console.error('Login error:', error);
                showAlert('Network error. Please check your connection and try again.');
            } finally {
                // Reset loading state
                loginBtn.disabled = false;
                loginText.style.display = 'inline';
                loginSpinner.style.display = 'none';
            }
        });

        // Clear field errors on input
        document.getElementById('email').addEventListener('input', function() {
            clearFieldError('email');
        });

        document.getElementById('password').addEventListener('input', function() {
            clearFieldError('password');
        });

        // Check if user is already logged in
        window.addEventListener('load', function() {
            const token = localStorage.getItem('auth_token');
            if (token) {
                // User is already logged in, redirect to dashboard
                window.location.href = '{{ route("admin.dashboard") }}';
            }
        });
    </script>
</body>
</html>
