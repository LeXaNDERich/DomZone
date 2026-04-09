<?php
declare(strict_types=1);

require __DIR__ . '/../includes/layout.php';
require_once __DIR__ . '/../includes/admin.php';
require_once __DIR__ . '/../includes/repository.php';

requireAdmin();

$orders = fetchOrders(200);

renderHeader('Админ-панель');
?>

<section class="section">
    <div class="admin-top">
        <h1>Админ-панель</h1>
        <div class="admin-actions">
            <a class="btn btn-ghost" href="/admin/products.php">Товары</a>
            <a class="btn btn-small" href="/admin/logout.php">Выйти</a>
        </div>
    </div>

    <h2>Заказы</h2>
    <?php if ($orders === []): ?>
        <p class="lead">Пока нет заказов.</p>
    <?php else: ?>
        <div class="admin-table">
            <div class="admin-row admin-head">
                <div>ID</div>
                <div>Клиент</div>
                <div>Телефон</div>
                <div>Сумма</div>
                <div>Статус</div>
                <div>Дата</div>
                <div></div>
            </div>
            <?php foreach ($orders as $o): ?>
                <div class="admin-row">
                    <div>#<?= (int) $o['id'] ?></div>
                    <div><?= htmlspecialchars((string) $o['customer_name']) ?></div>
                    <div><?= htmlspecialchars((string) $o['phone']) ?></div>
                    <div><?= number_format((float) $o['total_amount'], 0, '.', ' ') ?> ₽</div>
                    <div><?= htmlspecialchars((string) $o['status']) ?></div>
                    <div><?= htmlspecialchars((string) $o['created_at']) ?></div>
                    <div><a href="/admin/order.php?id=<?= (int) $o['id'] ?>">Открыть</a></div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php renderFooter(); ?>

