<?php $pageTitle = 'Order #' . ($order['Order_ID'] ?? ''); ?>

<div class="admin-card">
    <div class="admin-card__header">
        <h2>Order #<?= eXSS(($order['Order_ID'] ?? '')) ?></h2>
        <a href="<?= url('admin/order/list') ?>" class="btn btn--outline">← Back</a>
    </div>

    <div class="order-detail-grid">
        <!-- Info -->
        <div>
            <h3>Customer</h3>
            <dl class="info-list">
                <dt>Name</dt>  <dd><?= eXSS(($order['Fname'] ?? '')) . ' ' . ($order['Lname'] ?? '') ?></dd>
                <dt>Email</dt> <dd><?= eXSS(($order['Email'] ?? '')) ?></dd>
            </dl>

            <h3>Order Info</h3>
            <dl class="info-list">
                <dt>Date</dt>    <dd><?= fmt_date(($order['Order_Date'] ?? ''), 'd M Y H:i') ?></dd>
                <dt>Address</dt> <dd><?= eXSS(($order['Delivery_Address'] ?? '')) ?></dd>
                <dt>Shipping</dt><dd><?= money(($order['Shipping_Fees'] ?? '')) ?></dd>
                <dt>Total</dt>   <dd><?= money(($order['Total_Price'] ?? '')) ?></dd>
                <?php if (($order['Coupon_Code'] ?? '')): ?>
                    <dt>Coupon</dt><dd><?= eXSS(($order['Coupon_Code'] ?? '')) ?></dd>
                <?php endif; ?>
                <dt>Status</dt>  <dd><?= order_badge(($order['Order_Status'] ?? '')) ?></dd>
            </dl>
        </div>

        <!-- Update Status -->
        <div>
            <h3>Update Status</h3>
            <form method="POST" action="<?= url('admin/order/update-status/' . ($order['Order_ID'] ?? '')) ?>">
                <?= csrf_field() ?>
                <div class="form-group">
                    <select name="status" class="form-control">
                        <?php
                        $statuses = ['Pending','Processing','Shipped','Delivered','Cancelled','Refunded'];
                        foreach ($statuses as $s):
                        ?>
                            <option value="<?= eXSS($s) ?>" <?= ($order['Order_Status'] ?? '') === $s ? 'selected' : '' ?>>
                                <?= eXSS($s) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn--primary">Update Status</button>
            </form>

            <?php if (!empty($order['payment'])): ?>
            <h3 style="margin-top:1.5rem">Payment</h3>
            <dl class="info-list">
                <dt>Method</dt><dd><?= eXSS($order['payment']['Payment_Method']) ?></dd>
                <dt>Amount</dt><dd><?= money($order['payment']['Amount']) ?></dd>
                <dt>Status</dt><dd><?= $order['payment']['Is_Paid']
                    ? '<span class="badge badge-success">Paid</span>'
                    : '<span class="badge badge-warning">Unpaid</span>' ?></dd>
            </dl>
            <?php endif; ?>
        </div>
    </div>

    <!-- Items -->
    <h3>Items</h3>
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
