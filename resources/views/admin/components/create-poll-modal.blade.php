<!-- Create Poll Modal -->
<div id="createPollModal" class="modal" style="display: none;">
    <div class="modal-overlay" onclick="closeCreatePollModal()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Create New Poll</h3>
            <button class="modal-close" onclick="closeCreatePollModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="createPollForm">
                <div class="form-group">
                    <label class="form-label">Poll Title *</label>
                    <input type="text" class="form-input" id="pollTitle" name="title" placeholder="Enter poll title" required>
                    <div class="form-error" id="titleError"></div>
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea class="form-input" id="pollDescription" name="description" rows="3" placeholder="Enter poll description (optional)"></textarea>
                    <div class="form-error" id="descriptionError"></div>
                </div>

                <div class="form-group">
                    <label class="form-label">Guest Voting</label>
                    <div class="checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" id="allowGuestVoting" name="allow_guest_voting" checked>
                            <span class="checkmark"></span>
                            Allow guests to vote without registration
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Poll Options *</label>
                    <div class="options-container">
                        <div id="optionsList" class="options-list">
                            <!-- Options will be added here dynamically -->
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="addOption()">
                            <i class="fas fa-plus"></i>
                            Add Option
                        </button>
                    </div>
                    <div class="form-error" id="optionsError"></div>
                    <small class="form-help">Drag options to reorder them. You need at least 2 options.</small>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeCreatePollModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="createPollBtn">
                        <span id="createPollText">Create Poll</span>
                        <span id="createPollSpinner" class="loading" style="display: none;"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Modal Styles */
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
    }

    .modal-content {
        background: var(--surface-color);
        border-radius: var(--radius);
        box-shadow: var(--shadow-lg);
        width: 90%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
        z-index: 1001;
        box-shadow: 6px 7px 39px #101c15;
    }

    .modal-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }

    .modal-close {
        background: none;
        border: none;
        color: var(--text-secondary);
        font-size: 1.25rem;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: var(--radius);
        transition: var(--transition);
    }

    .modal-close:hover {
        background: var(--background-color);
        color: var(--text-primary);
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border-color);
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
        background: var(--surface-color);
    }

    .form-input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }

    textarea.form-input {
        resize: vertical;
        min-height: 80px;
    }

    .form-error {
        color: var(--danger-color);
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }

    .form-help {
        color: var(--text-secondary);
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }

    /* Checkbox Styles */
    .checkbox-group {
        margin-top: 0.5rem;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        font-size: 0.875rem;
        color: var(--text-primary);
    }

    .checkbox-label input[type="checkbox"] {
        display: none;
    }

    .checkmark {
        width: 1.25rem;
        height: 1.25rem;
        border: 2px solid var(--border-color);
        border-radius: 4px;
        position: relative;
        transition: var(--transition);
    }

    .checkbox-label input[type="checkbox"]:checked + .checkmark {
        background: var(--primary-color);
        border-color: var(--primary-color);
    }

    .checkbox-label input[type="checkbox"]:checked + .checkmark::after {
        content: '\f00c';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        color: white;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 0.75rem;
    }

    /* Options Styles */
    .options-container {
        margin-top: 0.5rem;
    }

    .options-list {
        margin-bottom: 1rem;
    }

    .option-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        background: var(--background-color);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        margin-bottom: 0.5rem;
        cursor: move;
        transition: var(--transition);
    }

    .option-item:hover {
        border-color: var(--primary-color);
    }

    .option-item.dragging {
        opacity: 0.5;
        transform: rotate(2deg);
    }

    .option-drag-handle {
        color: var(--text-secondary);
        cursor: move;
        padding: 0.25rem;
    }

    .option-input {
        flex: 1;
        padding: 0.5rem 0.75rem;
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        font-size: 0.875rem;
        background: var(--surface-color);
    }

    .option-input:focus {
        outline: none;
        border-color: var(--primary-color);
    }

    .option-remove {
        background: var(--danger-color);
        color: white;
        border: none;
        border-radius: var(--radius);
        width: 2rem;
        height: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: var(--transition);
    }

    .option-remove:hover {
        background: #dc2626;
    }

    .option-order {
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
    }

    .btn-sm {
        padding: 0.5rem 1rem;
        font-size: 0.75rem;
    }
</style>

<script>
    // Global variables for create poll modal
    let optionCounter = 0;
    let draggedElement = null;

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

    // Form submission
    document.addEventListener('DOMContentLoaded', function() {
        const createPollForm = document.getElementById('createPollForm');
        if (createPollForm) {
            createPollForm.addEventListener('submit', handleCreatePollSubmit);
        }
    });

    async function handleCreatePollSubmit(e) {
        e.preventDefault();

        clearFormErrors();

        // Get form data
        const title = document.getElementById('pollTitle').value.trim();
        const description = document.getElementById('pollDescription').value.trim();
        const allowGuestVoting = document.getElementById('allowGuestVoting').checked;

        // Get options
        const optionInputs = document.querySelectorAll('.option-input');
        const options = [];

        optionInputs.forEach((input, index) => {
            const optionText = input.value.trim();
            if (optionText) {
                options.push({
                    option_text: optionText,
                    order_index: index + 1
                });
            }
        });

        // Validation
        let hasErrors = false;

        if (!title) {
            document.getElementById('titleError').textContent = 'Poll title is required';
            hasErrors = true;
        }

        if (options.length < 2) {
            document.getElementById('optionsError').textContent = 'At least 2 options are required';
            hasErrors = true;
        }

        if (hasErrors) {
            return;
        }

        // Show loading state
        const createPollBtn = document.getElementById('createPollBtn');
        const createPollText = document.getElementById('createPollText');
        const createPollSpinner = document.getElementById('createPollSpinner');

        createPollBtn.disabled = true;
        createPollText.style.display = 'none';
        createPollSpinner.style.display = 'inline-block';

        try {
            // Prepare request data
            const requestData = {
                title: title,
                description: description,
                allow_guest_voting: allowGuestVoting,
                options: options
            };

            // Make API call
            const response = await fetch(`${API_BASE_URL}/admin/polls`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${getAuthToken()}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(requestData)
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Show success message
                showAlert('Poll created successfully!', 'success');

                // Close modal
                closeCreatePollModal();

                // Refresh data based on current page
                if (typeof loadDashboardData === 'function') {
                    loadDashboardData(); // Dashboard page
                } else if (typeof loadPolls === 'function') {
                    loadPolls(); // Polls page
                }

            } else {
                // Handle validation errors
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const errorElement = document.getElementById(field + 'Error');
                        if (errorElement) {
                            errorElement.textContent = data.errors[field][0];
                        }
                    });
                } else if (data.message) {
                    showAlert(data.message);
                } else {
                    showAlert('Failed to create poll. Please try again.');
                }
            }

        } catch (error) {
            console.error('Error creating poll:', error);
            showAlert('Network error. Please check your connection and try again.');
        } finally {
            // Reset loading state
            createPollBtn.disabled = false;
            createPollText.style.display = 'inline';
            createPollSpinner.style.display = 'none';
        }
    }
</script>
