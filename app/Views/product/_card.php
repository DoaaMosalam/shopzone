<?php
/**
 * Reusable product card partial.
 * Expects: $p (product array)
 */
?>
<div class="product-card">
    <a href="<?= url('product/detail/' . ($p['Product_ID'] ?? '')) ?>" class="product-card__image-link">
        <img
            src="<?= eXSS(product_image($p['Image_URL'] ?? null)) ?>"
            alt="<?= eXSS($p['Name'] ?? '') ?>"
            class="product-card__img"
            loading="lazy"
            onerror="this.onerror=null;this.src='<?= asset('images/no-image.svg') ?>';"
        >
    </a>

    <div class="product-card__body">
        <?php if (!empty($p['Category_Name'])): ?>
            <span class="product-card__category"><?= eXSS($p['Category_Name']) ?></span>
        <?php endif; ?>

        <h3 class="product-card__title">
            <a href="<?= url('product/detail/' . ($p['Product_ID'] ?? '')) ?>"><?= eXSS($p['Name'] ?? '') ?></a>
        </h3>

        <div class="product-card__meta">
            <span class="product-card__price"><?= money((float)($p['Price'] ?? 0)) ?></span>
            <span class="product-card__rating" title="<?= eXSS($p['Rating_No'] ?? 0) ?>/5">
                <?= \Core\View::stars((float)($p['Rating_No'] ?? 0)) ?>
            </span>
        </div>

        <?php if ((int)($p['Product_Quantity'] ?? 0) === 0): ?>
            <span class="badge badge-danger">Out of Stock</span>
        <?php else: ?>
            <form method="POST" action="<?= url('cart/add') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= eXSS($p['Product_ID'] ?? '') ?>">
                <input type="hidden" name="qty" value="1">
                <button type="submit" class="btn btn--primary btn--sm btn--block">Add to Cart</button>
            </form>
        <?php endif; ?>
    </div>
</div>
