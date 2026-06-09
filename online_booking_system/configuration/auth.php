<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_id'], $_SESSION['role']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: ' . app_path('/auth/login.php'));
        exit;
    }
}

function require_role(string $role): void
{
    require_login();

    if ($_SESSION['role'] !== $role) {
        redirect_by_role();
    }
}

function redirect_by_role(): void
{
    if (($_SESSION['role'] ?? '') === 'admin') {
        header('Location: ' . app_path('/admin/bookings.php'));
        exit;
    }

    if (($_SESSION['role'] ?? '') === 'passenger') {
        header('Location: ' . app_path('/passenger/book_ticket.php'));
        exit;
    }

    header('Location: ' . app_path('/auth/login.php'));
    exit;
}

function app_path(string $path): string
{
    return '/online_booking_system' . $path;
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
