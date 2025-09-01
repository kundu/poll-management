@extends('admin.layouts.app')

@section('title', 'Poll Management')
@section('page-title', 'Poll Management')

@section('content')
    <!-- Page Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">
                All Polls
            </h2>
            <p style="color: var(--text-secondary); margin: 0;">
                Manage and monitor all polls in the system
            </p>
        </div>
        <button class="btn btn-primary" onclick="openCreatePollModal()">
            <i class="fas fa-plus"></i>
            Create Poll
        </button>
    </div>

    <!-- Filters and Search -->
    <div class="card" style="margin-bottom: 2rem;">
        <div class="card-body">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-input" placeholder="Search polls..." id="searchInput">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Status</label>
                    <select class="form-input" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Sort By</label>
                    <select class="form-input" id="sortBy">
                        <option value="created_at">Created Date</option>
                        <option value="title">Title</option>
                        <option value="status">Status</option>
                    </select>
                </div>
                <button class="btn btn-secondary" onclick="applyFilters()">
                    <i class="fas fa-filter"></i>
                    Apply Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Loading State -->
    <div id="loadingState" class="card" style="display: none;">
        <div class="card-body" style="text-align: center; padding: 3rem;">
            <div class="loading" style="width: 2rem; height: 2rem; margin: 0 auto 1rem;"></div>
            <p style="color: var(--text-secondary);">Loading polls...</p>
        </div>
    </div>

    <!-- Polls Table -->
    <div class="card" id="pollsTable" style="display: none;">
        <div class="card-header">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h3 class="card-title">Polls List</h3>
                <div style="color: var(--text-secondary); font-size: 0.875rem;" id="pollsCount">
                    Loading...
                </div>
            </div>
        </div>
        <div class="card-body">
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                                                    <th>Title</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Guest Voting</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="pollsTableBody">
                        <!-- Polls will be loaded here via AJAX -->
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div id="paginationContainer" class="pagination">
                <!-- Pagination will be loaded here via AJAX -->
            </div>
        </div>
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="card" style="display: none;">
        <div class="card-body" style="text-align: center; padding: 3rem;">
            <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; display: block; opacity: 0.5; color: var(--text-secondary);"></i>
            <div style="font-size: 1.125rem; margin-bottom: 0.5rem; color: var(--text-secondary);">No polls found</div>
            <div style="font-size: 0.875rem; color: var(--text-secondary);">Create your first poll to get started</div>
        </div>
    </div>

    @include('admin.components.create-poll-modal')
    @include('admin.components.poll-details-modal')

    <!-- Share Poll Modal -->
    <div id="sharePollModal" class="modal" style="display: none;">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="fas fa-share-alt" style="color: #2563eb; margin-right: 0.5rem;"></i>
                    Share Poll
                </h3>
                <button class="modal-close" onclick="closeShareModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div style="margin-bottom: 1.5rem;">
                    <label class="form-label">Public Poll Link</label>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <input type="text" id="pollUrlInput" class="form-input" readonly style="flex: 1; background-color: #f8fafc;">
                        <button class="btn btn-primary" onclick="copyPollUrl()" style="white-space: nowrap;">
                            <i class="fas fa-copy"></i>
                            Copy
                        </button>
                    </div>
                </div>
                <div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1rem;">
                    <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                        <i class="fas fa-info-circle" style="color: #0284c7; margin-right: 0.5rem;"></i>
                        <strong style="color: #0c4a6e;">Share this link with users</strong>
                    </div>
                    <p style="margin: 0; color: #0c4a6e; font-size: 0.875rem;">
                        Users can access this poll and vote using the link above. Make sure the poll is active before sharing.
                    </p>
                </div>
            </div> 
        </div>
    </div>

    <script>
        // Current page state
        let currentPage = 1;
        let currentFilters = {
            search: '',
            status: '',
            sort: 'created_at',
            order: 'desc',
            per_page: 10
        };

        // Load polls from API
        async function loadPolls(page = 1) {
            const loadingState = document.getElementById('loadingState');
            const pollsTable = document.getElementById('pollsTable');
            const emptyState = document.getElementById('emptyState');

            // Show loading
            loadingState.style.display = 'block';
            pollsTable.style.display = 'none';
            emptyState.style.display = 'none';

            try {
                // Build query parameters
                const params = new URLSearchParams({
                    page: page,
                    per_page: currentFilters.per_page
                });

                if (currentFilters.search) params.append('search', currentFilters.search);
                if (currentFilters.status !== '') params.append('status', currentFilters.status);
                if (currentFilters.sort) params.append('sort', currentFilters.sort);
                if (currentFilters.order) params.append('order', currentFilters.order);

                // Make API call
                const response = await fetch(`${API_BASE_URL}/admin/polls?${params.toString()}`, {
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

                if (data.success) {
                    displayPolls(data.data);
                } else {
                    throw new Error(data.message || 'Failed to load polls');
                }

            } catch (error) {
                console.error('Error loading polls:', error);
                showAlert('Failed to load polls. Please try again.');

                // Show empty state on error
                loadingState.style.display = 'none';
                pollsTable.style.display = 'none';
                emptyState.style.display = 'block';
            } finally {
                loadingState.style.display = 'none';
            }
        }

        // Display polls in table
        function displayPolls(data) {
            const pollsTable = document.getElementById('pollsTable');
            const emptyState = document.getElementById('emptyState');
            const pollsTableBody = document.getElementById('pollsTableBody');
            const pollsCount = document.getElementById('pollsCount');

            if (!data.polls || data.polls.length === 0) {
                pollsTable.style.display = 'none';
                emptyState.style.display = 'block';
                return;
            }

            pollsTable.style.display = 'block';
            emptyState.style.display = 'none';

            // Update count
            const total = data.pagination?.total || data.polls.length;
            const from = data.pagination?.from || 1;
            const to = data.pagination?.to || data.polls.length;
            pollsCount.textContent = `Showing ${from} to ${to} of ${total} polls`;

            // Build table rows
            let tableHTML = '';
            data.polls.forEach(poll => {
                const statusBadge = poll.status == 1
                    ? '<span class="badge badge-success"><i class="fas fa-check"></i> Active</span>'
                    : '<span class="badge badge-warning"><i class="fas fa-pause"></i> Inactive</span>';

                const guestVotingBadge = poll.allow_guest_voting
                    ? '<span class="badge badge-success"><i class="fas fa-users"></i> Allowed</span>'
                    : '<span class="badge badge-warning"><i class="fas fa-user-lock"></i> Restricted</span>';

                const createdDate = new Date(poll.created_at).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });

                const createdTime = new Date(poll.created_at).toLocaleTimeString('en-US', {
                    hour: 'numeric',
                    minute: '2-digit'
                });

                tableHTML += `
                    <tr>
                        <td>
                            <div style="font-weight: 500; color: var(--text-primary);">
                                ${poll.title}
                            </div>
                        </td>
                        <td>
                            <div style="color: var(--text-secondary); font-size: 0.875rem;">
                                ${poll.description ? poll.description.substring(0, 60) + (poll.description.length > 60 ? '...' : '') : 'No description'}
                            </div>
                        </td>
                        <td>${statusBadge}</td>
                        <td>${guestVotingBadge}</td>
                        <td>
                            <div style="font-size: 0.875rem;">
                                ${createdDate}
                            </div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">
                                ${createdTime}
                            </div>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-icon view" title="View Poll" onclick="viewPoll(${poll.id})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn-icon share" title="Share Poll" onclick="sharePoll(${poll.id})">
                                    <i class="fas fa-share-alt"></i>
                                </button>
                                <button class="btn-icon" title="Toggle Status"
                                        style="background: ${poll.status == 1 ? '#fee2e2' : '#d1fae5'}; color: ${poll.status == 1 ? '#ef4444' : '#059669'};"
                                        onclick="toggleStatus(${poll.id}, ${poll.status})">
                                    <i class="fas fa-${poll.status == 1 ? 'pause' : 'play'}"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });

            pollsTableBody.innerHTML = tableHTML;

            // Update pagination
            updatePagination(data.pagination);
        }

        // Update pagination controls
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
                paginationHTML += `<span class="pagination-link" style="opacity: 0.5; cursor: not-allowed;">Previous</span>`;
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
                paginationHTML += `<span class="pagination-link" style="opacity: 0.5; cursor: not-allowed;">Next</span>`;
            }

            paginationContainer.innerHTML = paginationHTML;
        }

        // Apply filters
        function applyFilters() {
            currentFilters.search = document.getElementById('searchInput').value;
            currentFilters.status = document.getElementById('statusFilter').value;
            currentFilters.sort = document.getElementById('sortBy').value;
            currentPage = 1;
            loadPolls(currentPage);
        }

        // Action functions

        function viewPoll(pollId) {
            openPollDetailsModal(pollId);
        }

        function sharePoll(pollId) {
            const pollUrl = `${window.location.origin}/polls/${pollId}`;
            openShareModal(pollUrl, pollId);
        }

        async function toggleStatus(pollId, currentStatus) {
            const newStatus = currentStatus == 1 ? 0 : 1;
            const statusText = newStatus == 1 ? 'activate' : 'deactivate';

            if (confirm(`Are you sure you want to ${statusText} this poll?`)) {
                try {
                    const response = await fetch(`${API_BASE_URL}/admin/polls/${pollId}/status`, {
                        method: 'PATCH',
                        headers: {
                            'Authorization': `Bearer ${getAuthToken()}`,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ status: newStatus })
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        showAlert(`Poll ${statusText}d successfully!`, 'success');
                        // Refresh the polls list
                        loadPolls(currentPage);
                    } else {
                        showAlert(data.message || `Failed to ${statusText} poll`, 'error');
                    }
                } catch (error) {
                    console.error('Error toggling poll status:', error);
                    showAlert('Network error. Please try again.', 'error');
                }
            }
        }

        // Share Poll Modal Functions
        function openShareModal(pollUrl, pollId) {
            const modal = document.getElementById('sharePollModal');
            const urlInput = document.getElementById('pollUrlInput');

            urlInput.value = pollUrl;
            modal.style.display = 'flex';

            // Focus on the URL input for easy selection
            setTimeout(() => {
                urlInput.focus();
                urlInput.select();
            }, 100);
        }

        function closeShareModal() {
            const modal = document.getElementById('sharePollModal');
            modal.style.display = 'none';
        }

        async function copyPollUrl() {
            const urlInput = document.getElementById('pollUrlInput');

            try {
                await navigator.clipboard.writeText(urlInput.value);
                showAlert('Poll link copied to clipboard!', 'success');

                // Change button text temporarily
                const copyBtn = document.querySelector('#sharePollModal .btn-primary');
                const originalText = copyBtn.innerHTML;
                copyBtn.innerHTML = '<i class="fas fa-check"></i> Copied!';
                copyBtn.style.background = '#059669';

                setTimeout(() => {
                    copyBtn.innerHTML = originalText;
                    copyBtn.style.background = '';
                }, 2000);

            } catch (err) {
                // Fallback for older browsers
                urlInput.select();
                urlInput.setSelectionRange(0, 99999);
                document.execCommand('copy');
                showAlert('Poll link copied to clipboard!', 'success');
            }
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('sharePollModal');
            if (event.target === modal) {
                closeShareModal();
            }
        });

        // Event listeners
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyFilters();
            }
        });

        document.getElementById('statusFilter').addEventListener('change', applyFilters);
        document.getElementById('sortBy').addEventListener('change', applyFilters);

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadPolls();
        });
    </script>
@endsection
