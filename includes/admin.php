<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

const ADMIN_EMAIL = 'kosarevlexa@gmail.com';

function normalizeEmail(string $email): string
{
    return mb_strtolower(trim($email));
}

function isAdmin(): bool
{
    $user = currentUser();
    if ($user === null) {
        return false;
    }
    return normalizeEmail((string) ($user['email'] ?? '')) === normalizeEmail(ADMIN_EMAIL);
}

function requireAdmin(): void
{
    if (!isAdmin()) {
        header('Location: /login.php');
        exit;
    }
}

