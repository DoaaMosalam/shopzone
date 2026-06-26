<?php $pageTitle = 'About Us – ' . APP_NAME; ?>

<!-- ── About Hero ──────────────────────────────── -->
<section class="page-hero">
    <div class="container page-hero__inner">
        <h1 class="page-hero__title">About ShopZone</h1>
        <p class="page-hero__subtitle">Your one-stop destination for premium tech products.</p>
    </div>
</section>

<!-- ── Our Story ──────────────────────────────── -->
<section class="section">
    <div class="container">
        <div class="about-grid">
            <div class="about-text">
                <h2 class="section__title">Our Story</h2>
                <p>ShopZone was founded with a simple mission: to make premium technology accessible to everyone. We started as a small team of tech enthusiasts who believed that great products shouldn't come with an overwhelming price tag or a confusing shopping experience.</p>
                <p style="margin-top:1rem;">Today, we offer thousands of carefully curated products — from the latest smartphones and laptops to smart watches, monitors, gaming accessories, and more — all backed by expert support and fast delivery.</p>
                <p style="margin-top:1rem;">We partner with top global brands to ensure every item we sell meets our high quality standards. Whether you're a gamer, a professional, or just upgrading your setup, ShopZone has something for you.</p>
            </div>
            <div class="about-stats">
                <div class="stat-card">
                    <span class="stat-card__icon">📦</span>
                    <span class="stat-card__number">50,000+</span>
                    <span class="stat-card__label">Products Sold</span>
                </div>
                <div class="stat-card">
                    <span class="stat-card__icon">😊</span>
                    <span class="stat-card__number">25,000+</span>
                    <span class="stat-card__label">Happy Customers</span>
                </div>
                <div class="stat-card">
                    <span class="stat-card__icon">🏆</span>
                    <span class="stat-card__number">200+</span>
                    <span class="stat-card__label">Top Brands</span>
                </div>
                <div class="stat-card">
                    <span class="stat-card__icon">⚡</span>
                    <span class="stat-card__number">24h</span>
                    <span class="stat-card__label">Fast Delivery</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── Our Values ──────────────────────────────── -->
<section class="section section--gray">
    <div class="container">
        <h2 class="section__title" style="text-align:center;margin-bottom:2rem;">Why Choose ShopZone?</h2>
        <div class="values-grid">
            <div class="value-card">
                <div class="value-card__icon">✅</div>
                <h3 class="value-card__title">Quality Guaranteed</h3>
                <p class="value-card__desc">Every product is verified and sourced directly from authorised distributors and manufacturers.</p>
            </div>
            <div class="value-card">
                <div class="value-card__icon">🚚</div>
                <h3 class="value-card__title">Fast Shipping</h3>
                <p class="value-card__desc">We offer same-day and next-day delivery options so your new tech arrives when you need it.</p>
            </div>
            <div class="value-card">
                <div class="value-card__icon">🔒</div>
                <h3 class="value-card__title">Secure Payments</h3>
                <p class="value-card__desc">Shop confidently with SSL encryption and multiple secure payment methods accepted.</p>
            </div>
            <div class="value-card">
                <div class="value-card__icon">🛠️</div>
                <h3 class="value-card__title">Expert Support</h3>
                <p class="value-card__desc">Our dedicated support team is available 7 days a week to help you with any questions.</p>
            </div>
            <div class="value-card">
                <div class="value-card__icon">↩️</div>
                <h3 class="value-card__title">Easy Returns</h3>
                <p class="value-card__desc">Not happy? Return any product within 30 days for a full refund, no questions asked.</p>
            </div>
            <div class="value-card">
                <div class="value-card__icon">💎</div>
                <h3 class="value-card__title">Best Prices</h3>
                <p class="value-card__desc">We regularly compare prices so you always get the best deal on the market.</p>
            </div>
        </div>
    </div>
</section>

<!-- ── Team ──────────────────────────────── -->
<section class="section">
    <div class="container" style="text-align:center;">
        <h2 class="section__title">Meet Our Team</h2>
        <p style="color:var(--color-text-muted);margin-bottom:2.5rem;">A passionate group of tech lovers dedicated to improving your shopping experience.</p>
        <div class="team-grid">
            <div class="team-card">
                <div class="team-card__avatar">👨‍💼</div>
                <h4 class="team-card__name">Ahmed Hassan</h4>
                <span class="team-card__role">CEO &amp; Founder</span>
            </div>
            <div class="team-card">
                <div class="team-card__avatar">👩‍💻</div>
                <h4 class="team-card__name">Sara Ali</h4>
                <span class="team-card__role">Head of Technology</span>
            </div>
            <div class="team-card">
                <div class="team-card__avatar">👨‍🎨</div>
                <h4 class="team-card__name">Kareem Nour</h4>
                <span class="team-card__role">Product Designer</span>
            </div>
            <div class="team-card">
                <div class="team-card__avatar">👩‍💼</div>
                <h4 class="team-card__name">Nadia Farouk</h4>
                <span class="team-card__role">Customer Success</span>
            </div>
        </div>
    </div>
</section>

<!-- ── CTA ──────────────────────────────── -->
<section class="section section--gray">
    <div class="container" style="text-align:center;">
        <h2 style="font-size:1.8rem;font-weight:800;margin-bottom:1rem;">Ready to Start Shopping?</h2>
        <p style="color:var(--color-text-muted);margin-bottom:2rem;">Explore thousands of products across all categories.</p>
        <a href="<?= url('product/list') ?>" class="btn btn--primary btn--lg">Browse Products</a>
        <a href="<?= url('contact/index') ?>" class="btn btn--outline btn--lg" style="margin-left:1rem;">Get in Touch</a>
    </div>
</section>
