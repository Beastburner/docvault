# DocVault - Project Structure & Information

## Overview
**DocVault** is a PHP-based document management system that provides secure storage, organization, and retrieval of documents with a modern web interface.

---

## Core Components

### 1. `index.php` (Main Application - 99.6 KB)
Full-stack application entry point combining PHP backend with JavaScript frontend.

#### Frontend Features
- Interactive dashboard  
- Drag-and-drop file upload  
- Document management (Grid/List view)  
- Folder organization with color coding  
- Dark mode support  
- Real-time storage tracking (**512 MB per user**)  
- Document search and filtering  
- Session persistence using PHP injection  

#### Includes
- User authentication (Sign In / Sign Up)  
- Upload and organization tools  
- Secure document handling  

---

### 2. `auth.php` (Authentication Handler - 9.8 KB)
Handles user authentication and session management.

#### Capabilities
- Login / Signup functionality  
- Password hashing (**bcrypt**)  
- Session management  
- Demo user accounts for testing  
- Dual storage support:
  - MySQL  
  - Flat-file JSON  

#### Security
- Input validation  
- Input sanitization  

---

### 3. `db.php` (Database Connection - 1.9 KB)
PDO-based MySQL connection manager.

#### Configuration
- XAMPP defaults:
  - Host: `localhost`
  - User: `root`
  - Password: *(empty)*

#### Features
- UTF-8 character set (`utf8mb4`)  
- Error handling  
- Logging support  

---

### 4. `schema.sql` (Database Schema - 1.9 KB)
Creates the **docvault** database and `users` table.

#### Users Table Structure
| Field | Type | Description |
|------|------|-------------|
| id | INT | Auto-increment primary key |
| first_name | VARCHAR | User first name |
| last_name | VARCHAR | User last name |
| email | VARCHAR | Unique email address |
| password | VARCHAR | Bcrypt hashed password |
| storage_used | BIGINT | Storage consumed |
| created_at | TIMESTAMP | Account creation time |

---

## Key Features
- ✅ Document upload (PDF, DOCX, XLS, Images, etc.)  
- ✅ OCR scanning capability  
- ✅ Folder management with colors  
- ✅ Full-text search  
- ✅ Storage quota (**512 MB per user**)  
- ✅ Dark/Light mode toggle  
- ✅ Responsive design  
- ✅ Session persistence  

---

## Technology Stack

### Backend
- PHP 7+
- PDO

### Database
- MySQL (XAMPP)

### Frontend
- Vanilla JavaScript  
- HTML5  
- CSS3  

### Storage
- LocalStorage for client-side document data  

---

## Authors
- **Parth Soni**  
- **Mann Soni**
