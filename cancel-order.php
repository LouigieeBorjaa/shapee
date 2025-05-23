<?php
require_once 'helpers/functions.php';
require_once 'models/Order.php';

use Aries\MiniFrameworkStore\Models\Order;

// Check if user is logged in
if (!isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Check if order ID is provided
if (!isset($_POST['order_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Order ID is required']);
    exit();
}

$order = new Order();
$orderId = $_POST['order_id'];
$userId = getCurrentUserId();

try {
    // Verify that the order belongs to the current user
    $orderDetails = $order->getOrder($orderId);
    if (!$orderDetails || $orderDetails['user_id'] != $userId) {
        throw new Exception('Order not found or unauthorized');
    }

    // Check if order can be cancelled (only pending orders can be cancelled)
    if ($orderDetails['status'] !== 'pending') {
        throw new Exception('Only pending orders can be cancelled');
    }

    // Cancel the order
    $order->updateOrderStatus($orderId, 'cancelled');

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Order cancelled successfully']);
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 