<?php

namespace Aries\MiniFrameworkStore\Models;

use Aries\MiniFrameworkStore\Includes\Database;
use Carbon\Carbon;

class Checkout extends Database
{

    private $db;

    public function __construct()
    {
        parent::__construct(); // Call the parent constructor to establish the connection
        $this->db = $this->getConnection(); // Get the connection instance
    }

    public function guestCheckout($data)
    {
        $sql = "INSERT INTO orders (customer_id, guest_name, guest_phone, guest_address, total, created_at, updated_at) VALUES (:customer_id, :guest_name, :guest_phone, :guest_address, :total, :created_at, :updated_at)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'customer_id' => null,
            'guest_name' => $data['name'],
            'guest_phone' => $data['phone'],
            'guest_address' => $data['address'],
            'total' => $data['total'],
            'created_at' => Carbon::now('Asia/Manila'),
            'updated_at' => Carbon::now('Asia/Manila')
        ]);

        return $this->db->lastInsertId();
    }

    public function userCheckout($data)
    {
        $sql = "INSERT INTO orders (user_id, shipping_address, contact_number, total_amount, payment_method) 
                VALUES (:user_id, :shipping_address, :contact_number, :total_amount, :payment_method)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id' => $data['user_id'],
            'shipping_address' => $data['shipping_address'],
            'contact_number' => $data['contact_number'],
            'total_amount' => $data['total_amount'],
            'payment_method' => 'cod' // Cash on Delivery
        ]);
        return $this->db->lastInsertId();
    }

    public function saveOrderDetails($data)
    {
        $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                VALUES (:order_id, :product_id, :quantity, :price)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'order_id' => $data['order_id'],
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
            'price' => $data['price'],
        ]);
    }

    public function getAllOrders()
    {
        $sql = "SELECT 
                o.id,
                o.created_at as order_date,
                u.name as user_name,
                p.name as product_name,
                od.quantity,
                SUM(od.quantity * od.price) as total_price
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                LEFT JOIN order_items od ON o.id = od.order_id
                LEFT JOIN products p ON od.product_id = p.id
                GROUP BY o.id, o.created_at, u.name, p.name, od.quantity
                ORDER BY o.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

}