<?php $pageTitle = 'Dashboard'; ?>

<!-- ── Stats Cards ─────────────────────────────────── -->
<div class="stats-grid">
    <div class="stat-card stat-card--blue">
        <div class="stat-card__icon">📦</div>
        <div class="stat-card__body">
            <p class="stat-card__label">Total Products</p>
            <h2 class="stat-card__value"><?= number_format((int)($stats['total_products'] ?? 0)) ?></h2>
        </div>
    </div>
    <div class="stat-card stat-card--green">
        <div class="stat-card__icon">🧾</div>
        <div class="stat-card__body">
            <p class="stat-card__label">Total Orders</p>
            <h2 class="stat-card__value"><?= number_format((int)($stats['total_orders'] ?? 0)) ?></h2>
        </div>
    </div>
    <div class="stat-card stat-card--purple">
        <div class="stat-card__icon">👤</div>
        <div class="stat-card__body">
            <p class="stat-card__label">Customers</p>
            <h2 class="stat-card__value"><?= number_format((int)($stats['total_customers'] ?? 0)) ?></h2>
        </div>
    </div>
    <div class="stat-card stat-card--orange">
        <div class="stat-card__icon">💰</div>
        <div class="stat-card__body">
            <p class="stat-card__label">Revenue</p>
            <h2 class="stat-card__value"><?= money((float)($stats['total_revenue'] ?? 0)) ?></h2>
        </div>
    </div>
    <div class="stat-card stat-card--yellow">
        <div class="stat-card__icon">⏳</div>
        <div class="stat-card__body">
            <p class="stat-card__label">Pending Orders</p>
            <h2 class="stat-card__value"><?= number_format((int)($stats['pending_orders'] ?? 0)) ?></h2>
        </div>
    </div>
    <div class="stat-card stat-card--red">
        <div class="stat-card__icon">⚠️</div>
        <div class="stat-card__body">
            <p class="stat-card__label">Low Stock Items</p>
            <h2 class="stat-card__value"><?= number_format((int)($stats['low_stock'] ?? 0)) ?></h2>
        </div>
    </div>
</div>

<!-- ── Recent Orders ───────────────────────────────── -->
<div class="admin-card">
    <div class="admin-card__header">
        <h2>Recent Orders</h2>
        <a href="<?= url('admin/order/list') ?>" class="btn btn--outline btn--sm">View All</a>
    </div>

    <?php if (empty($recentOrders)): ?>
        <p class="empty-note">No orders yet.</p>
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
                <?php foreach ($recentOrders as $order): ?>
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
    <?php endif; ?>
</div>
