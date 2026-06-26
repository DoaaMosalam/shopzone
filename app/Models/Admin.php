<?php

namespace App\Models;

use Core\Model;

class Admin extends Model
{
    protected string $table      = 'Admin';
    protected string $primaryKey = 'Admin_ID';

    /** Find admin record by User_ID, joined with User table */
    public function findWithUser(int $userId): array|false
    {
        return $this->db->fetchOne(
            "SELECT a.*, u.Fname, u.Mname, u.Lname, u.Email, u.Profile_Img
               FROM Admin a
               JOIN User u ON u.User_ID = a.Admin_ID
              WHERE a.Admin_ID = ?
              LIMIT 1",
            [$userId]
        );
    }

    /** Update last login timestamp */
    public function touchLastLogin(int $adminId): void
    {
        $this->update(['Last_Login' => date('Y-m-d H:i:s')], 'Admin_ID = ?', [$adminId]);
    }

    /** Dashboard stats */
    public function getDashboardStats(): array
    {
        return [
            'total_products'  => (int) ($this->db->fetchOne("SELECT COUNT(*) AS c FROM Product")['c'] ?? 0),
            'total_orders'    => (int) ($this->db->fetchOne("SELECT COUNT(*) AS c FROM `Order`")['c'] ?? 0),
            'total_customers' => (int) ($this->db->fetchOne("SELECT COUNT(*) AS c FROM Customer")['c'] ?? 0),
            'total_revenue'   => (float) ($this->db->fetchOne(
                "SELECT COALESCE(SUM(Total_Price),0) AS r FROM `Order` WHERE Order_Status = 'Delivered'"
            )['r'] ?? 0),
            'pending_orders'  => (int) ($this->db->fetchOne(
                "SELECT COUNT(*) AS c FROM `Order` WHERE Order_Status = 'Pending'"
            )['c'] ?? 0),
            'low_stock'       => (int) ($this->db->fetchOne(
                "SELECT COUNT(*) AS c FROM Product WHERE Product_Quantity < 5"
            )['c'] ?? 0),
        ];
    }

    /** Recent orders for dashboard */
    public function getRecentOrders(int $limit = 10): array
    {
        return $this->db->fetchAll(
            "SELECT o.*, u.Fname, u.Lname
               FROM `Order` o
               JOIN Customer c  ON c.Customer_ID  = o.Customer_ID
               JOIN User     u  ON u.User_ID       = c.Customer_ID
              ORDER BY o.Order_Date DESC
              LIMIT ?",
            [$limit]
        );
    }
}
