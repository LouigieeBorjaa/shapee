<?php

namespace Aries\MiniFrameworkStore\Models;

use Aries\MiniFrameworkStore\Includes\Database;

class Product extends Database
{
    private $db;

    public function __construct()
    {
        parent::__construct(); // Call the parent constructor to establish the connection
        $this->db = $this->getConnection(); // Get the connection instance
    }

    public function insert($data)
    {
        $sql = "INSERT INTO products (name, description, price, slug, image_path, category_id, created_at, updated_at) VALUES (:name, :description, :price, :slug, :image_path, :category_id, :created_at, :updated_at)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'slug' => $data['slug'],
            'image_path' => $data['image_path'],
            'category_id' => $data['category_id'],
            'created_at' => $data['created_at'],
            'updated_at' => $data['updated_at']
        ]);
        return $this->db->lastInsertId();
    }
    public function update()
    {
        $sql = "UPDATE products SET name = :name, description = :description, price = :price, image_url = :image_url WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'image_url' => $this->image_url
        ]);
        return "Record UPDATED successfully";
    }
    public function delete()
    {
        $sql = "DELETE FROM products WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $this->id
        ]);
        return "Record DELETED successfully";
    }
    public function getAll($search = '', $category = '')
    {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (p.name LIKE :search OR p.description LIKE :search)";
            $params['search'] = "%{$search}%";
        }

        if (!empty($category)) {
            $sql .= " AND p.category_id = :category_id";
            $params['category_id'] = $category;
        }

        $sql .= " ORDER BY p.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function getById($id)
    {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $id
        ]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    public function getByName($name)
    {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.name = :name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'name' => $name
        ]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    public function getByPrice($price)
    {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.price = :price";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'price' => $price
        ]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getByCategory($category_id)
    {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.category_id = :category_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'category_id' => $category_id
        ]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getCategories()
    {
        $sql = "SELECT * FROM categories ORDER BY name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getBySlug($slug)
    {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.slug = :slug";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'slug' => $slug
        ]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

}