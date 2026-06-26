<?php $pageTitle = 'Checkout'; ?>

<div class="container">
    <h1 class="page-title">Checkout</h1>

    <div class="checkout-layout">

        <!-- ── Checkout Form ───────────────────────── -->
        <form method="POST" action="<?= url('checkout/place') ?>" class="checkout-form">
            <?= csrf_field() ?>

            <div class="checkout-section">
                <h2>Delivery Address</h2>
                <div class="form-group">
                    <label for="address">Full Address</label>
                    <textarea id="address" name="address" rows="3" class="form-control" required
                        placeholder="Street, City, State, ZIP"><?= eXSS($_SESSION['address'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="checkout-section">
                <h2>Payment Method</h2>
                <?php
                $methods = [
                    'CashOnDelivery' => 'Cash on Delivery',
                    'CreditCard'     => 'Credit Card',
                    'DebitCard'      => 'Debit Card',
                    'PayPal'         => 'PayPal',
                    'BankTransfer'   => 'Bank Transfer',
                ];
                ?>
                <?php foreach ($methods as $val => $label): ?>
                <label class="radio-option">
                    <input type="radio" name="payment_method" value="<?= eXSS($val) ?>"
                        <?= $val === 'CashOnDelivery' ? 'checked' : '' ?>>
                    <?= eXSS($label) ?>
                </label>
                <?php endforeach; ?>
            </div>

            <div class="checkout-section">
                <h2>Coupon Code</h2>
                <div class="form-row">
                    <input type="text" name="coupon_code" class="form-control" placeholder="SHOPZONE2025">
                </div>
            </div>

            <button type="submit" class="btn btn--primary btn--block btn--lg">Place Order</button>
        </form>

        <!-- ── Order Summary ───────────────────────── -->
        <aside class="checkout-summary">
            <h2>Your Order</h2>
            <?php foreach (($items ?? []) as $item): ?>
            <div class="checkout-item">
                <span class="checkout-item__name"><?= eXSS($item['Name']) ?> × <?= eXSS($item['Quantity']) ?></span>
                <span class="checkout-item__price"><?= money($item['Price'] * $item['Quantity']) ?></span>
            </div>
            <?php endforeach; ?>

            <hr>
            <div class="checkout-item">
                <span>Subtotal</span>
                <span><?= money(($subtotal ?? 0)) ?></span>
            </div>
            <div class="checkout-item">
                <span>Shipping</span>
                <span><?= money(50) ?></span>
            </div>
            <hr>
            <div class="checkout-item checkout-item--total">
                <strong>Total</strong>
                <strong><?= money(($subtotal ?? 0) + 50) ?></strong>
            </div>
        </aside>
    </div>
</div>
