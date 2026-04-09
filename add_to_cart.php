<?php
declare(strict_types=1);

require __DIR__ . '/includes/cart.php';
require __DIR__ . '/includes/repository.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /catalog.php');
    exit;
}

$productId = (int) ($_POST['product_id'] ?? 0);
$quantity = max(1, (int) ($_POST['quantity'] ?? 1));
$redirect = trim((string) ($_POST['redirect'] ?? '/catalog.php'));

if ($redirect === '' || strpos($redirect, 'http') === 0) {
    $redirect = '/catalog.php';
}

$product = fetchProductById($productId);
if ($product !== null) {
    addToCart($productId, $quantity);
}

header('Location: ' . $redirect);
exit;
