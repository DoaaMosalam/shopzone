<?php $pageTitle = 'Shopping Cart'; ?>

<div class="container">
    <h1 class="page-title">Shopping Cart</h1>

    <?php if (empty($items)): ?>
        <div class="empty-state">
            <p>Your cart is empty.</p>
            <a href="<?= url('product/list') ?>" class="btn btn--primary">Browse Products</a>
        </div>
    <?php else: ?>
    <div class="cart-layout">

        <!-- ── Cart Items ──────────────────────────── -->
        <div class="cart-items">
            <?php foreach ($items as $item): ?>
            <div class="cart-item">
                <img src="<?= product_image($item['Image_URL'] ?? null) ?>" alt="<?= eXSS($item['Name']) ?>" class="cart-item__img">
                <div class="cart-item__info">
                    <h3 class="cart-item__name">
                        <a href="<?= url('product/detail/' . $item['Product_ID']) ?>"><?= eXSS($item['Name']) ?></a>
                    </h3>
                    <p class="cart-item__price"><?= money($item['Price']) ?> each</p>

                    <form method="POST" action="<?= url('cart/update') ?>" class="cart-item__qty">
                        <?= csrf_field() ?>
                        <input type="hidden" name="product_id" value="<?= eXSS($item['Product_ID']) ?>">
                        <label>Qty:</label>
                        <input type="number" name="qty" value="<?= eXSS($item['Quantity']) ?>"
                               min="1" max="<?= eXSS($item['Stock']) ?>" class="form-control qty-input">
                        <button type="submit" class="btn btn--outline btn--sm">Update</button>
                    </form>
                </div>

                <div class="cart-item__subtotal">
                    <?= money($item['Price'] * $item['Quantity']) ?>
                </div>

                <form method="POST" action="<?= url('cart/remove') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="product_id" value="<?= eXSS($item['Product_ID']) ?>">
                    <button type="submit" class="btn btn--danger btn--sm" title="Remove">✕</button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- ── Cart Summary ────────────────────────── -->
        <div class="cart-summary">
            <h2 class="cart-summary__title">Order Summary</h2>
            <div class="cart-summary__row">
                <span>Subtotal</span>
                <strong><?= money(($subtotal ?? '')) ?></strong>
            </div>
            <div class="cart-summary__row">
                <span>Shipping</span>
                <strong><?= money(50) ?></strong>
            </div>
            <hr>
            <div class="cart-summary__row cart-summary__total">
                <span>Estimated Total</span>
                <strong><?= money(($subtotal ?? 0) + 50) ?></strong>
            </div>
            <a href="<?= url('checkout/index') ?>" class="btn btn--primary btn--block">Proceed to Checkout</a>
            <a href="<?= url('product/list') ?>"   class="btn btn--outline btn--block">Continue Shopping</a>
        </div>
    </div>
    <?php endif; ?>
</div>
