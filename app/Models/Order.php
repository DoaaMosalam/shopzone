<?php

namespace App\Models;

use Core\Model;

class Order extends Model
{
    protected string $table      = 'Order';
    protected string $primaryKey = 'Order_ID';

    /** Place a new order and return Order_ID (uses a transaction) */
    public function placeOrder(
        int    $customerId,
        string $deliveryAddress,
        float  $shippingFees,
        float  $totalPrice,
        ?string $couponCode,
        array  $items          // [['product_id' => X, 'price' => Y, 'qty' => Z], ...]
    ): int {
        $this->db->beginTransaction();

        try {
            $orderId = $this->insert([
                'Customer_ID'      => $customerId,
                'Delivery_Address' => $deliveryAddress,
                'Shipping_Fees'    => $shippingFees,
                'Total_Price'      => $totalPrice,
                'Coupon_Code'      => $couponCode,
                'Order_Date'       => date('Y-m-d H:i:s'),
                'Order_Status'     => 'Pending',
            ]);

            foreach ($items as $item) {
                $this->db->query(
                    "INSERT INTO Includes (Order_ID, Product_ID, Price_at_Purchase, Quantity)
                     VALUES (?, ?, ?, ?)",
                    [$orderId, $item['product_id'], $item['price'], $item['qty']]
                );

                $this->db->query(
                    "UPDATE Product SET Product_Quantity = GREATEST(0, Product_Quantity - ?) WHERE Product_ID = ?",
                    [$item['qty'], $item['product_id']]
                );
            }

            $this->db->commit();
            return $orderId;
        } catch (\Throwable $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /** Find order with customer and coupon data */
    public function findWithDetails(int $orderId): array|false
    {
        $order = $this->db->fetchOne(
            "SELECT o.*, u.Fname, u.Lname, u.Email
               FROM `Order` o
               JOIN Customer c ON c.Customer_ID = o.Customer_ID
               JOIN User     u ON u.User_ID     = c.Customer_ID
              WHERE o.Order_ID = ?
              LIMIT 1",
            [$orderId]
        );

        if (!$order) return false;

        $order['items'] = $this->db->fetchAll(
            "SELECT i.*, p.Name, p.Image_URL
               FROM Includes i
               JOIN Product p ON p.Product_ID = i.Product_ID
              WHERE i.Order_ID = ?",
            [$orderId]
        );

        $order['payment'] = $this->db->fetchOne(
            "SELECT * FROM Payment WHERE Order_ID = ? LIMIT 1",
            [$orderId]
        );

        return $order;
    }

    /** Customer's order history */
    public function forCustomer(int $customerId, int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        $data   = $this->db->fetchAll(
            "SELECT * FROM `Order` WHERE Customer_ID = ? ORDER BY Order_Date DESC LIMIT ? OFFSET ?",
            [$customerId, $perPage, $offset]
        );
        $total = $this->count('Customer_ID = ?', [$customerId]);

        return [
            'data'       => $data,
            'total'      => $total,
            'page'       => $page,
            'perPage'    => $perPage,
            'totalPages' => (int) ceil($total / $perPage),
        ];
    }

    /** Admin: all orders paginated */
    public function allWithCustomer(int $page = 1, int $perPage = 20, string $status = ''): array
    {
        $where  = $status ? "WHERE o.Order_Status = '{$status}'" : '';
        $offset = ($page - 1) * $perPage;

        $data = $this->db->fetchAll(
            "SELECT o.*, u.Fname, u.Lname
               FROM `Order` o
               JOIN Customer c ON c.Customer_ID = o.Customer_ID
               JOIN User u     ON u.User_ID     = c.Customer_ID
               {$where}
              ORDER BY o.Order_Date DESC
              LIMIT ? OFFSET ?",
            [$perPage, $offset]
        );

        $total = (int) ($this->db->fetchOne(
            "SELECT COUNT(*) AS c FROM `Order` o {$where}"
        )['c'] ?? 0);

        return [
            'data'       => $data,
            'total'      => $total,
            'page'       => $page,
            'perPage'    => $perPage,
            'totalPages' => (int) ceil($total / $perPage),
        ];
    }

    /** Update order status */
    public function updateStatus(int $orderId, string $status): void
    {
        $this->update(['Order_Status' => $status], 'Order_ID = ?', [$orderId]);
    }
}
