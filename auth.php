<?php
// ============================================================
//  auth.php — Authentication Handler
//
//  Modes:
//    USE_FLAT_FILE = true  → stores users in users.json (no MySQL needed)
//    USE_FLAT_FILE = false → uses MySQL via db.php / getPDO()
//
//  Responds with JSON:
//    { ok: true,  user: { id, email, firstName, lastName, storageUsed, storageLimit } }
//    { ok: false, error: "message" }
// ============================================================

define('USE_FLAT_FILE', false);         // real MySQL via XAMPP — set true for flat-file fallback
define('USERS_FILE', __DIR__ . '/users.json');
define('MAX_STORAGE', 512 * 1024 * 1024); // 512 MB per user

session_start();
header('Content-Type: application/json');

// ── Helpers ──────────────────────────────────────────────────

function jsonResponse(bool $ok, array $payload = [], string $error = ''): void {
    $body = ['ok' => $ok];
    if ($ok)    $body = array_merge($body, $payload);
    else        $body['error'] = $error;
    echo json_encode($body);
    exit;
}

function sanitize(string $value): string {
    return trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
}

// ── Flat-file storage helpers ─────────────────────────────────

function loadUsers(): array {
    if (!file_exists(USERS_FILE)) return [];
    $raw = file_get_contents(USERS_FILE);
    return json_decode($raw, true) ?: [];
}

function saveUsers(array $users): void {
    file_put_contents(USERS_FILE, json_encode(array_values($users), JSON_PRETTY_PRINT));
}

function findUserByEmail(array $users, string $email): ?array {
    foreach ($users as $u) {
        if (strtolower($u['email']) === strtolower($email)) return $u;
    }
    return null;
}

// ── Session check (called from index.php) ─────────────────────

if (isset($_GET['action']) && $_GET['action'] === 'check') {
    if (!empty($_SESSION['docvault_user'])) {
        jsonResponse(true, ['user' => $_SESSION['docvault_user']]);
    } else {
        jsonResponse(false, [], 'not_logged_in');
    }
}

// ── Logout ────────────────────────────────────────────────────

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $_SESSION = [];
    session_destroy();
    jsonResponse(true);
}

// ── Require POST for login / signup ──────────────────────────

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, [], 'Method not allowed.');
}

$input  = json_decode(file_get_contents('php://input'), true) ?? [];
$action = sanitize($input['action'] ?? '');

// ─────────────────────────────────────────────────────────────
//  LOGIN
// ─────────────────────────────────────────────────────────────
if ($action === 'login') {
    $email    = sanitize($input['email']    ?? '');
    $password = $input['password'] ?? '';

    if (!$email || !$password) {
        jsonResponse(false, [], 'Please fill in all fields.');
    }

    if (USE_FLAT_FILE) {
        // ── Flat-file path ──
        $users = loadUsers();

        // Seed demo accounts if file is empty
        if (empty($users)) {
            $users = seedDemoUsers();
            saveUsers($users);
        }

        $user = findUserByEmail($users, $email);
        if (!$user || !password_verify($password, $user['password'])) {
            jsonResponse(false, [], 'Invalid email or password.');
        }

    } else {
        // ── MySQL path ──
        require_once __DIR__ . '/db.php';
        $pdo  = getPDO();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            jsonResponse(false, [], 'Invalid email or password.');
        }

        // Map DB column names to app field names
        $user['firstName'] = $user['first_name'];
        $user['lastName']  = $user['last_name'];
    }

    $safeUser = buildSafeUser($user);
    $_SESSION['docvault_user'] = $safeUser;
    jsonResponse(true, ['user' => $safeUser]);
}

