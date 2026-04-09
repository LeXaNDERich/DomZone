<?php
declare(strict_types=1);

require __DIR__ . '/../includes/layout.php';
require_once __DIR__ . '/../includes/admin.php';
require_once __DIR__ . '/../includes/repository.php';

requireAdmin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$categories = fetchCategories();

$product = null;
if ($id !== null && $id > 0) {
    $product = fetchProductById($id);
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) && (string) $_POST['id'] !== '' ? (int) $_POST['id'] : null;
    $categoryId = (int) ($_POST['category_id'] ?? 0);
    $name = trim((string) ($_POST['name'] ?? ''));
    $description = trim((string) ($_POST['description'] ?? ''));
    $imagePath = trim((string) ($_POST['image_path'] ?? ''));
    $price = (float) ($_POST['price'] ?? 0);
    $isFeatured = isset($_POST['is_featured']) && (string) $_POST['is_featured'] === '1';

    if ($categoryId <= 0 || $name === '' || $description === '' || $price <= 0) {
        $error = 'Заполните категорию, название, описание и цену.';
    } else {
        $newId = upsertProduct($id, $categoryId, $name, $description, $imagePath, $price, $isFeatured);
        $success = 'Сохранено.';
        header('Location: /admin/product_edit.php?id=' . $newId);
        exit;
    }
}

renderHeader($product ? 'Редактирование товара' : 'Добавление товара');
?>

<section class="section auth-wrap">
    <form class="card auth-form" method="post" action="/admin/product_edit.php<?= $product ? '?id=' . (int) $product['id'] : '' ?>">
        <h1><?= $product ? 'Редактирование' : 'Добавление' ?></h1>
        <?php if ($error !== ''): ?>
            <p class="alert error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <?php if ($success !== ''): ?>
            <p class="alert success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <input type="hidden" name="id" value="<?= htmlspecialchars((string) ($product['id'] ?? '')) ?>">

        <label>
            Категория
            <select name="category_id" required>
                <option value="">Выберите</option>
                <?php foreach ($categories as $c): ?>
                    <?php
                    $selected = (int) ($product['category_id'] ?? 0) === (int) $c['id'] ? 'selected' : '';
                    ?>
                    <option value="<?= (int) $c['id'] ?>" <?= $selected ?>>
                        <?= htmlspecialchars((string) $c['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            Название
            <input type="text" name="name" value="<?= htmlspecialchars((string) ($_POST['name'] ?? ($product['name'] ?? ''))) ?>" required>
        </label>

        <label>
            Описание
            <textarea name="description" rows="4" required><?= htmlspecialchars((string) ($_POST['description'] ?? ($product['description'] ?? ''))) ?></textarea>
        </label>

        <label>
            Путь к картинке (например `/assets/image/1.jpg`)
            <input type="text" name="image_path" value="<?= htmlspecialchars((string) ($_POST['image_path'] ?? ($product['image_path'] ?? ''))) ?>">
        </label>

        <label>
            Цена
            <input type="number" step="0.01" name="price" value="<?= htmlspecialchars((string) ($_POST['price'] ?? ($product['price'] ?? ''))) ?>" required>
        </label>

        <label>
            <input type="checkbox" name="is_featured" value="1" <?= !empty($product['is_featured']) ? 'checked' : '' ?>>
            Показывать на главной
        </label>

        <div class="admin-actions">
            <button type="submit" class="btn btn-primary">Сохранить</button>
            <a class="btn btn-ghost" href="/admin/products.php">Назад</a>
            <a class="btn btn-small" href="/admin/logout.php">Выйти</a>
        </div>
    </form>
</section>

<?php renderFooter(); ?>

