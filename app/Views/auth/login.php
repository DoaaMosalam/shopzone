<?php $pageTitle = 'Login'; ?>

<h2 class="auth-card__title">Welcome Back</h2>
<p class="auth-card__subtitle">Sign in to your account</p>

<?php if (!empty($flash['error'])): ?>
    <div class="alert alert--danger" style="margin-bottom:16px; padding:12px 16px; border-radius:6px; background:#f8d7da; color:#721c24; border:1px solid #f5c6cb;">
        <?= eXSS($flash['error']) ?>
    </div>
<?php endif; ?>

<?php if (!empty($flash['banned'])): ?>
    <div class="alert alert--banned" style="margin-bottom:16px; padding:14px 16px; border-radius:6px; background:#fff3cd; color:#856404; border:1px solid #ffc107;">
        <strong>&#128683; Account Banned</strong><br>
        <?= eXSS($flash['banned']) ?>
    </div>
<?php endif; ?>

<?php if (!empty($flash['success'])): ?>
    <div class="alert alert--success" style="margin-bottom:16px; padding:12px 16px; border-radius:6px; background:#d4edda; color:#155724; border:1px solid #c3e6cb;">
        <?= eXSS($flash['success']) ?>
    </div>
<?php endif; ?>

<form method="POST" action="<?= url('auth/login/store') ?>">
    <?= csrf_field() ?>

    <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" class="form-control" required autofocus placeholder="you@example.com">
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••">
    </div>

    <button type="submit" class="btn btn--primary btn--block">Sign In</button>
</form>

<p class="auth-card__footer">
    Don't have an account? <a href="<?= url('auth/register') ?>">Register</a>
</p>
