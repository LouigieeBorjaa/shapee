<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once 'helpers/functions.php';

use Aries\MiniFrameworkStore\Models\User;
use Aries\MiniFrameworkStore\Models\Order;

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Check if order ID is provided
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$user = new User();
$order = new Order();

$userId = getCurrentUserId();
$orderId = $_GET['id'];

// Get order details
$orderDetails = $order->getOrder($orderId);
$orderItems = $order->getOrderItems($orderId);

// Verify that the order belongs to the current user
if (!$orderDetails || $orderDetails['user_id'] != $userId) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Mini Framework Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/templates/header.php'; ?>

    <div class="container py-5">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                            <h2 class="mt-3">Thank You for Your Order!</h2>
                            <p class="text-muted">Order #<?php echo $orderId; ?></p>
                        </div>

                        <div class="order-details">
                            <h4 class="mb-3">Order Details</h4>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Order Date:</strong></p>
                                    <p class="text-muted"><?php echo date('F j, Y g:i A', strtotime($orderDetails['created_at'])); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Order Status:</strong></p>
                                    <p class="text-muted"><?php echo ucfirst($orderDetails['status']); ?></p>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Shipping Address:</strong></p>
                                    <p class="text-muted"><?php echo nl2br(htmlspecialchars($orderDetails['shipping_address'])); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Contact Number:</strong></p>
                                    <p class="text-muted"><?php echo htmlspecialchars($orderDetails['contact_number']); ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="order-items mt-4">
                            <h4 class="mb-3">Order Items</h4>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orderItems as $item): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td>₱<?php echo number_format($item['price'], 2); ?></td>
                                            <td>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3">Total</th>
                                            <th>₱<?php echo number_format($orderDetails['total_amount'], 2); ?></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title mb-4">What's Next?</h4>
                        <div class="next-steps">
                            <div class="d-flex align-items-center mb-3">
                                <i class="bi bi-envelope me-3 text-primary" style="font-size: 1.5rem;"></i>
                                <div>
                                    <h6 class="mb-1">Order Confirmation</h6>
                                    <p class="text-muted small mb-0">You will receive an email confirmation shortly.</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <i class="bi bi-truck me-3 text-primary" style="font-size: 1.5rem;"></i>
                                <div>
                                    <h6 class="mb-1">Order Processing</h6>
                                    <p class="text-muted small mb-0">We'll start processing your order right away.</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-clock me-3 text-primary" style="font-size: 1.5rem;"></i>
                                <div>
                                    <h6 class="mb-1">Delivery Updates</h6>
                                    <p class="text-muted small mb-0">You'll receive updates about your order status.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="index.php" class="btn btn-primary me-2">
                        <i class="bi bi-house me-2"></i>Continue Shopping
                    </a>
                    <a href="orders.php" class="btn btn-outline-primary">
                        <i class="bi bi-list me-2"></i>View Orders
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/templates/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 