<?php $pageTitle = 'Welcome to ' . APP_NAME; ?>

<!-- ── Hero ──────────────────────────────────────── -->
<section class="hero">
    <div class="container hero__inner">
        <h1 class="hero__title">Shop the Best Tech Products</h1>
        <p class="hero__subtitle">Smart watches, laptops, phones & more — quality guaranteed, delivered fast.</p>
        <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
            <a href="<?= url('product/list') ?>" class="btn btn--primary btn--lg">Shop Now</a>
            <a href="<?= url('about/index') ?>"  class="btn btn--outline btn--lg" style="color:#fff;border-color:#fff;">Learn More</a>
        </div>
    </div>
</section>

<!-- ── Trust Badges ───────────────────────────────── -->
<section class="trust-bar">
    <div class="container trust-bar__inner">
        <div class="trust-badge">🚚 <span>Free Shipping</span> over 500 EGP</div>
        <div class="trust-badge">🔒 <span>Secure Checkout</span> SSL encrypted</div>
        <div class="trust-badge">↩️ <span>30-Day Returns</span> no hassle</div>
        <div class="trust-badge">🛠️ <span>24/7 Support</span> always here</div>
    </div>
</section>

<!-- ── Categories ────────────────────────────────── -->
<?php if (!empty($categories)): ?>
<section class="section">
    <div class="container">
        <h2 class="section__title">Browse Categories</h2>
        <?php 
        $catIcons = [
            'Smartphones'    => '📱',
            'Laptops'        => '💻',
            'Smart Watches'  => '⌚',
            'Monitors & TVs' => '🖥️',
            'PC Cases'       => '🗄️',
            'Headphones'     => '🎧',
            'Keyboards'      => '⌨️',
            'Gaming'         => '🎮',
            'Tablets'        => '📟',
            'Cameras'        => '📷',
        ];
        ?>
        <div class="categories-grid">
            <?php foreach ($categories as $cat): ?>
            <a href="<?= url('product/list', ['category' => $cat['Category_ID']]) ?>" class="category-card">
                <?php if ($cat['Image']): ?>
                    <img src="<?= eXSS(product_image($cat['Image'])) ?>" alt="<?= eXSS($cat['Name']) ?>" class="category-card__img">
                <?php else: ?>
                    <div class="category-card__placeholder">
                        <?= $catIcons[$cat['Name']] ?? '🗂️' ?>
                    </div>
                <?php endif; ?>
                <span class="category-card__name"><?= eXSS($cat['Name']) ?></span>
                <span class="category-card__count"><?= eXSS($cat['product_count']) ?> items</span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ── Featured Products ──────────────────────────── -->
<?php if (!empty($featured)): ?>
<section class="section section--gray">
    <div class="container">
        <div class="section__header">
            <h2 class="section__title">Featured Products</h2>
            <a href="<?= url('product/list') ?>" class="section__link">View all →</a>
        </div>
        <div class="products-grid">
            <?php foreach ($featured as $p): ?>
                <?php include VIEW_PATH . '/product/_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ── New Arrivals ───────────────────────────────── -->
<?php if (!empty($newArrivals)): ?>
<section class="section">
    <div class="container">
        <div class="section__header">
            <h2 class="section__title">New Arrivals</h2>
            <a href="<?= url('product/list', ['sort' => 'Release_Date', 'dir' => 'DESC']) ?>" class="section__link">View all →</a>
        </div>
        <div class="products-grid">
            <?php foreach ($newArrivals as $p): ?>
                <?php include VIEW_PATH . '/product/_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
