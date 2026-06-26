<?php

namespace App\Models;

use Core\Model;

class Customer extends Model
{
    protected string $table      = 'Customer';
    protected string $primaryKey = 'Customer_ID';

    /** Find customer with user data */
    public function findWithUser(int $customerId): array|false
    {
        return $this->db->fetchOne(
            "SELECT c.*, u.Fname, u.Mname, u.Lname, u.Gender,
                    u.Date_of_Birth, u.Profile_Img, u.Email, u.Created_At
               FROM Customer c
               JOIN User u ON u.User_ID = c.Customer_ID
              WHERE c.Customer_ID = ?
              LIMIT 1",
            [$customerId]
        );
    }

    /** All customers with user data – paginated */
    public function allWithUser(int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        $data   = $this->db->fetchAll(
            "SELECT c.*, u.Fname, u.Lname, u.Email, u.Created_At
               FROM Customer c
               JOIN User u ON u.User_ID = c.Customer_ID
              ORDER BY u.Created_At DESC
              LIMIT ? OFFSET ?",
            [$perPage, $offset]
        );
        $total  = $this->count();

        return [
            'data'       => $data,
            'total'      => $total,
            'page'       => $page,
            'perPage'    => $perPage,
            'totalPages' => (int) ceil($total / $perPage),
        ];
    }

    /**
     * Update account status with optional ban expiry date.
     *
     * @param int         $customerId
     * @param string      $status    'Active' | 'Suspended' | 'Banned'
     * @param string|null $banUntil  Datetime string (e.g. '2026-06-01 00:00:00'), or null
     */
    public function setStatus(int $customerId, string $status, ?string $banUntil = null): void
    {
        if ($status === 'Banned') {
            $this->update(
                ['Account_Status' => $status, 'Ban_Until' => $banUntil],
                'Customer_ID = ?',
                [$customerId]
            );
        } else {
            // When lifting ban / suspending, always clear Ban_Until
            $this->update(
                ['Account_Status' => $status, 'Ban_Until' => null],
                'Customer_ID = ?',
                [$customerId]
            );
        }
    }

    /** Update shipping address fields */
    public function updateAddress(int $customerId, array $address): void
    {
        $allowed = ['City', 'Street', 'State', 'Zip_Code'];
        $data    = array_intersect_key($address, array_flip($allowed));
        if ($data) {
            $this->update($data, 'Customer_ID = ?', [$customerId]);
        }
    }

    /**
     * Fetch customer status for login check.
     * Returns the customer row (Account_Status, Ban_Until) or false.
     */
    public function findStatusById(int $customerId): array|false
    {
        return $this->db->fetchOne(
            "SELECT Account_Status, Ban_Until FROM Customer WHERE Customer_ID = ? LIMIT 1",
            [$customerId]
        );
    }
}
