<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
// ============================================================
//  db.php — PDO Connection (XAMPP / MySQL)
//
//  XAMPP defaults:
//    host     : 127.0.0.1
//    user     : root
//    password : (empty string — XAMPP ships with no root password)
//    database : docvault
//
//  Steps before this works:
//  1. Start XAMPP Control Panel → click Start for Apache + MySQL
//  2. Open phpMyAdmin → http://localhost/phpmyadmin
//  3. Import tab → choose schema.sql → click Go
//  4. Confirm `docvault` database + `users` table exist
// ============================================================

define('DB_HOST', 'localhost');
define('DB_NAME',    'docvault');
define('DB_USER',    'root');       // XAMPP default — change if you added a root password
define('DB_PASS',    '');           // XAMPP default — empty string
define('DB_CHARSET', 'utf8mb4');
define('DB_PORT',    3306);         // XAMPP default MySQL port

function getPDO(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
    );

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        error_log('[DOCVAULT] DB connection failed: ' . $e->getMessage());
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'ok'    => false,
            'error' => 'Database unavailable. Is XAMPP MySQL running?'
        ]);
        exit;
    }

    return $pdo;
}
