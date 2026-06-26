<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= eXSS($pageTitle ?? APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <link rel="shortcut icon" type="image/png" href="<?= asset('favicon.png?v=1') ?>">
</head>
<body>

<!-- ── Navigation ─────────────────────────────────────── -->
<header class="navbar">
    <div class="container navbar__inner">
        <a href="<?= url('') ?>" class="navbar__logo"><?= APP_NAME ?></a>

        <nav class="navbar__links">
            <a href="<?= url('') ?>"             class="<?= \Core\View::activeClass('/') ?>">Home</a>
            <a href="<?= url('product/list') ?>"  class="<?= \Core\View::activeClass('product') ?>">Shop</a>
            <a href="<?= url('compare/index') ?>" class="<?= \Core\View::activeClass('compare') ?>">Compare</a>
            <a href="<?= url('about/index') ?>"   class="<?= \Core\View::activeClass('about') ?>">About Us</a>
            <a href="<?= url('contact/index') ?>" class="<?= \Core\View::activeClass('contact') ?>">Contact</a>
        </nav>

        <div class="navbar__actions">
            <?php if (is_logged_in()): ?>
                <?php if (!is_admin()): ?>
                    <a href="<?= url('cart/index') ?>"     class="btn-icon" title="Cart">
                        🛒 Cart
                    </a>
                    <a href="<?= url('wishlist/index') ?>" class="btn-icon" title="Wishlist">
                        ♡ Wishlist
                    </a>
                <?php endif; ?>
                <div class="dropdown">
                    <button class="btn-icon dropdown__toggle">
                        <?= eXSS($_SESSION['name']) ?> ▾
                    </button>
                    <ul class="dropdown__menu">
                        <?php if (is_admin()): ?>
                            <li><a href="<?= url('admin/dashboard') ?>">📊 Dashboard</a></li>
                            <li><a href="<?= url('admin/profile/index') ?>">👤 My Profile</a></li>
                        <?php else: ?>
                            <li><a href="<?= url('profile/index') ?>">My Profile</a></li>
                            <li><a href="<?= url('order/list') ?>">My Orders</a></li>
                        <?php endif; ?>
                        <li class="dropdown__divider"></li>
                        <li><a href="<?= url('auth/logout') ?>">Logout</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <a href="<?= url('auth/login') ?>"    class="btn btn--outline btn--sm">Login</a>
                <a href="<?= url('auth/register') ?>" class="btn btn--primary btn--sm">Register</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- ── Flash Messages ─────────────────────────────────── -->
<?php if (!empty($flash)): ?>
    <div class="container flash-container">
        <?php foreach ($flash as $type => $msg): ?>
            <div class="alert alert--<?= eXSS($type) ?>">
                <?= $msg ?>
                <button class="alert__close" onclick="this.parentElement.remove()">×</button>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- ── Main Content ───────────────────────────────────── -->
<main class="main-content">
    <?= ($content ?? '') ?>
</main>

<!-- ── Footer ─────────────────────────────────────────── -->
<footer class="footer">
    <div class="container">
        <div class="footer__top">
            <div class="footer__brand">
                <span class="footer__logo"><?= APP_NAME ?></span>
                <p class="footer__tagline">Quality tech products at great prices, delivered fast to your door.</p>
                <div class="footer__social">
                    <a href="#" class="footer__social-link" title="Facebook">📘</a>
                    <a href="#" class="footer__social-link" title="Twitter">🐦</a>
                    <a href="#" class="footer__social-link" title="Instagram">📸</a>
                    <a href="#" class="footer__social-link" title="YouTube">▶️</a>
                </div>
            </div>
            <div class="footer__col">
                <h4 class="footer__col-title">Shop</h4>
                <ul class="footer__col-links">
                    <li><a href="<?= url('product/list') ?>">All Products</a></li>
                    <li><a href="<?= url('product/list', ['sort' => 'Release_Date', 'dir' => 'DESC']) ?>">New Arrivals</a></li>
                    <li><a href="<?= url('product/list', ['sort' => 'Rating_No', 'dir' => 'DESC']) ?>">Best Sellers</a></li>
                    <li><a href="<?= url('compare/index') ?>">Compare Products</a></li>
                </ul>
            </div>
            <div class="footer__col">
                <h4 class="footer__col-title">Customer Care</h4>
                <ul class="footer__col-links">
                    <li><a href="<?= url('contact/index') ?>">Contact Us</a></li>
                    <li><a href="<?= url('order/list') ?>">Track Order</a></li>
                    <li><a href="#">Returns &amp; Refunds</a></li>
                    <li><a href="#">FAQ</a></li>
                </ul>
            </div>
            <div class="footer__col">
                <h4 class="footer__col-title">Company</h4>
                <ul class="footer__col-links">
                    <li><a href="<?= url('about/index') ?>">About Us</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Service</a></li>
                    <li><a href="#">Careers</a></li>
                </ul>
            </div>
        </div>
        <div class="footer__bottom">
            <p>&copy; <?= date('Y') ?> <?= APP_NAME ?>. All rights reserved.</p>
            <div class="footer__payment">
                <span class="payment-badge">💳 Visa</span>
                <span class="payment-badge">💳 Mastercard</span>
                <span class="payment-badge">📱 Apple Pay</span>
            </div>
        </div>
    </div>
</footer>

<!-- ── Scroll to Top ───────────────────────────────────── -->
<button id="scrollTop" class="scroll-top-btn" title="Back to top" aria-label="Scroll to top">↑</button>

<script src="<?= asset('js/main.js') ?>"></script>
</body>
</html>
