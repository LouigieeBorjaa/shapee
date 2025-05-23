<?php 
require_once 'helpers/functions.php';
require_once 'vendor/autoload.php';

use Aries\MiniFrameworkStore\Models\Checkout;
use Aries\MiniFrameworkStore\Models\Cart;

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$userId = getCurrentUserId();
$cart = new Cart();
$checkout = new Checkout();

$cartItems = $cart->getCartItems($userId);
$total = $cart->getCartTotal($userId);

$amounLocale = 'en_PH';
$pesoFormatter = new NumberFormatter($amounLocale, NumberFormatter::CURRENCY);

if(isset($_POST['submit'])) {
    // Validate and sanitize input
    $shipping_address = filter_input(INPUT_POST, 'shipping_address', FILTER_SANITIZE_STRING);
    $contact_number = filter_input(INPUT_POST, 'contact_number', FILTER_SANITIZE_STRING);

    if (!$shipping_address || !$contact_number) {
        echo "<script>alert('Please fill in all required fields.');</script>";
    } else {
        // Create order
        $orderId = $checkout->userCheckout([
            'user_id' => $userId,
            'shipping_address' => $shipping_address,
            'contact_number' => $contact_number,
            'total_amount' => $total
        ]);

        // Save order details
        foreach($cartItems as $item) {
            $checkout->saveOrderDetails([
                'order_id' => $orderId,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['price'] * $item['quantity']
            ]);
        }

        // Clear the cart
        $cart->clearCart($userId);

        echo "<script>alert('Order placed successfully! You will pay ₱" . number_format($total, 2) . " upon delivery.'); window.location.href='index.php'</script>";
    }
}

template('header.php');
?>

<div class="container my-5">
    <div class="row">
        <h1>Checkout</h1>
        <h2>Cart Details</h2>
        <table class="table table-bordered">
            <?php if(!empty($cartItems)): ?>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($cartItems as $item): ?>
                    <tr>
                        <td><?php echo $item['name'] ?></td>
                        <td><?php echo $item['quantity'] ?></td>
                        <td><?php echo $pesoFormatter->formatCurrency($item['price'], 'PHP') ?></td>
                        <td><?php echo $pesoFormatter->formatCurrency($item['price'] * $item['quantity'], 'PHP') ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" class="text-end"><strong>Total</strong></td>
                    <td><strong><?php echo $pesoFormatter->formatCurrency($total, 'PHP') ?></strong></td>
                </tr>
            </tbody>
            <?php else: ?>
            <tbody>
                <tr>
                    <td colspan="4" class="text-center">Your cart is empty</td>
                </tr>
            </tbody>
            <?php endif; ?>
        </table>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h2>Shipping Information</h2>
            <?php if(empty($cartItems)): ?>
                <p>Your cart is empty.</p>
                <a href="index.php" class="btn btn-primary">Continue Shopping</a>
            <?php else: ?>
                <form action="checkout.php" method="POST">
                    <div class="mb-3">
                        <label for="shipping_address" class="form-label">Shipping Address</label>
                        <input type="text" class="form-control" id="shipping_address" name="shipping_address" required>
                    </div>
                    <div class="mb-3">
                        <label for="contact_number" class="form-label">Contact Number</label>
                        <input type="text" class="form-control" id="contact_number" name="contact_number" required>
                    </div>
                    <div class="mb-3">
                        <div class="alert alert-info">
                            <h5>Payment Method: Cash on Delivery</h5>
                            <p>You will pay the total amount of <?php echo $pesoFormatter->formatCurrency($total, 'PHP') ?> upon delivery.</p>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success" name="submit">Place Order</button>
                    <a href="cart.php" class="btn btn-primary">View Cart</a>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php template('footer.php'); ?>