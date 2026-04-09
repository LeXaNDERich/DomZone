<?php
declare(strict_types=1);

require __DIR__ . '/../includes/layout.php';
require_once __DIR__ . '/../includes/admin.php';
require_once __DIR__ . '/../includes/repository.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deleteId = (int) ($_POST['delete_id'] ?? 0);
    if ($deleteId > 0) {
        deleteProduct($deleteId);
    }
    header('Location: /admin/products.php');
    exit;
}

$products = fetchProductsAdmin();

renderHeader('Товары');
?>

<section class="section">
    <div class="admin-top">
        <h1>Товары</h1>
        <div class="admin-actions">
            <a class="btn btn-primary" href="/admin/product_edit.php">Добавить</a>
            <a class="btn btn-ghost" href="/admin/index.php">Заказы</a>
            <a class="btn btn-small" href="/admin/logout.php">Выйти</a>
        </div>
    </div>

    <div class="admin-table">
        <div class="admin-row admin-head">
            <div>ID</div>
            <div>Название</div>
            <div>Категория</div>
            <div>Цена</div>
            <div></div>
        </div>
        <?php foreach ($products as $p): ?>
            <div class="admin-row">
                <div>#<?= (int) $p['id'] ?></div>
                <div><?= htmlspecialchars((string) $p['name']) ?></div>
                <div><?= htmlspecialchars((string) $p['category']) ?></div>
                <div><?= number_format((float) $p['price'], 0, '.', ' ') ?> ₽</div>
                <div class="admin-actions-inline">
                    <a href="/admin/product_edit.php?id=<?= (int) $p['id'] ?>">Изменить</a>
                    <form method="post" action="/admin/products.php" onsubmit="return confirm('Удалить товар?');">
                        <input type="hidden" name="delete_id" value="<?= (int) $p['id'] ?>">
                        <button type="submit" class="link-danger">Удалить</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php renderFooter(); ?>

