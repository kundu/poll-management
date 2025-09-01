<!-- Authentication Modal -->
<div id="authModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Login</h2>
            <button class="modal-close" onclick="closeAuthModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Login Form -->
        <div id="loginForm" class="auth-form">
            <form onsubmit="handleLogin(event)">
                <div class="form-group">
                    <label for="loginEmail">Email</label>
                    <input type="email" id="loginEmail" name="email" required>
                    <div class="form-error" id="loginEmailError"></div>
                </div>

                <div class="form-group">
                    <label for="loginPassword">Password</label>
                    <input type="password" id="loginPassword" name="password" required>
                    <div class="form-error" id="loginPasswordError"></div>
                </div>

                <button type="submit" class="btn btn-primary btn-full">
                    <span class="btn-text">Login</span>
                    <span class="btn-loading" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i> Logging in...
                    </span>
                </button>
            </form>

            <div class="auth-switch">
                <p>Don't have an account? <a href="#" onclick="switchToRegister()">Sign up</a></p>
            </div>
        </div>

        <!-- Register Form -->
        <div id="registerForm" class="auth-form" style="display: none;">
            <form onsubmit="handleRegister(event)">
                <div class="form-group">
                    <label for="registerName">Full Name</label>
                    <input type="text" id="registerName" name="name" required>
                    <div class="form-error" id="registerNameError"></div>
                </div>

                <div class="form-group">
                    <label for="registerEmail">Email</label>
                    <input type="email" id="registerEmail" name="email" required>
                    <div class="form-error" id="registerEmailError"></div>
                </div>

                <div class="form-group">
                    <label for="registerPassword">Password</label>
                    <input type="password" id="registerPassword" name="password" required>
                    <div class="form-error" id="registerPasswordError"></div>
                </div>

                <div class="form-group">
                    <label for="registerPasswordConfirm">Confirm Password</label>
                    <input type="password" id="registerPasswordConfirm" name="password_confirmation" required>
                    <div class="form-error" id="registerPasswordConfirmError"></div>
                </div>

                <button type="submit" class="btn btn-primary btn-full">
                    <span class="btn-text">Create Account</span>
                    <span class="btn-loading" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i> Creating account...
                    </span>
                </button>
            </form>

            <div class="auth-switch">
                <p>Already have an account? <a href="#" onclick="switchToLogin()">Login</a></p>
            </div>
        </div>
    </div>
</div>

<style>
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .modal.show {
        opacity: 1;
        visibility: visible;
    }

    .modal-content {
        background: white;
        border-radius: 1rem;
        width: 90%;
        max-width: 400px;
        max-height: 90vh;
        overflow-y: auto;
        transform: scale(0.9);
        transition: transform 0.3s ease;
    }

    .modal.show .modal-content {
        transform: scale(1);
    }

    .modal-header {
        padding: 1.5rem 1.5rem 1rem;
        border-bottom: 1px solid var(--border-light);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h2 {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.25rem;
        color: var(--text-light);
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
    }

    .modal-close:hover {
        background: var(--bg-tertiary);
        color: var(--text-primary);
    }

    .auth-form {
        padding: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        font-weight: 500;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }

    .form-group input {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid var(--border-color);
        border-radius: 0.5rem;
        font-size: 1rem;
        transition: all 0.2s ease;
        background: var(--bg-primary);
    }

    .form-group input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px var(--primary-light);
    }

    .form-group input.error {
        border-color: var(--danger-color);
    }

    .form-error {
        color: var(--danger-color);
        font-size: 0.75rem;
        margin-top: 0.25rem;
        min-height: 1rem;
    }

    .btn-full {
        width: 100%;
        justify-content: center;
        padding: 0.75rem;
        font-size: 1rem;
        margin-bottom: 1rem;
    }

    .btn-loading {
        display: none;
    }

    .btn.loading .btn-text {
        display: none;
    }

    .btn.loading .btn-loading {
        display: inline-flex;
    }

    .auth-switch {
        text-align: center;
        padding-top: 1rem;
        border-top: 1px solid var(--border-light);
    }

    .auth-switch p {
        margin: 0;
        color: var(--text-secondary);
        font-size: 0.875rem;
    }

    .auth-switch a {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 500;
    }

    .auth-switch a:hover {
        text-decoration: underline;
    }

    @media (max-width: 480px) {
        .modal-content {
            width: 95%;
            margin: 1rem;
        }

        .modal-header {
            padding: 1rem 1rem 0.75rem;
        }

        .auth-form {
            padding: 1rem;
        }
    }
</style>

