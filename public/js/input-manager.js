class InputManager {
    constructor() {
        this.resource = window.RESOURCE;
        this.initEventListeners();
    }

    initEventListeners() {
        // Row click for view
        document.querySelectorAll('tbody tr[data-input-id]').forEach(row => {
            row.addEventListener('click', (e) => {
                if (!e.target.closest('button')) {
                    this.showDetailModal(row.dataset.inputId);
                }
            });
        });
    }

    showDetailModal(inputId) {
        fetch(`/${this.resource}/${inputId}`)
            .then(response => response.json())
            .then(data => {
                // Implement modal display logic
                console.log('Showing details for:', data);
            });
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    new InputManager();
});

// Global functions
function openEditModal(resource, data) {
    console.log(`Editing ${resource} with data:`, data);
    // Implementation will vary per resource type
}