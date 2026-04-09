<?php
declare(strict_types=1);

require __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/cart.php';
require_once __DIR__ . '/includes/repository.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = (int) ($_POST['product_id'] ?? 0);
    $quantity = (int) ($_POST['quantity'] ?? 0);
    updateCartItem($productId, $quantity);
    header('Location: /cart.php');
    exit;
}

$cartItems = getCartItems();
$productsById = fetchProductsByIds(array_keys($cartItems));

$rows = [];
$total = 0.0;
foreach ($cartItems as $productId => $qty) {
    if (!isset($productsById[$productId])) {
        continue;
    }
    $product = $productsById[$productId];
    $lineTotal = ((float) $product['price']) * $qty;
    $total += $lineTotal;
    $rows[] = [
        'product' => $product,
        'qty' => $qty,
        'line_total' => $lineTotal,
    ];
}

renderHeader('Корзина');
?>

<section class="section">
    <h1>Корзина</h1>
    <?php if ($rows === []): ?>
        <p class="lead">Корзина пока пустая. Добавьте товары из каталога.</p>
        <a class="btn btn-primary" href="/catalog.php">Перейти в каталог</a>
    <?php else: ?>
        <div class="cart-list">
            <?php foreach ($rows as $row): ?>
                <article class="card cart-item">
                    <a class="cart-item-link" href="/product.php?id=<?= (int) $row['product']['id'] ?>">
                        <?= htmlspecialchars((string) $row['product']['name']) ?>
                    </a>
                    <div><?= number_format((float) $row['product']['price'], 0, '.', ' ') ?> ₽</div>
                    <form method="post" action="/cart.php" class="cart-qty">
                        <input type="hidden" name="product_id" value="<?= (int) $row['product']['id'] ?>">
                        <input type="number" name="quantity" min="0" max="99" value="<?= (int) $row['qty'] ?>">
                        <button type="submit" class="btn btn-small">Обновить</button>
                    </form>
                    <strong><?= number_format((float) $row['line_total'], 0, '.', ' ') ?> ₽</strong>
                </article>
            <?php endforeach; ?>
        </div>
        <div class="card cart-summary">
            <h3>Итого: <?= number_format($total, 0, '.', ' ') ?> ₽</h3>
            <a class="btn btn-primary" href="/checkout.php">Оформить заказ</a>
        </div>
    <?php endif; ?>
</section>

<?php renderFooter(); ?>
