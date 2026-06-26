<?php $pageTitle = 'Register'; ?>

<h2 class="auth-card__title">Create an Account</h2>
<p class="auth-card__subtitle">Join <?= APP_NAME ?> today</p>

<form method="POST" action="<?= url('auth/register/store') ?>">
    <?= csrf_field() ?>

    <div class="form-row">
        <div class="form-group">
            <label for="fname">First Name</label>
            <input type="text" id="fname" name="fname" class="form-control" required placeholder="Ahmed">
        </div>
        <div class="form-group">
            <label for="lname">Last Name</label>
            <input type="text" id="lname" name="lname" class="form-control" required placeholder="Ali">
        </div>
    </div>

    <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" class="form-control" required placeholder="you@example.com">
    </div>

    <div class="form-group">
        <label for="phone">Phone Number</label>
        <input type="tel" id="phone" name="phone" class="form-control" placeholder="+20 1xx xxx xxxx">
    </div>

    <div class="form-group">
        <label for="gender">Gender</label>
        <select id="gender" name="gender" class="form-control">
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select>
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" class="form-control" required minlength="8" placeholder="Min. 8 characters">
    </div>

    <div class="form-group">
        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required placeholder="Repeat password">
    </div>

    <button type="submit" class="btn btn--primary btn--block">Create Account</button>
</form>

<p class="auth-card__footer">
    Already have an account? <a href="<?= url('auth/login') ?>">Sign In</a>
</p>
