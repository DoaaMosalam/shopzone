<?php

namespace App\Models;

use Core\Model;

class Coupon extends Model
{
    protected string $table      = 'Coupon';
    protected string $primaryKey = 'Coupon_Code';

    /** Validate and return coupon if applicable for a given order total */
    public function validate(string $code, float $orderTotal): array|false
    {
        $coupon = $this->db->fetchOne(
            "SELECT * FROM Coupon WHERE Coupon_Code = ? LIMIT 1",
            [$code]
        );

        if (!$coupon) return false;

        $today = date('Y-m-d');

        if ($coupon['Status'] !== 'Active')                          return false;
        if ($coupon['Start_Date'] > $today)                         return false;
        if ($coupon['End_Date'] < $today)                           return false;
        if ($coupon['Used_Count'] >= $coupon['Usage_Limit'])        return false;
        if ($orderTotal < (float) $coupon['Min_Order_Value'])       return false;

        return $coupon;
    }

    /** Mark coupon as used and log the redemption */
    public function redeem(string $code, int $customerId): void
    {
        $this->db->query(
            "UPDATE Coupon SET Used_Count = Used_Count + 1 WHERE Coupon_Code = ?",
            [$code]
        );
        $this->db->query(
            "INSERT IGNORE INTO Redeems (Customer_ID, Coupon_Code) VALUES (?, ?)",
            [$customerId, $code]
        );
    }

    /** All coupons for admin listing (without admin join since it's optional) */
    public function allWithAdmin(int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        $data   = $this->db->fetchAll(
            "SELECT cp.*, u.Fname, u.Lname
               FROM Coupon cp
               LEFT JOIN Admin  a ON a.Admin_ID = cp.Admin_ID
               LEFT JOIN User   u ON u.User_ID  = a.Admin_ID
              ORDER BY cp.Start_Date DESC
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

    /** Expire overdue coupons (run via cron) */
    public function expireOld(): void
    {
        $this->db->query(
            "UPDATE Coupon SET Status = 'Expired' WHERE End_Date < CURDATE() AND Status = 'Active'"
        );
    }
}
