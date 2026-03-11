<?php
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getCurrentUser() {
    return isset($_SESSION['user_id']) ? $_SESSION : null;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: " . BASE_URL . "View/Auth/User/login.php");
        exit();
    }
}

/**
 * Redirect a logged-in user to their role dashboard.
 * Use this at the top of public/guest pages (home, login pages).
 */
function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        header("Location: " . getDashboardUrl());
        exit();
    }
}

/**
 * Enforce that the current user is logged in AND belongs to the allowed role(s).
 * - Not logged in  → redirect to login page
 * - Wrong role     → redirect to their own dashboard
 *
 * @param string|array $roles  One role string or an array of allowed roles.
 */
function requireRole($roles) {
    require_once __DIR__ . '/constants.php';

    // Must be logged in first
    if (!isLoggedIn()) {
        header("Location: " . BASE_URL . "View/Auth/User/login.php");
        exit();
    }

    $allowed = is_array($roles) ? $roles : [$roles];
    $current = $_SESSION['role'] ?? '';

    if (!in_array($current, $allowed)) {
        // Wrong role — send them to their own dashboard
        header("Location: " . getDashboardUrl());
        exit();
    }
}

/**
 * Require that the current owner's business application is Approved.
 * If not, redirect them back to their dashboard where the status card is shown.
 * Must be called AFTER requireRole('owner').
 */
function requireApprovedOwner() {
    requireRole('owner');
    require_once __DIR__ . '/constants.php';
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        header("Location: " . BASE_URL . "View/Owner/Dashboard/dashboard.php");
        exit();
    }
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER, DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        $stmt = $pdo->prepare(
            "SELECT status FROM business_applications WHERE user_id = :uid ORDER BY created_at DESC LIMIT 1"
        );
        $stmt->execute([':uid' => $userId]);
        $app = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$app || $app['status'] !== 'Approved') {
            header("Location: " . BASE_URL . "View/Owner/Dashboard/dashboard.php");
            exit();
        }
    } catch (Exception $e) {
        // DB check failed — do not block, fail open gracefully
    }
}

function getDashboardUrl() {
    if (!isset($_SESSION['role'])) {
        return BASE_URL . "View/User/Home/index.php";
    }

    switch ($_SESSION['role']) {
        case 'admin':
            return BASE_URL . "View/Admin/Dashboard/dashboard.php";
        case 'owner':
            return BASE_URL . "View/Owner/Dashboard/dashboard.php";
        case 'technician':
            return BASE_URL . "View/Technician/Dashboard/index.php";
        default:
            return BASE_URL . "View/User/Dashboard/index.php";
    }
}
