<?php

namespace App\Models;

use Core\Model;

class Category extends Model
{
    protected string $table      = 'Category';
    protected string $primaryKey = 'Category_ID';

    /** All categories with product count */
    public function allWithCount(): array
    {
        return $this->db->fetchAll(
            "SELECT c.*, COUNT(p.Product_ID) AS product_count
               FROM Category c
               LEFT JOIN Product p ON p.Category_ID = c.Category_ID
              GROUP BY c.Category_ID
              ORDER BY c.Name ASC"
        );
    }

    /** Find by name (case-insensitive) */
    public function findByName(string $name): array|false
    {
        return $this->db->fetchOne(
            "SELECT * FROM Category WHERE LOWER(Name) = LOWER(?) LIMIT 1",
            [$name]
        );
    }
}
