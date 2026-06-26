<?php

namespace App\Models;

use Core\Model;

class Product extends Model
{
    protected string $table      = 'Product';
    protected string $primaryKey = 'Product_ID';

    /** Paginated product list with optional filters */
    public function search(
        string $keyword    = '',
        int    $categoryId = 0,
        string $sortBy     = 'Product_ID',
        string $sortDir    = 'DESC',
        int    $page       = 1,
        int    $perPage    = 12
    ): array {
        $where  = [];
        $params = [];

        if ($keyword) {
            $where[]  = "(p.Name LIKE ? OR p.Brand LIKE ? OR p.Description LIKE ?)";
            $kw       = "%{$keyword}%";
            $params[] = $kw;
            $params[] = $kw;
            $params[] = $kw;
        }

        if ($categoryId) {
            $where[]  = "p.Category_ID = ?";
            $params[] = $categoryId;
        }

        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $allowedSort = ['Product_ID', 'Price', 'Rating_No', 'Name', 'Release_Date'];
        $sortBy      = in_array($sortBy, $allowedSort, true) ? $sortBy : 'Product_ID';
        $sortDir     = strtoupper($sortDir) === 'ASC' ? 'ASC' : 'DESC';

        $offset = ($page - 1) * $perPage;

        $countSql = "SELECT COUNT(*) AS c FROM Product p {$whereClause}";
        $total    = (int) ($this->db->fetchOne($countSql, $params)['c'] ?? 0);

        $sql = "SELECT p.*, c.Name AS Category_Name
                  FROM Product p
                  LEFT JOIN Category c ON c.Category_ID = p.Category_ID
                  {$whereClause}
                  ORDER BY p.{$sortBy} {$sortDir}
                  LIMIT ? OFFSET ?";

        $data = $this->db->fetchAll($sql, [...$params, $perPage, $offset]);

        return [
            'data'       => $data,
            'total'      => $total,
            'page'       => $page,
            'perPage'    => $perPage,
            'totalPages' => (int) ceil($total / $perPage),
        ];
    }

    /** Find product with category and specifications */
    public function findDetail(int $productId): array|false
    {
        $product = $this->db->fetchOne(
            "SELECT p.*, c.Name AS Category_Name
               FROM Product p
               LEFT JOIN Category c ON c.Category_ID = p.Category_ID
              WHERE p.Product_ID = ?
              LIMIT 1",
            [$productId]
        );

        if (!$product) return false;

        $product['specs'] = $this->db->fetchAll(
            "SELECT Spec_Key, Spec_Value FROM Specification WHERE Product_ID = ?",
            [$productId]
        );

        return $product;
    }

    /** Get reviews for a product */
    public function getReviews(int $productId): array
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

    /** Recalculate and update average rating */
    public function refreshRating(int $productId): void
    {
        $row = $this->db->fetchOne(
            "SELECT AVG(AI_Rating) AS avg_r FROM Review WHERE Product_ID = ? AND AI_Rating IS NOT NULL",
            [$productId]
        );
        $avg = round((float) ($row['avg_r'] ?? 0), 2);
        $this->update(['Rating_No' => $avg], 'Product_ID = ?', [$productId]);
    }

    /** Get featured / latest products */
    public function featured(int $limit = 8): array
    {
        return $this->db->fetchAll(
            "SELECT p.*, c.Name AS Category_Name
               FROM Product p
               LEFT JOIN Category c ON c.Category_ID = p.Category_ID
              WHERE p.Product_Quantity > 0
              ORDER BY p.Rating_No DESC, p.Product_ID DESC
              LIMIT ?",
            [$limit]
        );
    }

    /** Decrement stock after purchase */
    public function decrementStock(int $productId, int $qty): void
    {
        $this->db->query(
            "UPDATE Product SET Product_Quantity = MAX(0, Product_Quantity - ?) WHERE Product_ID = ?",
            [$qty, $productId]
        );
    }

    /** Products with low stock */
    public function lowStock(int $threshold = 5): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM Product WHERE Product_Quantity < ? ORDER BY Product_Quantity ASC",
            [$threshold]
        );
    }
}
