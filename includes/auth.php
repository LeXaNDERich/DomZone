<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

function ensureSessionStarted(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function currentUser(): ?array
{
    ensureSessionStarted();

    if (!isset($_SESSION['user']) || !is_array($_SESSION['user'])) {
        return null;
    }

    return $_SESSION['user'];
}

function isGuest(): bool
{
    return currentUser() === null;
}

function registerUser(string $name, string $email, string $password): array
{
    $normalizedEmail = mb_strtolower(trim($email));
    $normalizedName = trim($name);

    if ($normalizedName === '' || $normalizedEmail === '' || $password === '') {
        return ['ok' => false, 'message' => 'Заполните все поля.'];
    }
    if (!filter_var($normalizedEmail, FILTER_VALIDATE_EMAIL)) {
        return ['ok' => false, 'message' => 'Введите корректный email.'];
    }
    if (mb_strlen($password) < 6) {
        return ['ok' => false, 'message' => 'Пароль должен быть не короче 6 символов.'];
    }

    $pdo = getPdo();
    $check = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $check->execute(['email' => $normalizedEmail]);
    if ($check->fetch()) {
        return ['ok' => false, 'message' => 'Пользователь с таким email уже зарегистрирован.'];
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare(
        'INSERT INTO users (name, email, password_hash)
         VALUES (:name, :email, :password_hash)'
    );
    $stmt->execute([
        'name' => $normalizedName,
        'email' => $normalizedEmail,
        'password_hash' => $passwordHash,
    ]);

    $userId = (int) $pdo->lastInsertId();
    loginById($userId);

    return ['ok' => true, 'message' => 'Регистрация прошла успешно.'];
}

function loginUser(string $email, string $password): array
{
    $normalizedEmail = mb_strtolower(trim($email));

    if ($normalizedEmail === '' || $password === '') {
        return ['ok' => false, 'message' => 'Введите email и пароль.'];
    }

    $stmt = getPdo()->prepare(
        'SELECT id, name, email, password_hash
         FROM users
         WHERE email = :email
         LIMIT 1'
    );
    $stmt->execute(['email' => $normalizedEmail]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, (string) $user['password_hash'])) {
        return ['ok' => false, 'message' => 'Неверный email или пароль.'];
    }

    ensureSessionStarted();
    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'name' => (string) $user['name'],
        'email' => (string) $user['email'],
    ];

    return ['ok' => true, 'message' => 'Вы успешно вошли в аккаунт.'];
}

function loginById(int $userId): void
{
    $stmt = getPdo()->prepare('SELECT id, name, email FROM users WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch();

    if (!$user) {
        return;
    }

    ensureSessionStarted();
    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'name' => (string) $user['name'],
        'email' => (string) $user['email'],
    ];
}

function logoutUser(): void
{
    ensureSessionStarted();
    unset($_SESSION['user']);
}
