@extends('admin.layouts.app')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        <!-- Profile Settings -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Profile Settings</h3>
            </div>
            <div class="card-body">
                <form>
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-input" value="{{ Auth::user()->name ?? 'Admin User' }}" placeholder="Enter your full name">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-input" value="{{ Auth::user()->email ?? 'admin@example.com' }}" placeholder="Enter your email">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Current Password</label>
                        <input type="password" class="form-input" placeholder="Enter current password">
                    </div>

                    <div class="form-group">
                        <label class="form-label">New Password</label>
                        <input type="password" class="form-input" placeholder="Enter new password">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" class="form-input" placeholder="Confirm new password">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Update Profile
                    </button>
                </form>
            </div>
        </div>

        <!-- System Settings -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">System Settings</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Default Poll Status</label>
                    <select class="form-input">
                        <option value="0">Inactive</option>
                        <option value="1" selected>Active</option>
                    </select>
                    <small style="color: var(--text-secondary); font-size: 0.75rem; margin-top: 0.25rem;">
                        New polls will be created with this status by default
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label">Default Guest Voting</label>
                    <select class="form-input">
                        <option value="0">Disabled</option>
                        <option value="1" selected>Enabled</option>
                    </select>
                    <small style="color: var(--text-secondary); font-size: 0.75rem; margin-top: 0.25rem;">
                        Allow guest voting by default for new polls
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label">Polls Per Page</label>
                    <select class="form-input">
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <small style="color: var(--text-secondary); font-size: 0.75rem; margin-top: 0.25rem;">
                        Number of polls displayed per page in the admin panel
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label">Auto-delete Inactive Polls</label>
                    <select class="form-input">
                        <option value="0" selected>Never</option>
                        <option value="30">After 30 days</option>
                        <option value="60">After 60 days</option>
                        <option value="90">After 90 days</option>
                    </select>
                    <small style="color: var(--text-secondary); font-size: 0.75rem; margin-top: 0.25rem;">
                        Automatically delete inactive polls after specified days
                    </small>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Save Settings
                </button>
            </div>
        </div>
    </div>

    <!-- API Information -->
    <div class="card" style="margin-top: 2rem;">
        <div class="card-header">
            <h3 class="card-title">API Information</h3>
        </div>
        <div class="card-body">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                <div>
                    <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem; color: var(--text-primary);">
                        API Base URL
                    </h4>
                    <div style="background: var(--background-color); padding: 1rem; border-radius: var(--radius); font-family: monospace; font-size: 0.875rem;">
                        {{ url('/api') }}
                    </div>
                </div>

                <div>
                    <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem; color: var(--text-primary);">
                        Authentication
                    </h4>
                    <div style="background: var(--background-color); padding: 1rem; border-radius: var(--radius); font-size: 0.875rem;">
                        <div style="margin-bottom: 0.5rem;">
                            <strong>Method:</strong> Bearer Token
                        </div>
                        <div style="margin-bottom: 0.5rem;">
                            <strong>Header:</strong> Authorization: Bearer {token}
                        </div>
                        <div>
                            <strong>Token Type:</strong> Laravel Sanctum
                        </div>
                    </div>
                </div>
            </div>

            <div style="margin-top: 1.5rem;">
                <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem; color: var(--text-primary);">
                    Available Endpoints
                </h4>
                <div style="overflow-x: auto;">
                    <table class="table" style="font-size: 0.875rem;">
                        <thead>
                            <tr>
                                <th>Method</th>
                                <th>Endpoint</th>
                                <th>Description</th>
                                <th>Auth Required</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span style="background: #10b981; color: white; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem;">POST</span></td>
                                <td><code>/api/admin/polls</code></td>
                                <td>Create new poll</td>
                                <td><span class="badge badge-success">Yes</span></td>
                            </tr>
                            <tr>
                                <td><span style="background: #3b82f6; color: white; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem;">GET</span></td>
                                <td><code>/api/admin/polls</code></td>
                                <td>List all polls</td>
                                <td><span class="badge badge-success">Yes</span></td>
                            </tr>
                            <tr>
                                <td><span style="background: #3b82f6; color: white; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem;">GET</span></td>
                                <td><code>/api/admin/polls/{id}</code></td>
                                <td>Get poll details</td>
                                <td><span class="badge badge-success">Yes</span></td>
                            </tr>
                            <tr>
                                <td><span style="background: #f59e0b; color: white; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem;">PATCH</span></td>
                                <td><code>/api/admin/polls/{id}/status</code></td>
                                <td>Update poll status</td>
                                <td><span class="badge badge-success">Yes</span></td>
                            </tr>
                            <tr>
                                <td><span style="background: #3b82f6; color: white; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem;">GET</span></td>
                                <td><code>/api/polls</code></td>
                                <td>List active polls (public)</td>
                                <td><span class="badge badge-warning">No</span></td>
                            </tr>
                            <tr>
                                <td><span style="background: #10b981; color: white; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem;">POST</span></td>
                                <td><code>/api/polls/{id}/vote</code></td>
                                <td>Submit vote</td>
                                <td><span class="badge badge-warning">Optional</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add form submission handlers
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<span class="loading"></span> Saving...';
                submitBtn.disabled = true;

                // Simulate API call
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                    alert('Settings saved successfully!');
                }, 1000);
            });
        });
    </script>
@endsection
