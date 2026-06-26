<?php $pageTitle = 'My Profile'; ?>

<div class="profile-layout">

    <!-- ── Personal Info ───────────────────────── -->
    <section class="profile-card">
        <h2>Personal Information</h2>
        <form method="POST" action="<?= url('admin/profile/update') ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="profile-avatar" style="text-align:center;margin-bottom:20px;">
                <?php
                    $img = $admin['Profile_Img'] ?? null;
                    if ($img && (str_starts_with($img, 'http://') || str_starts_with($img, 'https://'))) {
                        $avatarSrc = $img;
                    } elseif ($img && file_exists(STORAGE_PATH . '/' . $img)) {
                        $avatarSrc = APP_URL . '/storage/' . $img;
                    } else {
                        $avatarSrc = asset('images/default-avatar.png');
                    }
                ?>
                <img src="<?= $avatarSrc ?>"
                     alt="Avatar"
                     class="profile-avatar__img"
                     id="avatarPreview"
                     style="width:120px;height:120px;border-radius:50%;object-fit:cover;border:3px solid #3498db;display:block;margin:0 auto 15px;box-shadow:0 4px 10px rgba(0,0,0,.1);">
                <label for="avatar" class="btn btn--outline btn--sm" style="cursor:pointer;padding:6px 15px;">
                    Change Photo
                </label>
                <input type="file" id="avatar" name="avatar" accept="image/*" hidden
                       onchange="(function(i){var r=new FileReader();r.onload=function(e){document.getElementById('avatarPreview').src=e.target.result};r.readAsDataURL(i.files[0])})(this)">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="fname">First Name</label>
                    <input type="text" id="fname" name="fname" class="form-control"
                           value="<?= eXSS($admin['Fname'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="mname">Middle Name</label>
                    <input type="text" id="mname" name="mname" class="form-control"
                           value="<?= eXSS($admin['Mname'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="lname">Last Name</label>
                    <input type="text" id="lname" name="lname" class="form-control"
                           value="<?= eXSS($admin['Lname'] ?? '') ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" value="<?= eXSS($admin['Email'] ?? '') ?>" disabled>
                <small style="color:#888">Email cannot be changed here.</small>
            </div>

            <button type="submit" class="btn btn--primary">Save Changes</button>
        </form>
    </section>

    <!-- ── Change Password ─────────────────────── -->
    <section class="profile-card">
        <h2>Change Password</h2>
        <form method="POST" action="<?= url('admin/profile/change-password') ?>">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" class="form-control" required minlength="8">
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn--primary">Change Password</button>
        </form>
    </section>

</div>
