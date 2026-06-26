<?php

namespace App\Models;

use Core\Model;

class Review extends Model
{
    protected string $table      = 'Review';
    protected string $primaryKey = 'Review_No';

    /** Submit or update a review */
    public function submitReview(int $customerId, int $productId, string $comment): void
    {
        $existing = $this->db->fetchOne(
            "SELECT Review_No FROM Review WHERE Customer_ID = ? AND Product_ID = ?",
            [$customerId, $productId]
        );

        if ($existing) {
            $this->db->query(
                "UPDATE Review SET Comment = ?, Created_At = NOW() WHERE Customer_ID = ? AND Product_ID = ?",
                [$comment, $customerId, $productId]
            );
        } else {
            $this->db->query(
                "INSERT INTO Review (Customer_ID, Product_ID, Comment, Created_At) VALUES (?, ?, ?, NOW())",
                [$customerId, $productId, $comment]
            );
        }
    }

    /** Get reviews with user data */
    public function forProduct(int $productId): array
    {
        return $this->db->fetchAll(
            "SELECT r.*, u.Fname, u.Lname, u.Profile_Img
               FROM Review r
               JOIN User u ON u.User_ID = r.Customer_ID
              WHERE r.Product_ID = ?
              ORDER BY r.Created_At DESC",
            [$productId]
        );
    }

    /** Check if a customer reviewed a product */
    public function exists(int $customerId, int $productId): bool
    {
        $row = $this->db->fetchOne(
            "SELECT 1 FROM Review WHERE Customer_ID = ? AND Product_ID = ? LIMIT 1",
            [$customerId, $productId]
        );
        return (bool) $row;
    }

    /** All reviews (admin) */
    public function allWithDetails(int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        $data   = $this->db->fetchAll(
            "SELECT r.*, u.Fname, u.Lname, p.Name AS Product_Name
               FROM Review r
               JOIN User    u ON u.User_ID    = r.Customer_ID
               JOIN Product p ON p.Product_ID = r.Product_ID
              ORDER BY r.Created_At DESC
              LIMIT ? OFFSET ?",
            [$perPage, $offset]
        );
        $total = $this->count();
        return [
            'data'       => $data,
            'total'      => $total,
            'page'       => $page,
            'perPage'    => $perPage,
            'totalPages' => (int) ceil($total / $perPage),
        ];
    }
}
