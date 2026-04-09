<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

function getCartItems(): array
{
    ensureSessionStarted();
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $items = [];
    foreach ($_SESSION['cart'] as $productId => $qty) {
        $id = (int) $productId;
        $count = (int) $qty;
        if ($id > 0 && $count > 0) {
            $items[$id] = $count;
        }
    }

    $_SESSION['cart'] = $items;
    return $items;
}

function getCartCount(): int
{
    return array_sum(getCartItems());
}

function addToCart(int $productId, int $quantity = 1): void
{
    if ($productId <= 0 || $quantity <= 0) {
        return;
    }
    $cart = getCartItems();
    $cart[$productId] = ($cart[$productId] ?? 0) + $quantity;
    $_SESSION['cart'] = $cart;
}

function updateCartItem(int $productId, int $quantity): void
{
    $cart = getCartItems();
    if ($quantity <= 0) {
        unset($cart[$productId]);
    } else {
        $cart[$productId] = $quantity;
    }
    $_SESSION['cart'] = $cart;
}

function clearCart(): void
{
    ensureSessionStarted();
    $_SESSION['cart'] = [];
}
