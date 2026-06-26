<?php $pageTitle = 'Orders'; ?>

<div class="admin-card">
    <div class="admin-card__header">
        <h2>All Orders</h2>
    </div>

    <!-- Status Filter -->
    <div class="admin-filter-bar">
        <?php
        $statuses = ['', 'Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled', 'Refunded'];
        foreach ($statuses as $s):
        ?>
            <a href="<?= url('admin/order/list', $s ? ['status' => $s] : []) ?>"
               class="btn btn--sm <?= ($statusFilter ?? '') === $s ? 'btn--primary' : 'btn--outline' ?>">
                <?= $s ?: 'All' ?>
            </a>
        <?php endforeach; ?>
    </div>

    <p class="table-count"><?= number_format($paginator['total'] ?? 0) ?> orders</p>

    <?php if (empty($paginator['data'])): ?>
        <p class="empty-note">No orders found.</p>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($paginator['data'] as $order): ?>
                <tr>
                    <td>#<?= eXSS($order['Order_ID']) ?></td>
                    <td><?= eXSS($order['Fname'] . ' ' . $order['Lname']) ?></td>
                    <td><?= fmt_date($order['Order_Date']) ?></td>
                    <td><?= money($order['Total_Price']) ?></td>
                    <td><?= order_badge($order['Order_Status']) ?></td>
                    <td>
                        <a href="<?= url('admin/order/detail/' . $order['Order_ID']) ?>"
                           class="btn btn--outline btn--sm">View</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?= \Core\View::pagination($paginator, url('admin/order/list', ($statusFilter ?? null) ? ['status' => ($statusFilter ?? null)] : [])) ?>
    <?php endif; ?>
</div>
