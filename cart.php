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

<div class="container my-5">
    <div class="row">
        <div class="col-md-12">
            <h1>Cart</h1>
            <?php 
            $cartItems = $cart->getCartItems($userId);
            if(empty($cartItems)): 
            ?>
                <p>Your cart is empty.</p>
                <a href="index.php" class="btn btn-primary">Continue Shopping</a>
            <?php else: ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($cartItems as $item): ?>
                            <tr>
                                <td><?php echo $item['name'] ?></td>
                                <td><?php echo $item['quantity'] ?></td>
                                <td><?php echo $pesoFormatter->formatCurrency($item['price'], 'PHP') ?></td>
                                <td><?php echo $pesoFormatter->formatCurrency($item['price'] * $item['quantity'], 'PHP') ?></td>
                                <td><a href="cart.php?remove=<?php echo $item['product_id'] ?>" class="btn btn-danger">Remove</a></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total</strong></td>
                            <td colspan="2"><strong><?php echo $pesoFormatter->formatCurrency($cart->getCartTotal($userId), 'PHP') ?></strong></td>
                        </tr>
                    </tbody>
                </table>

                <a href="checkout.php" class="btn btn-success">Checkout</a>
                <a href="index.php" class="btn btn-primary">Continue Shopping</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php template('footer.php'); ?>