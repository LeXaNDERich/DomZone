<?php
declare(strict_types=1);

require __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/cart.php';
require_once __DIR__ . '/includes/repository.php';

$cartItems = getCartItems();
$productsById = fetchProductsByIds(array_keys($cartItems));
$user = currentUser();

$error = '';
$success = '';
$orderId = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $address = trim((string) ($_POST['address'] ?? ''));
    $comment = trim((string) ($_POST['comment'] ?? ''));

    if ($cartItems === [] || $productsById === []) {
        $error = 'Корзина пустая, добавьте товары перед оформлением.';
    } elseif ($name === '' || $phone === '' || $address === '') {
        $error = 'Заполните имя, телефон и адрес.';
    } else {
        $orderId = createOrder(
            $user !== null ? (int) $user['id'] : null,
            $name,
            $phone,
            $address,
            $comment,
            $cartItems,
            $productsById
        );
        clearCart();
        $success = 'Заказ оформлен! Номер заказа: #' . $orderId;
    }
}

$total = 0.0;
foreach ($cartItems as $productId => $qty) {
    if (!isset($productsById[$productId])) {
        continue;
    }
    $total += ((float) $productsById[$productId]['price']) * $qty;
}

renderHeader('Оформление заказа');
?>

<section class="section">
    <h1>Оформление заказа</h1>
    <?php if ($success !== ''): ?>
        <p class="alert success"><?= htmlspecialchars($success) ?></p>
        <a class="btn btn-primary" href="/catalog.php">Продолжить покупки</a>
    <?php else: ?>
        <?php if ($error !== ''): ?>
            <p class="alert error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <div class="checkout-grid">
            <form class="card contact-form" method="post" action="/checkout.php">
                <h3>Данные покупателя</h3>
                <label>
                    Имя
                    <input type="text" name="name" value="<?= htmlspecialchars((string) ($_POST['name'] ?? ($user['name'] ?? ''))) ?>" required>
                </label>
                <label>
                    Телефон
                    <input type="tel" name="phone" value="<?= htmlspecialchars((string) ($_POST['phone'] ?? '')) ?>" required>
                </label>
                <label>
                    Адрес доставки
                    <input type="text" name="address" value="<?= htmlspecialchars((string) ($_POST['address'] ?? '')) ?>" required>
                </label>
                <label>
                    Комментарий
                    <textarea name="comment" rows="3"><?= htmlspecialchars((string) ($_POST['comment'] ?? '')) ?></textarea>
                </label>
                <button type="submit" class="btn btn-primary">Подтвердить заказ</button>
            </form>
            <article class="card">
                <h3>Ваш заказ</h3>
                <?php foreach ($cartItems as $productId => $qty): ?>
                    <?php if (!isset($productsById[$productId])) { continue; } ?>
                    <p>
                        <?= htmlspecialchars((string) $productsById[$productId]['name']) ?> x <?= (int) $qty ?>
                    </p>
                <?php endforeach; ?>
                <hr>
                <p><strong>Итого: <?= number_format($total, 0, '.', ' ') ?> ₽</strong></p>
            </article>
        </div>
    <?php endif; ?>
</section>

<?php renderFooter(); ?>
