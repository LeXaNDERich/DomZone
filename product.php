<?php
declare(strict_types=1);

require __DIR__ . '/includes/layout.php';
require __DIR__ . '/includes/repository.php';

$productId = (int) ($_GET['id'] ?? 0);
$product = fetchProductById($productId);

if ($product === null) {
    http_response_code(404);
    renderHeader('Товар не найден');
    ?>
    <section class="section">
        <h1>Товар не найден</h1>
        <p class="lead">Проверьте ссылку или вернитесь в каталог.</p>
        <a class="btn btn-primary" href="/catalog.php">Перейти в каталог</a>
    </section>
    <?php
    renderFooter();
    exit;
}

renderHeader((string) $product['name']);
?>

<section class="section product-page">
    <div class="card">
        <?php if (!empty($product['image_path'])): ?>
            <img class="product-hero" src="<?= htmlspecialchars((string) $product['image_path']) ?>" alt="<?= htmlspecialchars((string) $product['name']) ?>">
        <?php else: ?>
            <div class="product-hero placeholder">Фото товара</div>
        <?php endif; ?>
    </div>
    <div class="card">
        <span class="chip"><?= htmlspecialchars((string) $product['category']) ?></span>
        <h1><?= htmlspecialchars((string) $product['name']) ?></h1>
        <p class="lead"><?= htmlspecialchars((string) $product['description']) ?></p>
        <p class="price-lg"><?= number_format((float) $product['price'], 0, '.', ' ') ?> ₽</p>
        <form class="buy-form" method="post" action="/add_to_cart.php">
            <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
            <input type="hidden" name="redirect" value="/product.php?id=<?= (int) $product['id'] ?>">
            <label>
                Количество
                <input type="number" name="quantity" min="1" max="99" value="1">
            </label>
            <button type="submit" class="btn btn-primary">Добавить в корзину</button>
        </form>
    </div>
</section>

<?php renderFooter(); ?>
