-- database.sql
-- MySQL schema for Work Reminder & Chat Bot
-- Import this via phpMyAdmin into a database (e.g., work_reminder_db)

CREATE DATABASE IF NOT EXISTS work_reminder_db;
USE work_reminder_db;

-- Users table with role support (user | admin)
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('user','admin') DEFAULT 'user',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE INDEX idx_users_role ON users(role);

-- Tasks table
CREATE TABLE IF NOT EXISTS tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(200) NOT NULL,
  description TEXT,
  due_date DATE NOT NULL,
  due_time TIME NOT NULL,
  priority VARCHAR(20) DEFAULT 'medium',
  status VARCHAR(20) DEFAULT 'pending', -- pending | completed
  reminder_sent TINYINT(1) DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_tasks_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE INDEX idx_tasks_user ON tasks(user_id);
CREATE INDEX idx_tasks_due ON tasks(due_date, due_time, status, reminder_sent);

-- Notifications log table
CREATE TABLE IF NOT EXISTS notifications_log (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  task_id INT NULL,
  channel VARCHAR(50) NOT NULL, -- web | email | whatsapp
  message TEXT,
  status VARCHAR(50) DEFAULT 'sent', -- sent | blocked | failed
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_notifications_task FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE INDEX idx_notifications_user ON notifications_log(user_id);

-- Chatbot history table to store conversation logs
CREATE TABLE IF NOT EXISTS chatbot_history (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  sender ENUM('user','bot') NOT NULL,
  message TEXT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_chatbot_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE INDEX idx_chatbot_user ON chatbot_history(user_id, created_at);

-- Sample admin and user (password: password123)
INSERT INTO users (name, email, password_hash, role) VALUES
('Admin User', 'admin@example.com', '$2y$10$GmK7dXN.uV.KxsXxhxL7BuZlZTa3YL6hFLaeL5MIcAvPPnlND002i', 'admin'),
('Demo User', 'demo@example.com', '$2y$10$GmK7dXN.uV.KxsXxhxL7BuZlZTa3YL6hFLaeL5MIcAvPPnlND002i', 'user');

-- Sample tasks for demo user (user id = 2)
INSERT INTO tasks (user_id, title, description, due_date, due_time, priority, status, reminder_sent)
VALUES
(2, 'Complete project report', 'Finish the Work Reminder documentation', CURDATE(), '23:59:00', 'high', 'pending', 0),
(2, 'Buy groceries', 'Milk, eggs, bread', DATE_ADD(CURDATE(), INTERVAL 1 DAY), '18:00:00', 'medium', 'pending', 0),
(2, 'Morning workout', '30 minutes cardio', CURDATE(), '07:00:00', 'low', 'completed', 1);

