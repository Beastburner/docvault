DocVault - Project Structure & Information
Overview
DocVault is a PHP-based document management system that provides secure storage, organization, and retrieval of documents with a modern web interface.

Core Components
1. index.php (Main Application - 99.6 KB)
Full-stack application entry point combining PHP backend with JavaScript frontend
Frontend: Interactive dashboard with upload, organize, and search functionality
Features:
User authentication (sign in/sign up)
Drag-and-drop file upload
Document management (grid/list view)
Folder organization with color coding
Dark mode support
Real-time storage tracking (512 MB per user)
Document search and filtering
Session persistence with PHP injection
2. auth.php (Authentication Handler - 9.8 KB)
Handles user authentication and session management
Capabilities:
Login/Signup functionality
Password hashing (bcrypt)
Session management
Demo user accounts for testing
Support for dual storage: MySQL or flat-file JSON
Input validation and sanitization
3. db.php (Database Connection - 1.9 KB)
PDO-based MySQL connection manager
Configuration:
XAMPP defaults (localhost, root user, empty password)
UTF-8 character set (utf8mb4)
Error handling and logging
4. schema.sql (Database Schema - 1.9 KB)
Creates the docvault database and users table
User Table Structure:
id (Auto-increment primary key)
first_name, last_name
email (unique)
password (bcrypt hash)
storage_used (BIGINT)
created_at (timestamp)
Key Features
✅ Document upload (PDF, DOCX, XLS, images, etc.)
✅ OCR scanning capability
✅ Folder management with colors
✅ Full-text search
✅ Storage quota (512 MB per user)
✅ Dark/Light mode toggle
✅ Responsive design
✅ Session persistence

Technology Stack
Backend: PHP (7+) with PDO
Database: MySQL (XAMPP)
Frontend: Vanilla JavaScript, HTML5, CSS3
Storage: LocalStorage for client-side document data
Authors
Parth Soni
Mann Soni
