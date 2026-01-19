# Work Reminder and Chat Bot

## Project Overview
Work Reminder and Chat Bot is a PHP + MySQL web app for managing tasks and receiving time-based reminders via browser notifications. It also includes a Python Flask chatbot and an Admin panel (User/Admin roles).

## Software Requirements
- XAMPP (Apache + MySQL) with PHP 8
- Python 3.x
- A modern browser (Chrome/Edge recommended)

## Step-by-Step Run Instructions
1) Extract the ZIP
- Unzip `WorkReminderChatBot_FinalYear.zip`.

2) Place the folder in XAMPP
- Copy the extracted `WorkReminder` folder to:
  - `C:\xampp\htdocs\WorkReminder`

3) Import the database
- Start **Apache** and **MySQL** in XAMPP.
- Open phpMyAdmin:
  - `http://localhost/phpmyadmin`
- Create a database:
  - `work_reminder_db`
- Import:
  - `WorkReminder/database.sql`

4) (Optional) Check DB config
- If your MySQL user/password is different, edit:
  - `WorkReminder/config.php`

5) Run the Flask chatbot (exact commands)
- Open a terminal and run:

```bash
cd C:\xampp\htdocs\WorkReminder\chatbot
python -m venv venv
venv\Scripts\activate
pip install -r requirements.txt
python app.py
```

6) Open the UI in browser
- Open:
  - `http://localhost/WorkReminder`

## Login Credentials
- Admin:
  - Email: `admin@example.com`
  - Password: `password123`
- User:
  - Email: `demo@example.com`
  - Password: `password123`

## How to Test
1) Task add/edit/delete
- Login as user → Dashboard → Add Task
- Edit and delete tasks from Dashboard
- Mark tasks completed

2) Reminder notification
- On Dashboard, **allow notifications** when asked.
- Create a task due in the next 1–2 minutes.
- Keep the Dashboard open (tab can be minimized) and wait for the notification.

3) Chatbot
- Open **Chatbot** from the navbar.
- Send messages like:
  - “hello”
  - “motivation”
  - “how to add task”
- Chat history is saved in MySQL (`chatbot_history`).

4) Admin panel
- Login as admin → open **Admin Panel** from the navbar.
- View/delete users (admin accounts are protected) and tasks.

## Mobile notification note
- **HTTPS is required on phone browsers** for service workers/notifications.
- Desktop `http://localhost` works without HTTPS (localhost is treated as secure).

## Run Confirmation
Project tested and runnable on **Windows + XAMPP** by following the steps above.

