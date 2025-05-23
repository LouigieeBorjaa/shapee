<?php
namespace Aries\MiniFrameworkStore\Models;

require_once __DIR__ . '/../includes/database.php';

use Aries\MiniFrameworkStore\Includes\Database;

class Cart {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function addToCart($userId, $productId, $quantity = 1) {
        // Check if product already exists in cart
        $existingItem = $this->getCartItem($userId, $productId);
        
        if ($existingItem) {
            // Update quantity if item exists
            return $this->updateQuantity($userId, $productId, $existingItem['quantity'] + $quantity);
        }

        $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
        $stmt = $this->db->getConnection()->prepare($sql);
        return $stmt->execute([$userId, $productId, $quantity]);
    }

    public function removeFromCart($userId, $productId) {
        $sql = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        return $stmt->execute([$userId, $productId]);
    }

    public function updateQuantity($userId, $productId, $quantity) {
        if ($quantity <= 0) {
            return $this->removeFromCart($userId, $productId);
        }

        $sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        return $stmt->execute([$quantity, $userId, $productId]);
    }

    public function getCartItem($userId, $productId) {
        $sql = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$userId, $productId]);
        return $stmt->fetch();
    }

    public function getCartItems($userId) {
        $sql = "SELECT c.*, p.name, p.price, p.image_path 
                FROM cart c 
                JOIN products p ON c.product_id = p.id 
                WHERE c.user_id = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getCartTotal($userId) {
        $sql = "SELECT SUM(c.quantity * p.price) as total 
                FROM cart c 
                JOIN products p ON c.product_id = p.id 
                WHERE c.user_id = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    public function clearCart($userId) {
        $sql = "DELETE FROM cart WHERE user_id = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        return $stmt->execute([$userId]);
    }

    public function countCartItems($userId) {
        $sql = "SELECT COUNT(*) as count FROM cart WHERE user_id = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
} 