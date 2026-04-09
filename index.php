<?php
declare(strict_types=1);

require __DIR__ . '/includes/layout.php';
require __DIR__ . '/includes/repository.php';

$products = fetchFeaturedProducts(4);

renderHeader('Хозтовары для дома');
?>

<section class="hero">
    <div>
        <p class="badge">Новый сезон скидок до 25%</p>
        <h1>Все для дома, сада и ремонта в одном месте</h1>
        <p class="lead">
            Подберите качественные хозтовары для ежедневных задач: уборка, хранение, инструменты и полезные мелочи.
        </p>
        <div class="hero-actions">
            <a class="btn btn-primary" href="/catalog.php">Смотреть каталог</a>
            <a class="btn btn-ghost" href="/contact.php">Связаться с нами</a>
        </div>
    </div>
    <div class="hero-card">
        <h3>Почему выбирают DomZone</h3>
        <ul>
            <li>Быстрая доставка по городу</li>
            <li>Только проверенные бренды</li>
            <li>Помощь в подборе товаров</li>
        </ul>
    </div>
</section>

<section class="section">
    <h2>Популярные товары</h2>
    <div class="cards">
        <?php foreach ($products as $item): ?>
            <article class="card product-card">
                <a class="card-stretch-link" href="/product.php?id=<?= (int) $item['id'] ?>" aria-label="<?= htmlspecialchars((string) $item['name']) ?>"></a>
                <?php if (!empty($item['image_path'])): ?>
                    <img class="product-thumb"
                         src="<?= htmlspecialchars((string) $item['image_path']) ?>"
                         alt="<?= htmlspecialchars((string) $item['name']) ?>">
                <?php else: ?>
                    <div class="product-thumb placeholder">Фото товара</div>
                <?php endif; ?>
                <span class="chip"><?= htmlspecialchars($item['category']) ?></span>
                <h3><?= htmlspecialchars($item['name']) ?></h3>
                <p><?= htmlspecialchars($item['description']) ?></p>
                <div class="card-bottom">
                    <strong><?= number_format((float) $item['price'], 0, '.', ' ') ?> ₽</strong>
                    <form method="post" action="/add_to_cart.php" class="inline-form">
                        <input type="hidden" name="product_id" value="<?= (int) $item['id'] ?>">
                        <input type="hidden" name="quantity" value="1">
                        <input type="hidden" name="redirect" value="/index.php">
                        <button type="submit" class="btn btn-small">В корзину</button>
                    </form>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<?php renderFooter(); ?>
