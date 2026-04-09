<?php
declare(strict_types=1);

require __DIR__ . '/includes/layout.php';
require __DIR__ . '/includes/repository.php';

$selectedCategory = isset($_GET['category']) ? trim((string) $_GET['category']) : '';
$categories = fetchCategories();
$filtered = fetchProducts($selectedCategory !== '' ? $selectedCategory : null);

renderHeader('Каталог');
?>

<section class="section">
    <h1>Каталог хозтоваров</h1>
    <p class="lead">Выберите категорию и найдите нужный товар за пару минут.</p>

    <div class="filters">
        <a class="<?= $selectedCategory === '' ? 'active' : '' ?>" href="/catalog.php">Все</a>
        <?php foreach ($categories as $category): ?>
            <a class="<?= $selectedCategory === $category['slug'] ? 'active' : '' ?>"
               href="/catalog.php?category=<?= urlencode((string) $category['slug']) ?>">
                <?= htmlspecialchars((string) $category['name']) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="cards">
        <?php if ($filtered === []): ?>
            <p>Товары пока не добавлены в эту категорию.</p>
        <?php endif; ?>
        <?php foreach ($filtered as $item): ?>
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
                        <input type="hidden" name="redirect" value="<?= htmlspecialchars('/catalog.php' . ($selectedCategory !== '' ? '?category=' . urlencode($selectedCategory) : '')) ?>">
                        <button type="submit" class="btn btn-small">В корзину</button>
                    </form>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<?php renderFooter(); ?>
