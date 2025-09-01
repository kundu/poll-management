<!-- Poll Details Modal -->
<div id="pollDetailsModal" class="modal" style="display: none;">
    <div class="modal-overlay" onclick="closePollDetailsModal()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Poll Details</h3>
            <button class="modal-close" onclick="closePollDetailsModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div id="pollDetailsContent">
                <!-- Poll details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<style>
    /* Poll Details Modal Styles */
    .poll-details-section {
        margin-bottom: 2rem;
    }

    .poll-details-section:last-child {
        margin-bottom: 0;
    }

    .poll-details-section h4 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.75rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid var(--border-color);
    }

    .poll-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .poll-info-item {
        background: var(--background-color);
        padding: 1rem;
        border-radius: var(--radius);
        border: 1px solid var(--border-color);
    }

    .poll-info-label {
        font-size: 0.75rem;
        font-weight: 500;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }

    .poll-info-value {
        font-size: 0.875rem;
        color: var(--text-primary);
        font-weight: 500;
    }

    .poll-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .poll-status-badge.active {
        background: rgba(16, 185, 129, 0.1);
        color: var(--primary-color);
        border: 1px solid rgba(16, 185, 129, 0.2);
    }

    .poll-status-badge.inactive {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
        border: 1px solid rgba(245, 158, 11, 0.2);
    }

    .poll-options-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .poll-option-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        background: var(--background-color);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
    }

    .poll-option-order {
        background: var(--primary-color);
        color: white;
        border-radius: 50%;
        width: 1.5rem;
        height: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 600;
        flex-shrink: 0;
    }

    .poll-option-text {
        flex: 1;
        font-size: 0.875rem;
        color: var(--text-primary);
    }

    .poll-option-votes {
        background: var(--surface-color);
        color: var(--text-secondary);
        padding: 0.25rem 0.5rem;
        border-radius: var(--radius);
        font-size: 0.75rem;
        font-weight: 500;
    }

    .poll-creator-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        background: var(--background-color);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
    }

    .poll-creator-avatar {
        width: 2.5rem;
        height: 2.5rem;
        background: var(--primary-color);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .poll-creator-details {
        flex: 1;
    }

    .poll-creator-name {
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .poll-creator-email {
        font-size: 0.75rem;
        color: var(--text-secondary);
    }

    .poll-creator-type {
        background: var(--primary-color);
        color: white;
        padding: 0.125rem 0.5rem;
        border-radius: 0.5rem;
        font-size: 0.625rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .poll-dates {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .poll-date-item {
        background: var(--background-color);
        padding: 0.75rem;
        border-radius: var(--radius);
        border: 1px solid var(--border-color);
    }

    .poll-date-label {
        font-size: 0.75rem;
        color: var(--text-secondary);
        margin-bottom: 0.25rem;
    }

    .poll-date-value {
        font-size: 0.875rem;
        color: var(--text-primary);
        font-weight: 500;
    }

    .loading-details {
        text-align: center;
        padding: 2rem;
    }

    .loading-details .loading {
        width: 2rem;
        height: 2rem;
        margin: 0 auto 1rem;
    }

    .error-details {
        text-align: center;
        padding: 2rem;
        color: var(--danger-color);
    }

    .error-details i {
        font-size: 2rem;
        margin-bottom: 1rem;
        display: block;
    }
</style>

<script>
    // Poll Details Modal Functions
    function openPollDetailsModal(pollId) {
        const modal = document.getElementById('pollDetailsModal');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';

        // Load poll details
        loadPollDetails(pollId);
    }

    function closePollDetailsModal() {
        const modal = document.getElementById('pollDetailsModal');
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    async function loadPollDetails(pollId) {
        const content = document.getElementById('pollDetailsContent');

        // Show loading state
        content.innerHTML = `
            <div class="loading-details">
                <div class="loading"></div>
                <p>Loading poll details...</p>
            </div>
        `;

        try {
            const response = await fetch(`${API_BASE_URL}/admin/polls/${pollId}`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${getAuthToken()}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Failed to fetch poll details');
            }

            const data = await response.json();

            if (data.success) {
                displayPollDetails(data.data.poll);
            } else {
                throw new Error(data.message || 'Failed to load poll details');
            }

        } catch (error) {
            console.error('Error loading poll details:', error);
            content.innerHTML = `
                <div class="error-details">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Failed to load poll details</p>
                    <p style="font-size: 0.875rem; margin-top: 0.5rem;">${error.message}</p>
                </div>
            `;
        }
    }

    function displayPollDetails(poll) {
        const content = document.getElementById('pollDetailsContent');

        const statusBadge = poll.status == 1
            ? '<span class="poll-status-badge active"><i class="fas fa-check"></i> Active</span>'
            : '<span class="poll-status-badge inactive"><i class="fas fa-pause"></i> Inactive</span>';

        const guestVotingBadge = poll.allow_guest_voting
            ? '<span class="poll-status-badge active"><i class="fas fa-users"></i> Guest Voting Enabled</span>'
            : '<span class="poll-status-badge inactive"><i class="fas fa-user-lock"></i> Guest Voting Disabled</span>';

        const createdDate = new Date(poll.created_at).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: 'numeric',
            minute: '2-digit'
        });

        const updatedDate = new Date(poll.updated_at).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: 'numeric',
            minute: '2-digit'
        });

        const creatorInitials = poll.creator.name.split(' ').map(n => n[0]).join('').toUpperCase();
        const creatorType = poll.creator.user_type == 2 ? 'Admin' : 'User';

        let optionsHTML = '';
        if (poll.options && poll.options.length > 0) {
            optionsHTML = poll.options.map(option => `
                <div class="poll-option-item">
                    <div class="poll-option-order">${option.order_index}</div>
                    <div class="poll-option-text">${option.option_text}</div>
                    <div class="poll-option-votes">
                        ${option.votes ? option.votes.length : 0} votes
                    </div>
                </div>
            `).join('');
        } else {
            optionsHTML = '<p style="color: var(--text-secondary); text-align: center; padding: 1rem;">No options found</p>';
        }

        content.innerHTML = `
            <!-- Poll Information -->
            <div class="poll-details-section">
                <h4>Poll Information</h4>
                <div class="poll-info-grid">
                    <div class="poll-info-item">
                        <div class="poll-info-label">Title</div>
                        <div class="poll-info-value">${poll.title}</div>
                    </div>
                    <div class="poll-info-item">
                        <div class="poll-info-label">Status</div>
                        <div class="poll-info-value">${statusBadge}</div>
                    </div>
                    <div class="poll-info-item">
                        <div class="poll-info-label">Guest Voting</div>
                        <div class="poll-info-value">${guestVotingBadge}</div>
                    </div>
                    <div class="poll-info-item">
                        <div class="poll-info-label">Poll ID</div>
                        <div class="poll-info-value">#${poll.id}</div>
                    </div>
                </div>

                ${poll.description ? `
                    <div class="poll-info-item" style="grid-column: 1 / -1;">
                        <div class="poll-info-label">Description</div>
                        <div class="poll-info-value">${poll.description}</div>
                    </div>
                ` : ''}
            </div>

            <!-- Poll Options -->
            <div class="poll-details-section">
                <h4>Poll Options (${poll.options ? poll.options.length : 0})</h4>
                <div class="poll-options-list">
                    ${optionsHTML}
                </div>
            </div>

            <!-- Creator Information -->
            <div class="poll-details-section">
                <h4>Created By</h4>
                <div class="poll-creator-info">
                    <div class="poll-creator-avatar">${creatorInitials}</div>
                    <div class="poll-creator-details">
                        <div class="poll-creator-name">${poll.creator.name}</div>
                        <div class="poll-creator-email">${poll.creator.email}</div>
                    </div>
                    <div class="poll-creator-type">${creatorType}</div>
                </div>
            </div>

            <!-- Dates -->
            <div class="poll-details-section">
                <h4>Timeline</h4>
                <div class="poll-dates">
                    <div class="poll-date-item">
                        <div class="poll-date-label">Created</div>
                        <div class="poll-date-value">${createdDate}</div>
                    </div>
                    <div class="poll-date-item">
                        <div class="poll-date-label">Last Updated</div>
                        <div class="poll-date-value">${updatedDate}</div>
                    </div>
                </div>
            </div>
        `;
    }
</script>
