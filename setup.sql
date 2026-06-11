-- Blog Database Setup (Task 2 base + Task 3 ready)
-- Run this in phpMyAdmin or MySQL CLI

CREATE DATABASE IF NOT EXISTS blog CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE blog;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','editor','viewer') DEFAULT 'editor',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Posts table
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    author_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Sample admin user (password: admin123)
INSERT IGNORE INTO users (username, password, role)
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Sample posts for testing pagination & search
INSERT IGNORE INTO posts (title, content, author_id) VALUES
('Welcome to the Blog', 'This is the first post on our blog. We are excited to share content with you!', 1),
('PHP Tips and Tricks', 'PHP is a versatile language. Here are some tips: use prepared statements, validate inputs, and keep code clean.', 1),
('MySQL Best Practices', 'When working with MySQL, always index your columns, use transactions for critical operations, and back up regularly.', 1),
('Getting Started with XAMPP', 'XAMPP makes it easy to run Apache and MySQL locally. Download, install, and start the services to begin development.', 1),
('Understanding Sessions in PHP', 'PHP sessions allow you to store user data across multiple pages. Use session_start() at the top of every page.', 1),
('Bootstrap for Responsive Design', 'Bootstrap is a popular CSS framework. It helps you build mobile-first responsive layouts with minimal effort.', 1),
('Form Validation Techniques', 'Always validate forms on both client and server side. Never trust user input — sanitize and validate everything.', 1),
('Introduction to CRUD Operations', 'CRUD stands for Create, Read, Update, Delete. These are the four basic operations for any data-driven application.', 1),
('CSS Flexbox Guide', 'Flexbox is a powerful CSS layout module. It makes aligning elements horizontally and vertically straightforward.', 1),
('Deploying PHP Applications', 'When deploying PHP apps, configure your web server, secure your database credentials, and enable HTTPS.', 1),
('JavaScript Basics for PHP Devs', 'As a PHP developer, knowing JavaScript helps you add interactivity. Start with DOM manipulation and event listeners.', 1),
('Password Hashing in PHP', 'Never store plain-text passwords. Use password_hash() with PASSWORD_BCRYPT and verify using password_verify().', 1);
