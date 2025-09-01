@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <!-- Loading State -->
    <div id="loadingState" style="text-align: center; padding: 3rem;">
        <div class="loading" style="width: 2rem; height: 2rem; margin: 0 auto 1rem;"></div>
        <p style="color: var(--text-secondary);">Loading dashboard data...</p>
    </div>

    <!-- Dashboard Content -->
    <div id="dashboardContent" style="display: none;">
        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-value" id="totalPolls">-</div>
                        <div class="stat-label">Total Polls</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-poll"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-value" id="activePolls">-</div>
                        <div class="stat-label">Active Polls</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-value" id="inactivePolls">-</div>
                        <div class="stat-label">Inactive Polls</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-pause-circle"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-value" id="totalVotes">-</div>
                        <div class="stat-label">Total Votes</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-vote-yea"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Quick Actions</h3>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <a href="{{ route('admin.polls') }}" class="btn btn-primary">
                        <i class="fas fa-list"></i>
                        View All Polls
                    </a>
                    <button class="btn btn-primary" onclick="openCreatePollModal()">
                        <i class="fas fa-plus"></i>
                        Create New Poll
                    </button>
                    <a href="#" class="btn btn-secondary">
                        <i class="fas fa-chart-bar"></i>
                        View Analytics
                    </a>
                    <a href="{{ route('admin.settings') }}" class="btn btn-secondary">
                        <i class="fas fa-cog"></i>
                        Settings
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Polls</h3>
            </div>
            <div class="card-body">
                <div style="overflow-x: auto;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody id="recentPollsTableBody">
                            <!-- Recent polls will be loaded here via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Error State -->
    <div id="errorState" class="card" style="display: none;">
        <div class="card-body" style="text-align: center; padding: 3rem;">
            <i class="fas fa-exclamation-triangle" style="font-size: 3rem; margin-bottom: 1rem; display: block; color: var(--danger-color);"></i>
            <div style="font-size: 1.125rem; margin-bottom: 0.5rem; color: var(--text-secondary);">Failed to load dashboard data</div>
            <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 1rem;" id="errorMessage">Please try refreshing the page</div>
            <button class="btn btn-primary" onclick="loadDashboardData()">
                <i class="fas fa-refresh"></i>
                Retry
            </button>
        </div>
    </div>

    @include('admin.components.create-poll-modal')



    <script>
        // Load dashboard data from API
        async function loadDashboardData() {
            const loadingState = document.getElementById('loadingState');
            const dashboardContent = document.getElementById('dashboardContent');
            const errorState = document.getElementById('errorState');

            // Show loading
            loadingState.style.display = 'block';
            dashboardContent.style.display = 'none';
            errorState.style.display = 'none';

            try {
                const response = await fetch(`${API_BASE_URL}/admin/dashboard`, {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${getAuthToken()}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Failed to fetch dashboard data');
                }

                const data = await response.json();

                if (data.success) {
                    displayDashboardData(data.data);
                } else {
                    throw new Error(data.message || 'Failed to load dashboard data');
                }

            } catch (error) {
                console.error('Error loading dashboard data:', error);
                showError('Failed to load dashboard data. Please try again.');
            } finally {
                loadingState.style.display = 'none';
            }
        }

        // Display dashboard data
        function displayDashboardData(data) {
            const dashboardContent = document.getElementById('dashboardContent');

            // Update stats
            document.getElementById('totalPolls').textContent = data.stats.total_polls;
            document.getElementById('activePolls').textContent = data.stats.active_polls;
            document.getElementById('inactivePolls').textContent = data.stats.inactive_polls;
            document.getElementById('totalVotes').textContent = data.stats.total_votes;

            // Update recent polls table
            displayRecentPolls(data.recent_polls);

            // Show dashboard content
            dashboardContent.style.display = 'block';
        }

        // Display recent polls in table
        function displayRecentPolls(polls) {
            const tableBody = document.getElementById('recentPollsTableBody');

            if (!polls || polls.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--text-secondary); padding: 2rem;">
                            <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                            No polls created yet
                        </td>
                    </tr>
                `;
                return;
            }

            let tableHTML = '';
            polls.forEach(poll => {
                const statusBadge = poll.status == 1
                    ? '<span class="badge badge-success"><i class="fas fa-check"></i> Active</span>'
                    : '<span class="badge badge-warning"><i class="fas fa-pause"></i> Inactive</span>';

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
                            <div style="font-weight: 500;">${poll.title}</div>
                            ${poll.description ? `
                                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.25rem;">
                                    ${poll.description.length > 50 ? poll.description.substring(0, 50) + '...' : poll.description}
                                </div>
                            ` : ''}
                        </td>
                        <td>${statusBadge}</td>
                        <td>
                            <div style="font-size: 0.875rem;">
                                ${createdDate}
                            </div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">
                                ${createdTime}
                            </div>
                        </td>
                    </tr>
                `;
            });

            tableBody.innerHTML = tableHTML;
        }

        // Show error state
        function showError(message) {
            const errorState = document.getElementById('errorState');
            const errorMessage = document.getElementById('errorMessage');

            errorMessage.textContent = message;
            errorState.style.display = 'block';
        }

        // Modal functions
        function openCreatePollModal() {
            const modal = document.getElementById('createPollModal');
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';

            // Initialize form
            resetCreatePollForm();
            addOption(); // Add first option
            addOption(); // Add second option
        }

        function closeCreatePollModal() {
            const modal = document.getElementById('createPollModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function resetCreatePollForm() {
            document.getElementById('createPollForm').reset();
            document.getElementById('optionsList').innerHTML = '';
            optionCounter = 0;
            clearFormErrors();
        }

        function clearFormErrors() {
            const errorElements = document.querySelectorAll('.form-error');
            errorElements.forEach(element => {
                element.textContent = '';
            });
        }

        // Option management
        function addOption() {
            optionCounter++;
            const optionsList = document.getElementById('optionsList');
            const optionItem = document.createElement('div');
            optionItem.className = 'option-item';
            optionItem.draggable = true;
            optionItem.dataset.optionId = optionCounter;

            optionItem.innerHTML = `
                <div class="option-drag-handle">
                    <i class="fas fa-grip-vertical"></i>
                </div>
                <div class="option-order">${optionCounter}</div>
                <input type="text" class="option-input" placeholder="Enter option text" required>
                <button type="button" class="option-remove" onclick="removeOption(this)">
                    <i class="fas fa-times"></i>
                </button>
            `;

            // Add drag and drop event listeners
            addDragListeners(optionItem);

            optionsList.appendChild(optionItem);
            updateOptionOrders();
        }

        function removeOption(button) {
            const optionItem = button.closest('.option-item');
            optionItem.remove();
            updateOptionOrders();
        }

        function updateOptionOrders() {
            const optionItems = document.querySelectorAll('.option-item');
            optionItems.forEach((item, index) => {
                const orderElement = item.querySelector('.option-order');
                orderElement.textContent = index + 1;
            });
        }

        // Drag and drop functionality
        function addDragListeners(element) {
            element.addEventListener('dragstart', handleDragStart);
            element.addEventListener('dragend', handleDragEnd);
            element.addEventListener('dragover', handleDragOver);
            element.addEventListener('drop', handleDrop);
        }

        function handleDragStart(e) {
            draggedElement = e.target;
            e.target.classList.add('dragging');
        }

        function handleDragEnd(e) {
            e.target.classList.remove('dragging');
            draggedElement = null;
        }

        function handleDragOver(e) {
            e.preventDefault();
        }

        function handleDrop(e) {
            e.preventDefault();
            const optionsList = document.getElementById('optionsList');
            const afterElement = getDragAfterElement(optionsList, e.clientY);

            if (afterElement == null) {
                optionsList.appendChild(draggedElement);
            } else {
                optionsList.insertBefore(draggedElement, afterElement);
            }

            updateOptionOrders();
        }

        function getDragAfterElement(container, y) {
            const draggableElements = [...container.querySelectorAll('.option-item:not(.dragging)')];

            return draggableElements.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;

                if (offset < 0 && offset > closest.offset) {
                    return { offset: offset, element: child };
                } else {
                    return closest;
                }
            }, { offset: Number.NEGATIVE_INFINITY }).element;
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboardData();
        });
    </script>
@endsection