<script>
    // Global configuration
    // const API_BASE_URL = 'http://localhost:8000/api';

    let currentAuthMode = 'login';

    // Make functions globally accessible
    window.openAuthModal = function(type = 'login') {
        currentAuthMode = type;
        const modal = document.getElementById('authModal');
        const modalTitle = document.getElementById('modalTitle');

        // Clear previous errors
        clearFormErrors();

        if (type === 'login') {
            showLoginForm();
            modalTitle.textContent = 'Login';
        } else {
            showRegisterForm();
            modalTitle.textContent = 'Create Account';
        }

        modal.style.display = 'flex';
        setTimeout(() => modal.classList.add('show'), 10);
        document.body.style.overflow = 'hidden';
    }

    window.closeAuthModal = function() {
        const modal = document.getElementById('authModal');
        modal.classList.remove('show');
        setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }, 300);

        // Clear form errors
        clearFormErrors();
    }

    window.switchToRegister = function() {
        currentAuthMode = 'register';
        document.getElementById('modalTitle').textContent = 'Create Account';
        showRegisterForm();
        clearFormErrors();
    }

    window.switchToLogin = function() {
        currentAuthMode = 'login';
        document.getElementById('modalTitle').textContent = 'Login';
        showLoginForm();
        clearFormErrors();
    }

    function showLoginForm() {
        document.getElementById('loginForm').style.display = 'block';
        document.getElementById('registerForm').style.display = 'none';
    }

    function showRegisterForm() {
        document.getElementById('loginForm').style.display = 'none';
        document.getElementById('registerForm').style.display = 'block';
    }

    function clearFormErrors() {
        const errorElements = document.querySelectorAll('.form-error');
        errorElements.forEach(element => {
            element.textContent = '';
        });

        const inputs = document.querySelectorAll('.form-group input');
        inputs.forEach(input => {
            input.classList.remove('error');
        });
    }

    async function handleLogin(event) {
        event.preventDefault();

        const form = event.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        const email = document.getElementById('loginEmail').value;
        const password = document.getElementById('loginPassword').value;

        // Clear previous errors
        clearFormErrors();

        // Show loading state
        submitBtn.classList.add('loading');

        try {
            const response = await fetch(`${API_BASE_URL}/login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ email, password })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Store authentication data
                localStorage.setItem('auth_token', data.data.token);
                localStorage.setItem('user_data', JSON.stringify(data.data.user));

                // Update UI
                if (typeof window.showUserInfo === 'function') {
                    window.showUserInfo(data.data.user);
                }
                closeAuthModal();
                if (typeof window.showAlert === 'function') {
                    window.showAlert('Login successful!', 'success');
                }

                // Refresh polls to show any user-specific data
                if (typeof window.loadPolls === 'function') {
                    window.loadPolls();
                }
            } else {
                // Handle validation errors
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const errorElement = document.getElementById('login' + field.charAt(0).toUpperCase() + field.slice(1) + 'Error');
                        if (errorElement) {
                            errorElement.textContent = data.errors[field][0];
                        }

                        const inputElement = document.getElementById('login' + field.charAt(0).toUpperCase() + field.slice(1));
                        if (inputElement) {
                            inputElement.classList.add('error');
                        }
                    });
                } else {
                    if (typeof window.showAlert === 'function') {
                        window.showAlert(data.message || 'Login failed', 'error');
                    }
                }
            }
        } catch (error) {
            console.error('Login error:', error);
            if (typeof window.showAlert === 'function') {
                window.showAlert('Network error. Please try again.', 'error');
            }
        } finally {
            submitBtn.classList.remove('loading');
        }
    }

    async function handleRegister(event) {
        event.preventDefault();

        const form = event.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        const name = document.getElementById('registerName').value;
        const email = document.getElementById('registerEmail').value;
        const password = document.getElementById('registerPassword').value;
        const passwordConfirmation = document.getElementById('registerPasswordConfirm').value;

        // Clear previous errors
        clearFormErrors();

        // Show loading state
        submitBtn.classList.add('loading');

        try {
            const response = await fetch(`${API_BASE_URL}/register`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    name,
                    email,
                    password,
                    password_confirmation: passwordConfirmation
                })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Store authentication data
                localStorage.setItem('auth_token', data.data.token);
                localStorage.setItem('user_data', JSON.stringify(data.data.user));

                // Update UI
                if (typeof window.showUserInfo === 'function') {
                    window.showUserInfo(data.data.user);
                }
                closeAuthModal();
                if (typeof window.showAlert === 'function') {
                    window.showAlert('Account created successfully!', 'success');
                }

                // Refresh polls to show any user-specific data
                if (typeof window.loadPolls === 'function') {
                    window.loadPolls();
                }
            } else {
                // Handle validation errors
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const errorElement = document.getElementById('register' + field.charAt(0).toUpperCase() + field.slice(1) + 'Error');
                        if (errorElement) {
                            errorElement.textContent = data.errors[field][0];
                        }

                        const inputElement = document.getElementById('register' + field.charAt(0).toUpperCase() + field.slice(1));
                        if (inputElement) {
                            inputElement.classList.add('error');
                        }
                    });
                } else {
                    if (typeof window.showAlert === 'function') {
                        window.showAlert(data.message || 'Registration failed', 'error');
                    }
                }
            }
        } catch (error) {
            console.error('Registration error:', error);
            if (typeof window.showAlert === 'function') {
                window.showAlert('Network error. Please try again.', 'error');
            }
        } finally {
            submitBtn.classList.remove('loading');
        }
    }

    // Close modal when clicking outside
    document.getElementById('authModal').addEventListener('click', function(event) {
        if (event.target === this) {
            closeAuthModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeAuthModal();
        }
    });
</script>
