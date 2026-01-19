// assets/js/notifications.js
// Handles browser notification permission, service worker registration,
// periodic polling for due tasks, and showing notifications.

// Expose a simple global for dashboard to initialize.
window.WorkReminderNotifications = (function () {
    const state = {
        apiEndpoint: '',
        pollHandle: null,
        swRegistration: null,
    };

    async function registerServiceWorker() {
        if (!('serviceWorker' in navigator)) {
            console.warn('Service workers not supported.');
            return null;
        }
        try {
            const reg = await navigator.serviceWorker.register('/WorkReminder/service-worker.js');
            state.swRegistration = reg;
            return reg;
        } catch (err) {
            console.error('Service worker registration failed', err);
            return null;
        }
    }

    function showFallbackLinks() {
        const fallback = document.getElementById('notification-fallback');
        const statusBox = document.getElementById('notification-status');
        if (statusBox) {
            statusBox.style.display = 'none';
        }
        if (!fallback) return;
        const emailLink = document.getElementById('email-fallback');
        const waLink = document.getElementById('whatsapp-fallback');
        const text = encodeURIComponent('Task reminder: please check your Work Reminder dashboard for due tasks.');
        const subject = encodeURIComponent('Task Reminder');
        if (emailLink) emailLink.href = `mailto:?subject=${subject}&body=${text}`;
        if (waLink) waLink.href = `https://wa.me/?text=${text}`;
        fallback.style.display = 'block';
    }

    function showStatus(message, type = 'info') {
        const box = document.getElementById('notification-status');
        if (!box) return;
        box.className = `alert alert-${type}`;
        box.textContent = message;
        box.style.display = 'block';
    }

    async function requestPermissionAndRegister() {
        if (!('Notification' in window)) {
            showStatus('Notifications not supported by this browser.', 'warning');
            showFallbackLinks();
            return false;
        }
        // NOTE: Service workers + notifications require HTTPS in browsers.
        // Localhost is treated as a secure context for development demos.
        // For mobile testing, host this app with HTTPS (self-signed is fine).
        const permission = await Notification.requestPermission();
        if (permission !== 'granted') {
            showFallbackLinks();
            showStatus('Notifications are blocked. Using fallback options.', 'warning');
            await logNotification(null, 'web', 'Notification blocked by user', 'blocked');
            return false;
        }
        const reg = await registerServiceWorker();
        if (reg) {
            showStatus('Notifications enabled. We will alert you at task time.', 'success');
        } else {
            showFallbackLinks();
        }
        return !!reg;
    }

    async function logNotification(taskId, channel, message, status) {
        try {
            await fetch('/WorkReminder/notifications/log_notification.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    task_id: taskId || '',
                    channel: channel || 'web',
                    message: message || '',
                    status: status || 'sent',
                })
            });
        } catch (err) {
            console.warn('Logging notification failed', err);
        }
    }

    async function pollDueTasks() {
        try {
            const res = await fetch(state.apiEndpoint, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            const data = await res.json();
            const tasks = data.tasks || [];
            if (!tasks.length || !state.swRegistration) return;

            tasks.forEach(task => {
                const title = `Task due: ${task.title}`;
                const timeText = task.due_time ? task.due_time.substring(0, 5) : '';
                const body = `${task.description || 'No description'} (Priority: ${task.priority}) at ${timeText}`;
                state.swRegistration.showNotification(title, {
                    body,
                    icon: 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="128" height="128" viewBox="0 0 24 24" fill="%230d6efd"><path d="M12 2a7 7 0 0 0-7 7v4.586L3.293 16.293a1 1 0 0 0 .707 1.707h16a1 1 0 0 0 .707-1.707L19 13.586V9a7 7 0 0 0-7-7zm0 20a3 3 0 0 0 2.995-2.824L15 19h-6a3 3 0 0 0 2.824 2.995L12 22z"/></svg>',
                    vibrate: [200, 100, 200],
                    data: { taskId: task.id },
                });
                logNotification(task.id, 'web', `Reminder: ${title}`, 'sent');
            });
        } catch (err) {
            console.error('Polling for due tasks failed', err);
        }
    }

    function startPolling() {
        if (state.pollHandle) {
            clearInterval(state.pollHandle);
        }
        state.pollHandle = setInterval(pollDueTasks, 60 * 1000); // every 60 seconds
        // Also run immediately on load to catch near-due tasks.
        pollDueTasks();
    }

    async function init(options) {
        state.apiEndpoint = options.apiEndpoint;
        const ok = await requestPermissionAndRegister();
        if (ok) {
            startPolling();
        }
    }

    return { init };
})();

