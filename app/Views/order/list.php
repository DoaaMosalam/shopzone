<?php $pageTitle = 'My Orders'; ?>

<div class="container">
    <h1 class="page-title">My Orders</h1>

    <?php if (empty($paginator['data'])): ?>
        <div class="empty-state">
            <p>You haven't placed any orders yet.</p>
            <a href="<?= url('product/list') ?>" class="btn btn--primary">Start Shopping</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($paginator['data'] as $order): ?>
                    <tr>
                        <td>#<?= eXSS($order['Order_ID']) ?></td>
                        <td><?= fmt_date($order['Order_Date']) ?></td>
                        <td><?= money($order['Total_Price']) ?></td>
                        <td><?= order_badge($order['Order_Status']) ?></td>
                        <td>
                            <a href="<?= url('order/detail/' . $order['Order_ID']) ?>" class="btn btn--outline btn--sm">View</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?= \Core\View::pagination($paginator, url('order/list')) ?>
    <?php endif; ?>
</div>
