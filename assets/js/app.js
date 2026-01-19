// assets/js/app.js
// Small helper functions used across pages.

// Nothing heavy here yet, but this file is a good place for
// future UI helpers. For now we keep it lightweight.

// Example: auto-dismiss alerts after a few seconds (optional).
document.addEventListener('DOMContentLoaded', () => {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.classList.add('fade');
            setTimeout(() => alert.remove(), 500);
        }, 4000);
    });
});

