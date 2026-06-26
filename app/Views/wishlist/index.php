<?php $pageTitle = 'My Wishlist'; ?>

<div class="container">
    <h1 class="page-title"> <?= eXSS(($wishlist['Name'] ?? '')) ?></h1>

    <?php if (empty($items)): ?>
        <div class="empty-state">
            <p>Your wishlist is empty.</p>
            <a href="<?= url('product/list') ?>" class="btn btn--primary">Discover Products</a>
        </div>
    <?php else: ?>
        <div class="products-grid">
            
            <?php foreach ($items as $item): ?>
            <div class="product-card">



                <a href="<?= url('product/detail/' . $item['Product_ID']) ?>" class="product-card__image-link">
        
                    <img src="<?= eXSS(product_image($item['Image_URL'] ?? null)) ?>"
                        alt="<?= eXSS($item['Name']) ?>"
                        class="product-card__img"
                        style="width: 100%; max-width: 300px; height: 300px; display: block; margin: 0 auto;"
                        loading="lazy"
                        onerror="this.onerror=null;this.src='<?= asset('images/no-image.svg') ?>';">
                </a>

                <div class="product-card__body">
                    <h3 class="product-card__title">
                        <a href="<?= url('product/detail/' . $item['Product_ID']) ?>"><?= eXSS($item['Name']) ?></a>
                    </h3>
                    <div class="product-card__meta">
                        <span class="product-card__price"><?= money($item['Price']) ?></span>
                        <span class="product-card__rating"><?= \Core\View::stars((float) $item['Rating_No']) ?></span>
                    </div>
                    <div class="product-card__actions">
                        <form method="POST" action="<?= url('cart/add') ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="product_id" value="<?= eXSS($item['Product_ID']) ?>">
                            <input type="hidden" name="qty" value="1">
                            <button class="btn btn--primary btn--sm">Add to Cart</button>
                        </form>
                        <form method="POST" action="<?= url('wishlist/remove') ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="product_id" value="<?= eXSS($item['Product_ID']) ?>">
                            <button class="btn btn--danger btn--sm">Remove</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