// ─────────────────────────────────────────────────────────────
//  SIGNUP
// ─────────────────────────────────────────────────────────────
if ($action === 'signup') {
    $firstName = sanitize($input['firstName'] ?? '');
    $lastName  = sanitize($input['lastName']  ?? '');
    $email     = sanitize($input['email']     ?? '');
    $password  = $input['password']  ?? '';
    $confirm   = $input['confirm']   ?? '';

    if (!$firstName || !$lastName || !$email || !$password || !$confirm) {
        jsonResponse(false, [], 'Please fill in all fields.');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonResponse(false, [], 'Invalid email address.');
    }
    if (strlen($password) < 8) {
        jsonResponse(false, [], 'Password must be at least 8 characters.');
    }
    if ($password !== $confirm) {
        jsonResponse(false, [], 'Passwords do not match.');
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    if (USE_FLAT_FILE) {
        // ── Flat-file path ──
        $users = loadUsers();
        if (empty($users)) { $users = seedDemoUsers(); }

        if (findUserByEmail($users, $email)) {
            jsonResponse(false, [], 'Email address already registered.');
        }

        $newUser = [
            'id'           => 'usr_' . bin2hex(random_bytes(6)),
            'email'        => $email,
            'username'     => $firstName . ' ' . $lastName,
            'firstName'    => $firstName,
            'lastName'     => $lastName,
            'password'     => $hash,
            'storageUsed'  => 0,
            'storageLimit' => MAX_STORAGE,
            'createdAt'    => date('c'),
            'documents'    => [],
        ];
        $users[] = $newUser;
        saveUsers($users);

    } else {
        // ── MySQL path ──
        require_once __DIR__ . '/db.php';
        $pdo = getPDO();

        $check = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $check->execute([$email]);
        if ($check->fetch()) {
            jsonResponse(false, [], 'Email address already registered.');
        }

        $insert = $pdo->prepare(
            'INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)'
        );
        $insert->execute([$firstName, $lastName, $email, $hash]);
        $newUser = [
            'id'           => 'usr_' . $pdo->lastInsertId(),
            'email'        => $email,
            'firstName'    => $firstName,
            'lastName'     => $lastName,
            'storageUsed'  => 0,
            'storageLimit' => MAX_STORAGE,
            'createdAt'    => date('c'),
            'documents'    => [],
        ];
    }

    $safeUser = buildSafeUser($newUser);
    $_SESSION['docvault_user'] = $safeUser;
    jsonResponse(true, ['user' => $safeUser]);
}

// ─────────────────────────────────────────────────────────────
//  Helpers
// ─────────────────────────────────────────────────────────────

function buildSafeUser(array $u): array {
    return [
        'id'           => $u['id']           ?? ('usr_' . bin2hex(random_bytes(4))),
        'email'        => $u['email'],
        'firstName'    => $u['firstName']    ?? explode(' ', $u['username'] ?? 'User')[0],
        'lastName'     => $u['lastName']     ?? (explode(' ', $u['username'] ?? 'User')[1] ?? ''),
        'storageUsed'  => $u['storageUsed']  ?? 0,
        'storageLimit' => $u['storageLimit'] ?? MAX_STORAGE,
        'createdAt'    => $u['createdAt']    ?? date('c'),
        'documents'    => $u['documents']    ?? [],
    ];
}

function seedDemoUsers(): array {
    return [
        [
            'id'           => 'usr_alex',
            'email'        => 'alex@docvault.io',
            'username'     => 'Alex Johnson',
            'firstName'    => 'Alex',
            'lastName'     => 'Johnson',
            'password'     => password_hash('password123', PASSWORD_DEFAULT),
            'storageUsed'  => 0,
            'storageLimit' => MAX_STORAGE,
            'createdAt'    => date('c'),
            'documents'    => [],
        ],
        [
            'id'           => 'usr_priya',
            'email'        => 'priya@docvault.io',
            'username'     => 'Priya Sharma',
            'firstName'    => 'Priya',
            'lastName'     => 'Sharma',
            'password'     => password_hash('securepass', PASSWORD_DEFAULT),
            'storageUsed'  => 0,
            'storageLimit' => MAX_STORAGE,
            'createdAt'    => date('c'),
            'documents'    => [],
        ],
    ];
}

// Unknown action
jsonResponse(false, [], 'Unknown action.');
