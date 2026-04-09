<?php
declare(strict_types=1);

require __DIR__ . '/../includes/layout.php';
require_once __DIR__ . '/../includes/admin.php';
require_once __DIR__ . '/../includes/repository.php';

requireAdmin();

$orderId = (int) ($_GET['id'] ?? 0);
if ($orderId <= 0) {
    header('Location: /admin/index.php');
    exit;
}

$items = fetchOrderItems($orderId);

renderHeader('Заказ #' . $orderId);
?>

<section class="section">
    <div class="admin-top">
        <h1>Заказ #<?= (int) $orderId ?></h1>
        <div class="admin-actions">
            <a class="btn btn-ghost" href="/admin/index.php">Назад</a>
            <a class="btn btn-small" href="/admin/logout.php">Выйти</a>
        </div>
    </div>

    <div class="card">
        <h3>Состав заказа</h3>
        <?php if ($items === []): ?>
            <p class="lead">Позиции не найдены.</p>
        <?php else: ?>
            <?php foreach ($items as $it): ?>
                <p>
                    <?= htmlspecialchars((string) $it['name']) ?> — <?= (int) $it['quantity'] ?> шт. × <?= number_format((float) $it['price'], 0, '.', ' ') ?> ₽
                </p>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php renderFooter(); ?>

