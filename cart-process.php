<?php
require_once 'helpers/functions.php';
require_once 'vendor/autoload.php';

use Aries\MiniFrameworkStore\Models\Product;
use Aries\MiniFrameworkStore\Models\Cart;

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$userId = getCurrentUserId();
$action = $_POST['action'] ?? '';
$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
$redirect = $_POST['redirect'] ?? 'index.php';

// Validate product
$product = new Product();
$productDetails = $product->getById($productId);

if (!$productDetails) {
    $_SESSION['error'] = 'Product not found';
    header('Location: ' . $redirect);
    exit;
}

$cart = new Cart();

switch ($action) {
    case 'add':
        if ($quantity < 1) {
            $_SESSION['error'] = 'Invalid quantity';
            break;
        }
        $cart->addToCart($userId, $productId, $quantity);
        $_SESSION['success'] = 'Product added to cart successfully';
        break;

    case 'remove':
        $cart->removeFromCart($userId, $productId);
        $_SESSION['success'] = 'Product removed from cart';
        break;

    case 'update':
        if ($quantity < 1) {
            $_SESSION['error'] = 'Invalid quantity';
            break;
        }
        $cart->updateQuantity($userId, $productId, $quantity);
        $_SESSION['success'] = 'Cart updated successfully';
        break;

    case 'clear':
        $cart->clearCart($userId);
        $_SESSION['success'] = 'Cart cleared successfully';
        break;

    default:
        $_SESSION['error'] = 'Invalid action';
        break;
}

header('Location: ' . $redirect);
exit;