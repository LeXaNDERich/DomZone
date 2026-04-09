<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/sql_export.php';

function fetchCategories(): array
{
    $stmt = getPdo()->query('SELECT id, name, slug FROM categories ORDER BY name');
    return $stmt->fetchAll();
}

function fetchFeaturedProducts(int $limit = 4): array
{
    $stmt = getPdo()->prepare(
        'SELECT p.id, p.name, p.description, p.image_path, p.price, c.name AS category
         FROM products p
         INNER JOIN categories c ON c.id = p.category_id
         WHERE p.is_featured = 1
         ORDER BY p.created_at DESC
         LIMIT :limit'
    );
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function fetchProducts(?string $categorySlug = null): array
{
    if ($categorySlug === null || $categorySlug === '') {
        $stmt = getPdo()->query(
            'SELECT p.id, p.name, p.description, p.image_path, p.price, c.name AS category, c.slug AS category_slug
             FROM products p
             INNER JOIN categories c ON c.id = p.category_id
             ORDER BY p.created_at DESC'
        );
        return $stmt->fetchAll();
    }

    $stmt = getPdo()->prepare(
        'SELECT p.id, p.name, p.description, p.image_path, p.price, c.name AS category, c.slug AS category_slug
         FROM products p
         INNER JOIN categories c ON c.id = p.category_id
         WHERE c.slug = :slug
         ORDER BY p.created_at DESC'
    );
    $stmt->execute(['slug' => $categorySlug]);

    return $stmt->fetchAll();
}

function fetchProductById(int $id): ?array
{
    $stmt = getPdo()->prepare(
        'SELECT p.id, p.category_id, p.name, p.description, p.image_path, p.price, p.is_featured, c.name AS category, c.slug AS category_slug
         FROM products p
         INNER JOIN categories c ON c.id = p.category_id
         WHERE p.id = :id
         LIMIT 1'
    );
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function fetchProductsByIds(array $ids): array
{
    if ($ids === []) {
        return [];
    }

    $ids = array_values(array_unique(array_map('intval', $ids)));
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = getPdo()->prepare(
        "SELECT p.id, p.name, p.description, p.image_path, p.price, c.name AS category
         FROM products p
         INNER JOIN categories c ON c.id = p.category_id
         WHERE p.id IN ({$placeholders})"
    );
    $stmt->execute($ids);

    $rows = $stmt->fetchAll();
    $indexed = [];
    foreach ($rows as $row) {
        $indexed[(int) $row['id']] = $row;
    }
    return $indexed;
}

function createOrder(?int $userId, string $name, string $phone, string $address, string $comment, array $cartItems, array $productsById): int
{
    $pdo = getPdo();
    $pdo->beginTransaction();

    try {
        $total = 0.0;
        foreach ($cartItems as $productId => $qty) {
            if (!isset($productsById[$productId])) {
                continue;
            }
            $total += ((float) $productsById[$productId]['price']) * (int) $qty;
        }

        $stmt = $pdo->prepare(
            'INSERT INTO orders (user_id, customer_name, phone, address, comment, total_amount, status)
             VALUES (:user_id, :customer_name, :phone, :address, :comment, :total_amount, :status)'
        );
        $stmt->execute([
            'user_id' => $userId,
            'customer_name' => $name,
            'phone' => $phone,
            'address' => $address,
            'comment' => $comment !== '' ? $comment : null,
            'total_amount' => $total,
            'status' => 'new',
        ]);

        $orderId = (int) $pdo->lastInsertId();
        $itemStmt = $pdo->prepare(
            'INSERT INTO order_items (order_id, product_id, quantity, price)
             VALUES (:order_id, :product_id, :quantity, :price)'
        );
        foreach ($cartItems as $productId => $qty) {
            if (!isset($productsById[$productId])) {
                continue;
            }
            $itemStmt->execute([
                'order_id' => $orderId,
                'product_id' => (int) $productId,
                'quantity' => (int) $qty,
                'price' => (float) $productsById[$productId]['price'],
            ]);
        }

        $pdo->commit();
        return $orderId;
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function saveContactRequest(string $name, string $phone, string $message): void
{
    $stmt = getPdo()->prepare(
        'INSERT INTO contact_requests (name, phone, message)
         VALUES (:name, :phone, :message)'
    );

    $stmt->execute([
        'name' => $name,
        'phone' => $phone,
        'message' => $message,
    ]);
}

function fetchOrders(int $limit = 100): array
{
    $stmt = getPdo()->prepare(
        'SELECT id, user_id, customer_name, phone, address, total_amount, status, created_at
         FROM orders
         ORDER BY created_at DESC
         LIMIT :limit'
    );
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function fetchOrderItems(int $orderId): array
{
    $stmt = getPdo()->prepare(
        'SELECT oi.quantity, oi.price, p.name
         FROM order_items oi
         INNER JOIN products p ON p.id = oi.product_id
         WHERE oi.order_id = :order_id
         ORDER BY oi.id ASC'
    );
    $stmt->execute(['order_id' => $orderId]);
    return $stmt->fetchAll();
}

function fetchProductsAdmin(): array
{
    $stmt = getPdo()->query(
        'SELECT p.id, p.name, p.price, p.image_path, c.name AS category
         FROM products p
         INNER JOIN categories c ON c.id = p.category_id
         ORDER BY p.created_at DESC'
    );
    return $stmt->fetchAll();
}

function upsertProduct(?int $id, int $categoryId, string $name, string $description, string $imagePath, float $price, bool $isFeatured): int
{
    $pdo = getPdo();
    if ($id === null) {
        $stmt = $pdo->prepare(
            'INSERT INTO products (category_id, name, description, image_path, price, is_featured)
             VALUES (:category_id, :name, :description, :image_path, :price, :is_featured)'
        );
        $stmt->execute([
            'category_id' => $categoryId,
            'name' => $name,
            'description' => $description,
            'image_path' => $imagePath !== '' ? $imagePath : null,
            'price' => $price,
            'is_featured' => $isFeatured ? 1 : 0,
        ]);
        $newId = (int) $pdo->lastInsertId();
        appendAdminSql(
            'INSERT INTO `products` (`id`,`category_id`,`name`,`description`,`image_path`,`price`,`is_featured`) VALUES (' .
            $newId . ',' . (int) $categoryId . ',' . q($name) . ',' . q($description) . ',' . q($imagePath !== '' ? $imagePath : null) . ',' .
            number_format($price, 2, '.', '') . ',' . ($isFeatured ? '1' : '0') .
            ');'
        );
        return $newId;
    }

    $stmt = $pdo->prepare(
        'UPDATE products
         SET category_id = :category_id,
             name = :name,
             description = :description,
             image_path = :image_path,
             price = :price,
             is_featured = :is_featured
         WHERE id = :id'
    );
    $stmt->execute([
        'id' => $id,
        'category_id' => $categoryId,
        'name' => $name,
        'description' => $description,
        'image_path' => $imagePath !== '' ? $imagePath : null,
        'price' => $price,
        'is_featured' => $isFeatured ? 1 : 0,
    ]);

    appendAdminSql(
        'UPDATE `products` SET ' .
        '`category_id`=' . (int) $categoryId . ',' .
        '`name`=' . q($name) . ',' .
        '`description`=' . q($description) . ',' .
        '`image_path`=' . q($imagePath !== '' ? $imagePath : null) . ',' .
        '`price`=' . number_format($price, 2, '.', '') . ',' .
        '`is_featured`=' . ($isFeatured ? '1' : '0') .
        ' WHERE `id`=' . (int) $id . ';'
    );

    return $id;
}

function deleteProduct(int $id): void
{
    $stmt = getPdo()->prepare('DELETE FROM products WHERE id = :id');
    $stmt->execute(['id' => $id]);

    appendAdminSql('DELETE FROM `products` WHERE `id`=' . (int) $id . ';');
}
