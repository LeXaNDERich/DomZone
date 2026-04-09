<?php
declare(strict_types=1);

require __DIR__ . '/includes/layout.php';

if (!isGuest()) {
    header('Location: /index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    $result = registerUser($name, $email, $password);
    if ($result['ok'] === true) {
        $success = (string) $result['message'];
        header('Refresh: 1; url=/index.php');
    } else {
        $error = (string) $result['message'];
    }
}

renderHeader('Регистрация');
?>

<section class="section auth-wrap">
    <form class="card auth-form" method="post" action="/register.php">
        <h1>Регистрация</h1>
        <p class="lead">Создайте аккаунт, чтобы быстрее оформлять заказы.</p>

        <?php if ($success !== ''): ?>
            <p class="alert success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>
        <?php if ($error !== ''): ?>
            <p class="alert error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <label>
            Имя
            <input type="text" name="name" value="<?= htmlspecialchars((string) ($_POST['name'] ?? '')) ?>" required>
        </label>
        <label>
            Email
            <input type="email" name="email" value="<?= htmlspecialchars((string) ($_POST['email'] ?? '')) ?>" required>
        </label>
        <label>
            Пароль
            <input type="password" name="password" minlength="6" required>
        </label>

        <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
        <p class="muted-text">Уже есть аккаунт? <a href="/login.php">Войти</a></p>
    </form>
</section>

<?php renderFooter(); ?>
