<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/cart.php';
require_once __DIR__ . '/admin.php';

function renderHeader(string $title): void
{
    $menu = [
        'Главная' => '/index.php',
        'Каталог' => '/catalog.php',
        'О нас' => '/about.php',
        'Контакты' => '/contact.php',
    ];
    $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $user = currentUser();
    $cartCount = getCartCount();
    $admin = isAdmin();
    ?>
    <!doctype html>
    <html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= htmlspecialchars($title) ?> | DomZone</title>
        <link rel="stylesheet" href="/assets/css/style.css">
    </head>
    <body>
    <header class="site-header">
        <div class="container nav-wrap">
            <a class="brand" href="/index.php">DomZone</a>
            <nav class="nav">
                <?php foreach ($menu as $label => $url): ?>
                    <a class="<?= $currentPath === $url ? 'active' : '' ?>" href="<?= $url ?>">
                        <?= htmlspecialchars($label) ?>
                    </a>
                <?php endforeach; ?>
                <a class="<?= $currentPath === '/cart.php' ? 'active' : '' ?>" href="/cart.php">
                    Корзина (<?= $cartCount ?>)
                </a>
                <?php if ($admin): ?>
                    <a class="<?= strpos($currentPath, '/admin') === 0 ? 'active' : '' ?>" href="/admin/index.php">Админ</a>
                <?php endif; ?>
                <?php if ($user === null): ?>
                    <a class="<?= $currentPath === '/login.php' ? 'active' : '' ?>" href="/login.php">Вход</a>
                    <a class="<?= $currentPath === '/register.php' ? 'active' : '' ?>" href="/register.php">Регистрация</a>
                <?php else: ?>
                    <span class="nav-user"><?= htmlspecialchars((string) $user['name']) ?></span>
                    <a href="/logout.php">Выход</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <main class="container">
    <?php
}

function renderFooter(): void
{
    ?>
    </main>
    <footer class="site-footer">
        <div class="container footer-wrap">
            <p>© <?= date('Y') ?> DomZone. Хозтовары для дома и ремонта.</p>
            <p>Работаем ежедневно: 09:00-21:00</p>
        </div>
    </footer>
    </body>
    </html>
    <?php
}
