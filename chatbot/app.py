"""
chatbot/app.py
Simple Flask chatbot that responds to greetings, motivation, and task help.
Run with: python app.py
"""

from flask import Flask, request, jsonify
from flask_cors import CORS
import random

app = Flask(__name__)
# Allow local PHP frontend (http://localhost/WorkReminder) to call this API.
CORS(app)

GREETINGS = ["Hello!", "Hi there!", "Hey!", "Welcome back!"]
MOTIVATION = [
    "You can do it! Stay focused.",
    "Small steps each day lead to big results.",
    "Believe in yourself, you are capable.",
    "Stay positive and keep moving forward."
]

TASK_HELP = {
    "add task": "Go to Add Task page from navbar, fill Title/Date/Time/Priority.",
    "show tasks": "Open Dashboard to view today, upcoming, and completed tasks.",
    "reminder": "Notifications pop when due time is reached; keep the tab open at least in background.",
}


def classify(message: str) -> str:
    """
    Very lightweight rule-based classifier for a viva-friendly explanation.
    """
    text = message.lower()
    if any(g in text for g in ["hi", "hello", "hey"]):
        return "greeting"
    if any(k in text for k in ["motivate", "motivation", "encourage"]):
        return "motivation"
    if any(k in text for k in ["add task", "create task"]):
        return "add task"
    if any(k in text for k in ["show task", "view task", "list task"]):
        return "show tasks"
    if any(k in text for k in ["reminder", "notify", "notification"]):
        return "reminder"
    return "fallback"


@app.route("/chat", methods=["POST"])
def chat():
    """
    Accepts JSON: { "message": "text" }
    Returns: { "reply": "text" }
    """
    data = request.get_json(silent=True) or {}
    message = (data.get("message") or "").strip()

    if not message:
        return jsonify({"reply": "Please type something so I can help."})

    category = classify(message)

    if category == "greeting":
        return jsonify({"reply": random.choice(GREETINGS)})
    if category == "motivation":
        return jsonify({"reply": random.choice(MOTIVATION)})
    if category in TASK_HELP:
        return jsonify({"reply": TASK_HELP[category]})

    # Default fallback.
    return jsonify({
        "reply": "I'm a simple helper bot. Ask me about adding tasks, viewing tasks, reminders, or say 'motivation'."
    })


if __name__ == "__main__":
    # Run on port 5000 by default.
    app.run(host="127.0.0.1", port=5000, debug=True)

