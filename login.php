<?php
declare(strict_types=1);

require __DIR__ . '/includes/layout.php';

if (!isGuest()) {
    header('Location: /index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    $result = loginUser($email, $password);
    if ($result['ok'] === true) {
        header('Location: /index.php');
        exit;
    }
    $error = (string) $result['message'];
}

renderHeader('Вход');
?>

<section class="section auth-wrap">
    <form class="card auth-form" method="post" action="/login.php">
        <h1>Вход в аккаунт</h1>
        <p class="lead">Введите email и пароль, чтобы продолжить.</p>

        <?php if ($error !== ''): ?>
            <p class="alert error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <label>
            Email
            <input type="email" name="email" value="<?= htmlspecialchars((string) ($_POST['email'] ?? '')) ?>" required>
        </label>
        <label>
            Пароль
            <input type="password" name="password" required>
        </label>

        <button type="submit" class="btn btn-primary">Войти</button>
        <p class="muted-text">Нет аккаунта? <a href="/register.php">Зарегистрироваться</a></p>
    </form>
</section>

<?php renderFooter(); ?>
