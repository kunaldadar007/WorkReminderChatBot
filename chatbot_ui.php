<?php
// chatbot_ui.php
// Simple chat interface that talks to Flask chatbot via fetch.

require_once __DIR__ . '/includes/auth_check.php';
include __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <h3 class="card-title mb-3">Chatbot</h3>
                <p class="text-muted small">
                    The chatbot runs on Python Flask (default: http://127.0.0.1:5000). Make sure it is running.
                </p>

                <div id="chat-window" class="chat-window mb-3">
                    <!-- Messages will be appended here -->
                </div>

                <form id="chat-form" class="d-flex">
                    <input type="text" id="chat-input" class="form-control me-2" placeholder="Type a message..." required>
                    <button type="submit" class="btn btn-primary">Send</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const chatWindow = document.getElementById('chat-window');
    const chatForm = document.getElementById('chat-form');
    const chatInput = document.getElementById('chat-input');

    function addMessage(text, sender) {
        const wrapper = document.createElement('div');
        wrapper.className = sender === 'user' ? 'chat-message-user' : 'chat-message-bot';
        const bubble = document.createElement('div');
        bubble.className = 'chat-bubble ' + (sender === 'user' ? 'chat-bubble-user' : 'chat-bubble-bot');
        bubble.textContent = text;
        wrapper.appendChild(bubble);
        chatWindow.appendChild(wrapper);
        chatWindow.scrollTop = chatWindow.scrollHeight;
    }

    async function saveHistory(sender, message) {
        try {
            await fetch('/WorkReminder/chatbot/save_chat.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ sender, message })
            });
        } catch (err) {
            console.warn('History save failed', err);
        }
    }

    async function loadHistory() {
        try {
            const res = await fetch('/WorkReminder/chatbot/get_history.php');
            const data = await res.json();
            (data.history || []).forEach(item => addMessage(item.message, item.sender));
        } catch (err) {
            console.warn('History load failed', err);
        }
    }

    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const message = chatInput.value.trim();
        if (!message) return;
        addMessage(message, 'user');
        saveHistory('user', message);
        chatInput.value = '';

        try {
            const res = await fetch('http://127.0.0.1:5000/chat', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message })
            });
            const data = await res.json();
            const reply = data.reply || 'No reply received.';
            addMessage(reply, 'bot');
            saveHistory('bot', reply);
        } catch (err) {
            const failMsg = 'Error contacting chatbot. Is Flask running?';
            addMessage(failMsg, 'bot');
            saveHistory('bot', failMsg);
            console.error(err);
        }
    });

    loadHistory();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>

