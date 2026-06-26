<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – <?= eXSS($pageTitle ?? 'Dashboard') ?> | <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body class="admin-body">

<div class="admin-layout">

    <!-- ── Sidebar ──────────────────────────────────── -->
    <aside class="sidebar">
        <div class="sidebar__logo">
            <a href="<?= url('admin/dashboard') ?>"><?= APP_NAME ?> Admin</a>
        </div>

        <nav class="sidebar__nav">
            <a href="<?= url('admin/dashboard') ?>"    class="sidebar__link <?= \Core\View::activeClass('dashboard') ?>">
                📊 Dashboard
            </a>
            <a href="<?= url('admin/product/list') ?>" class="sidebar__link <?= \Core\View::activeClass('product') ?>">
                📦 Products
            </a>
            <a href="<?= url('admin/category/list') ?>" class="sidebar__link <?= \Core\View::activeClass('category') ?>">
                🗂 Categories
            </a>
            <a href="<?= url('admin/order/list') ?>"   class="sidebar__link <?= \Core\View::activeClass('order') ?>">
                🧾 Orders
            </a>
            <a href="<?= url('admin/user/list') ?>"    class="sidebar__link <?= \Core\View::activeClass('user') ?>">
                👤 Customers
            </a>
            <a href="<?= url('admin/coupon/list') ?>"  class="sidebar__link <?= \Core\View::activeClass('coupon') ?>">
                🎟 Coupons
            </a>
            <hr class="sidebar__divider">
            <a href="<?= url('admin/profile/index') ?>" class="sidebar__link <?= \Core\View::activeClass('profile') ?>">
                🧑‍💼 My Profile
            </a>
            <a href="<?= url('') ?>"             class="sidebar__link">← Back to Store</a>
            <a href="<?= url('auth/logout') ?>"  class="sidebar__link sidebar__link--danger">Logout</a>
        </nav>
    </aside>

    <!-- ── Main Area ─────────────────────────────────── -->
    <div class="admin-main">
        <header class="admin-topbar">
            <h1 class="admin-topbar__title"><?= eXSS($pageTitle ?? 'Dashboard') ?></h1>
            <a href="<?= url('admin/profile/index') ?>" class="admin-topbar__user" title="My Profile">
                <?php
                    $adminAvatar = $_SESSION['avatar'] ?? null;
                    if ($adminAvatar && file_exists(STORAGE_PATH . '/users/' . basename($adminAvatar))) {
                        $avatarUrl = APP_URL . '/storage/users/' . basename($adminAvatar);
                    } else {
                        $avatarUrl = asset('images/default-avatar.png');
                    }
                ?>
                <img src="<?= $avatarUrl ?>" alt="avatar" style="width:32px;height:32px;border-radius:50%;object-fit:cover;vertical-align:middle;margin-right:6px;">
                <?= eXSS($_SESSION['name'] ?? '') ?>
            </a>
        </header>

        <!-- Flash Messages -->
        <?php if (!empty($flash)): ?>
            <div class="flash-container">
                <?php foreach ($flash as $type => $msg): ?>
                    <div class="alert alert--<?= eXSS($type) ?>">
                        <?= $msg ?>
                        <button class="alert__close" onclick="this.parentElement.remove()">×</button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="admin-content">
            <?= ($content ?? '') ?>
        </div>
    </div>

</div>

<script src="<?= asset('js/main.js') ?>"></script>
<script src="<?= asset('js/admin.js') ?>"></script>
</body>
</html>
