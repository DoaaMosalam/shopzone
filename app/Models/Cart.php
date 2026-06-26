<?php

namespace App\Models;

use Core\Model;

class Cart extends Model
{
    protected string $table      = 'Shopping_Cart';
    protected string $primaryKey = 'Cart_ID';

    /** Get or create a cart for a customer */
    public function getOrCreate(int $customerId): array
    {
        $cart = $this->db->fetchOne(
            "SELECT * FROM Shopping_Cart WHERE Customer_ID = ? LIMIT 1",
            [$customerId]
        );

        if (!$cart) {
            $cartId = $this->insert(['Customer_ID' => $customerId, 'Item_Count' => 0]);
            $cart   = ['Cart_ID' => $cartId, 'Customer_ID' => $customerId, 'Item_Count' => 0];
        }

        return $cart;
    }

    /** Get cart items with product details */
    public function getItems(int $cartId): array
    {
        return $this->db->fetchAll(
            "SELECT co.*, p.Name, p.Price, p.Image_URL, p.Product_Quantity AS Stock
               FROM Contains co
               JOIN Product p ON p.Product_ID = co.Product_ID
              WHERE co.Cart_ID = ?",
            [$cartId]
        );
    }

    /** Add or increment a product in the cart */
    public function addItem(int $cartId, int $productId, int $qty = 1): void
    {
        $existing = $this->db->fetchOne(
            "SELECT Quantity FROM Contains WHERE Cart_ID = ? AND Product_ID = ?",
            [$cartId, $productId]
        );

        if ($existing) {
            $this->db->query(
                "UPDATE Contains SET Quantity = Quantity + ? WHERE Cart_ID = ? AND Product_ID = ?",
                [$qty, $cartId, $productId]
            );
        } else {
            $this->db->query(
                "INSERT INTO Contains (Cart_ID, Product_ID, Quantity) VALUES (?, ?, ?)",
                [$cartId, $productId, $qty]
            );
        }

        $this->syncItemCount($cartId);
    }

    /** Update quantity for a cart item */
    public function updateItem(int $cartId, int $productId, int $qty): void
    {
        if ($qty <= 0) {
            $this->removeItem($cartId, $productId);
            return;
        }

        $this->db->query(
            "UPDATE Contains SET Quantity = ? WHERE Cart_ID = ? AND Product_ID = ?",
            [$qty, $cartId, $productId]
        );

        $this->syncItemCount($cartId);
    }

    /** Remove a product from the cart */
    public function removeItem(int $cartId, int $productId): void
    {
        $this->db->query(
            "DELETE FROM Contains WHERE Cart_ID = ? AND Product_ID = ?",
            [$cartId, $productId]
        );
        $this->syncItemCount($cartId);
    }

    /** Empty the entire cart */
    public function clear(int $cartId): void
    {
        $this->db->query("DELETE FROM Contains WHERE Cart_ID = ?", [$cartId]);
        $this->update(['Item_Count' => 0], 'Cart_ID = ?', [$cartId]);
    }

    /** Calculate cart subtotal */
    public function getSubtotal(int $cartId): float
    {
        $row = $this->db->fetchOne(
            "SELECT COALESCE(SUM(co.Quantity * p.Price), 0) AS subtotal
               FROM Contains co
               JOIN Product p ON p.Product_ID = co.Product_ID
              WHERE co.Cart_ID = ?",
            [$cartId]
        );
        return (float) ($row['subtotal'] ?? 0);
    }

    /** Sync Item_Count column */
    private function syncItemCount(int $cartId): void
    {
        $row = $this->db->fetchOne(
            "SELECT COALESCE(SUM(Quantity), 0) AS cnt FROM Contains WHERE Cart_ID = ?",
            [$cartId]
        );
        $this->update(['Item_Count' => (int) ($row['cnt'] ?? 0)], 'Cart_ID = ?', [$cartId]);
    }
}
