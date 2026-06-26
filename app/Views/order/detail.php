<?php $pageTitle = 'Order #' . ($order['Order_ID'] ?? ''); ?>

<div class="container">
    <div class="page-header">
        <h1>Order #<?= eXSS(($order['Order_ID'] ?? '')) ?></h1>
        <a href="<?= url('order/list') ?>" class="btn btn--outline">← Back to Orders</a>
    </div>

    <div class="order-detail-grid">

        <!-- ── Order Info ──────────────────────────── -->
        <div class="order-info-card">
            <h2>Order Details</h2>
            <dl class="info-list">
                <dt>Status</dt>     <dd><?= order_badge(($order['Order_Status'] ?? '')) ?></dd>
                <dt>Date</dt>       <dd><?= fmt_date(($order['Order_Date'] ?? ''), 'd M Y H:i') ?></dd>
                <dt>Address</dt>    <dd><?= eXSS(($order['Delivery_Address'] ?? '')) ?></dd>
                <dt>Shipping</dt>   <dd><?= money(($order['Shipping_Fees'] ?? 0)) ?></dd>
                <dt>Total</dt>      <dd><?= money(($order['Total_Price'] ?? 0)) ?></dd>
                <?php if (($order['Coupon_Code'] ?? '')): ?>
                    <dt>Coupon</dt> <dd><?= eXSS(($order['Coupon_Code'] ?? '')) ?></dd>
                <?php endif; ?>
            </dl>

            <?php if (($order['Order_Status'] ?? '') === 'Pending'): ?>
            <form method="POST" action="<?= url('order/cancel/' . ($order['Order_ID'] ?? '')) ?>">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn--danger"
                    onclick="return confirm('Cancel this order?')">Cancel Order</button>
            </form>
            <?php endif; ?>
        </div>

        <!-- ── Payment Info ────────────────────────── -->
        <?php if (!empty($order['payment'])): ?>
        <div class="order-info-card">
            <h2>Payment</h2>
            <dl class="info-list">
                <dt>Method</dt> <dd><?= eXSS($order['payment']['Payment_Method']) ?></dd>
                <dt>Amount</dt> <dd><?= money($order['payment']['Amount']) ?></dd>
                <dt>Status</dt> <dd><?= $order['payment']['Is_Paid'] ? '<span class="badge badge-success">Paid</span>' : '<span class="badge badge-warning">Unpaid</span>' ?></dd>
                <dt>Date</dt>   <dd><?= fmt_date($order['payment']['Payment_Date'], 'd M Y H:i') ?></dd>
            </dl>
        </div>
        <?php endif; ?>
    </div>

    <!-- ── Order Items ─────────────────────────────── -->
    <div class="order-items">
        <h2>Items Ordered</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price at Purchase</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (($order['items'] ?? []) as $item): ?>
                <tr>
                    <td>
                        <div class="order-item-product">
                            <img src="<?= product_image($item['Image_URL'] ?? null) ?>" alt="" class="order-item-product__img">
                            <a href="<?= url('product/detail/' . $item['Product_ID']) ?>"><?= eXSS($item['Name']) ?></a>
                        </div>
                    </td>
                    <td><?= money($item['Price_at_Purchase']) ?></td>
                    <td><?= eXSS($item['Quantity']) ?></td>
                    <td><?= money($item['Price_at_Purchase'] * $item['Quantity']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
