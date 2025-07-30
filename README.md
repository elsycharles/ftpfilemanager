# ftpfilemanager
PHP FTP File Management

# Secure PHP File Manager

This project is a web-based file manager in PHP with authentication, ZIP extraction, file/folder deletion, moving, and copying. It offers a modern and secure interface.

## Main Features

- **Authentication**:
  - Login required via a form (username and Argon2 hashed password).
  - Logout available.
- **File Explorer**:
  - Navigate the server directory tree.
  - Display folders and files with visual distinction.
  - Protects the `config.php` file from deletion and modification.
- **File Management**:
  - Delete files and folders (with confirmation and recursive deletion).
  - Move and copy files/folders (with automatic creation of destination folders).
  - Extract ZIP files into the current folder.
- **User Interface**:
  - Responsive and modern design (integrated CSS).
  - Multi-selection, select/deselect all.
  - Display action results (success/error).

## Installation

1. Place all files in the desired folder on your PHP server.
2. Edit the username and password hash in `config.php`:
   ```php
   $USERNAME = 'admin';
   $PASSWORD_HASH = '...'; // Argon2 hash
   ```
   To generate a hash, use [argon2.online](https://argon2.online/).
3. Access the page via your browser.

## Security

- The script protects the `config.php` file from deletion and modification.
- Paths are normalized and secured to prevent unauthorized access.
- PHP session is used for authentication management.

## Usage

1. Log in with your credentials.
2. Browse folders and files.
3. Use the buttons to extract a ZIP, delete, move, or copy items.
4. Log out using the button at the top right.

## Customization

- Modify the CSS design directly in `config.php`.
- Adapt features as needed (add restrictions, logs, etc).

## Warning

> This script is intended for use on private servers or in controlled environments. Do not use in production without a security audit.

---
© 2025 - PHP File Manager

