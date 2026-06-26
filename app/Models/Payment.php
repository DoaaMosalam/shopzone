<?php

namespace App\Models;

use Core\Model;

class Payment extends Model
{
    protected string $table      = 'Payment';
    protected string $primaryKey = 'Payment_ID';

    /** Create a payment record for an order */
    public function createForOrder(int $orderId, string $method, float $amount): int
    {
        return $this->insert([
            'Order_ID'       => $orderId,
            'Payment_Method' => $method,
            'Amount'         => $amount,
            'Is_Paid'        => 0,
            'Payment_Date'   => date('Y-m-d H:i:s'),
        ]);
    }

    /** Mark payment as paid */
    public function markPaid(int $paymentId): void
    {
        $this->update(['Is_Paid' => 1], 'Payment_ID = ?', [$paymentId]);
    }

    /** Get payment by order ID */
    public function findByOrder(int $orderId): array|false
    {
        return $this->db->fetchOne(
            "SELECT * FROM Payment WHERE Order_ID = ? LIMIT 1",
            [$orderId]
        );
    }
}
