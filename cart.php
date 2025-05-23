<?php 
require_once 'helpers/functions.php';
require_once 'vendor/autoload.php';

use Aries\MiniFrameworkStore\Models\Cart;

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$userId = getCurrentUserId();
$cart = new Cart();

if(isset($_GET['remove'])) {
    $productId = $_GET['remove'];
    $cart->removeFromCart($userId, $productId);
    echo "<script>alert('Product removed from cart');</script>";
}

$amounLocale = 'en_PH';
$pesoFormatter = new NumberFormatter($amounLocale, NumberFormatter::CURRENCY);

template('header.php');
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <div class="cart-header d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Shopping Cart</h1>
                <a href="index.php" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-2"></i>Continue Shopping
                </a>
            </div>

            <?php 
            $cartItems = $cart->getCartItems($userId);
            if(empty($cartItems)): 
            ?>
                <div class="empty-cart text-center py-5">
                    <i class="bi bi-cart-x display-1 text-muted mb-3"></i>
                    <h3 class="mb-3">Your cart is empty</h3>
                    <p class="text-muted mb-4">Looks like you haven't added any items to your cart yet.</p>
                    <a href="index.php" class="btn btn-primary btn-lg">
                        <i class="bi bi-shop me-2"></i>Start Shopping
                    </a>
                </div>
            <?php else: ?>
                <div class="cart-items">
                    <?php foreach($cartItems as $item): ?>
                        <div class="cart-item card mb-3">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        <img src="<?php echo !empty($item['image_path']) ? htmlspecialchars($item['image_path']) : 'assets/images/no-image.png'; ?>" 
                                             class="img-fluid rounded" 
                                             alt="<?php echo htmlspecialchars($item['name']); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <h5 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h5>
                                        <p class="text-muted mb-0"><?php echo htmlspecialchars($item['category_name'] ?? 'Uncategorized'); ?></p>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="quantity-control">
                                            <div class="input-group input-group-sm">
                                                <button class="btn btn-outline-secondary" type="button">-</button>
                                                <input type="text" class="form-control text-center" value="<?php echo $item['quantity']; ?>" readonly>
                                                <button class="btn btn-outline-secondary" type="button">+</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-md-end">
                                        <div class="price mb-1"><?php echo $pesoFormatter->formatCurrency($item['price'], 'PHP'); ?></div>
                                        <div class="subtotal fw-bold"><?php echo $pesoFormatter->formatCurrency($item['price'] * $item['quantity'], 'PHP'); ?></div>
                                    </div>
                                    <div class="col-md-2 text-md-end">
                                        <a href="cart.php?remove=<?php echo $item['product_id']; ?>" 
                                           class="btn btn-outline-danger btn-sm"
                                           onclick="return confirm('Are you sure you want to remove this item?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="col-lg-4">
            <div class="card order-summary">
                <div class="card-body">
                    <h5 class="card-title mb-4">Order Summary</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span><?php echo $pesoFormatter->formatCurrency($cart->getCartTotal($userId), 'PHP'); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping</span>
                        <span>Free</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <strong>Total</strong>
                        <strong class="text-primary"><?php echo $pesoFormatter->formatCurrency($cart->getCartTotal($userId), 'PHP'); ?></strong>
                    </div>
                    <?php if(!empty($cartItems)): ?>
                        <a href="checkout.php" class="btn btn-primary w-100 mb-3">
                            <i class="bi bi-credit-card me-2"></i>Proceed to Checkout
                        </a>
                    <?php endif; ?>
                    <div class="payment-methods">
                        <p class="text-muted small mb-2">We accept:</p>
                        <div class="d-flex gap-2">
                            <i class="bi bi-credit-card-2-front"></i>
                            <i class="bi bi-cash"></i>
                            <i class="bi bi-wallet2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php template('footer.php'); ?>