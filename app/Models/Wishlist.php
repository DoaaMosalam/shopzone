<?php

namespace App\Models;

use Core\Model;

class Wishlist extends Model
{
    protected string $table      = 'Wish_List';
    protected string $primaryKey = 'Wishlist_ID';

    /** Get or create a default wishlist for a customer */
    public function getOrCreate(int $customerId): array
    {
        $wl = $this->db->fetchOne(
            "SELECT * FROM Wish_List WHERE Customer_ID = ? LIMIT 1",
            [$customerId]
        );

        if (!$wl) {
            $id = $this->insert(['Customer_ID' => $customerId, 'Name' => 'My Wishlist']);
            $wl = ['Wishlist_ID' => $id, 'Customer_ID' => $customerId, 'Name' => 'My Wishlist'];
        }

        return $wl;
    }

    /** Get wishlist items with product data */
    public function getItems(int $wishlistId): array
    {
        return $this->db->fetchAll(
            "SELECT s.*, p.Name, p.Price, p.Image_URL, p.Rating_No
               FROM Stores s
               JOIN Product p ON p.Product_ID = s.Product_ID
              WHERE s.Wishlist_ID = ?
              ORDER BY p.Name ASC",
            [$wishlistId]
        );
    }

    /** Add a product to wishlist (ignore duplicates) */
    public function addProduct(int $wishlistId, int $productId): void
    {
        $this->db->query(
            "INSERT IGNORE INTO Stores (Wishlist_ID, Product_ID) VALUES (?, ?)",
            [$wishlistId, $productId]
        );
    }

    /** Remove a product from wishlist */
    public function removeProduct(int $wishlistId, int $productId): void
    {
        $this->db->query(
            "DELETE FROM Stores WHERE Wishlist_ID = ? AND Product_ID = ?",
            [$wishlistId, $productId]
        );
    }

    /** Check if a product is in the wishlist */
    public function hasProduct(int $wishlistId, int $productId): bool
    {
        $row = $this->db->fetchOne(
            "SELECT 1 FROM Stores WHERE Wishlist_ID = ? AND Product_ID = ? LIMIT 1",
            [$wishlistId, $productId]
        );
        return (bool) $row;
    }
}
