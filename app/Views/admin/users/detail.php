<?php $pageTitle = 'Customer – ' . ($customer['Fname'] ?? '') . ' ' . ($customer['Lname'] ?? ''); ?>

<div class="admin-card">
    <div class="admin-card__header">
        <h2><?= eXSS(($customer['Fname'] ?? '') . ' ' . ($customer['Lname'] ?? '')) ?></h2>
        <a href="<?= url('admin/user/list') ?>" class="btn btn--outline">← Back</a>
    </div>

    <div class="order-detail-grid">
        <div>
            <dl class="info-list">
                <dt>Email</dt>        <dd><?= eXSS(($customer['Email'] ?? '')) ?></dd>
                <dt>Gender</dt>       <dd><?= eXSS(($customer['Gender'] ?? '')) ?></dd>
                <dt>Date of Birth</dt><dd><?= fmt_date(($customer['Date_of_Birth'] ?? '')) ?></dd>
                <dt>Joined</dt>       <dd><?= fmt_date(($customer['Created_At'] ?? '')) ?></dd>
                <dt>City</dt>         <dd><?= eXSS($customer['City'] ?? '—') ?></dd>
                <dt>Address</dt>      <dd><?= eXSS(($customer['Street'] ?? '') . ', ' . ($customer['State'] ?? '')) ?></dd>
                <dt>ZIP</dt>          <dd><?= eXSS($customer['Zip_Code'] ?? '—') ?></dd>
            </dl>
        </div>

        <div>
            <h3>Account Status</h3>

            <?php
                $currentStatus = $customer['Account_Status'] ?? 'Active';
                $banUntil      = $customer['Ban_Until'] ?? null;
            ?>

            <p>
                Current:
                <strong
                    class="<?php
                        if ($currentStatus === 'Banned')     echo 'text-danger';
                        elseif ($currentStatus === 'Suspended') echo 'text-warning';
                        else echo 'text-success';
                    ?>"
                ><?= eXSS($currentStatus) ?></strong>

                <?php if ($currentStatus === 'Banned' && $banUntil): ?>
                    &mdash;
                    <?php if (strtotime($banUntil) > time()): ?>
                        <span style="color:#dc3545;">
                            Ban expires on <strong><?= eXSS(date('d M Y', strtotime($banUntil))) ?></strong>
                        </span>
                    <?php else: ?>
                        <span style="color:#28a745;">Ban period has ended (will lift on next login).</span>
                    <?php endif; ?>
                <?php elseif ($currentStatus === 'Banned'): ?>
                    &mdash; <span style="color:#dc3545;">Permanent ban</span>
                <?php endif; ?>
            </p>

            <form method="POST" action="<?= url('admin/user/set-status/' . ($customer['Customer_ID'] ?? '')) ?>">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="status">New Status</label>
                    <select name="status" id="status" class="form-control" onchange="toggleBanOptions(this.value)">
                        <?php foreach (['Active', 'Suspended', 'Banned'] as $s): ?>
                            <option value="<?= eXSS($s) ?>" <?= $currentStatus === $s ? 'selected' : '' ?>>
                                <?= eXSS($s) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Ban duration options (shown only when Banned is selected) -->
                <div id="ban-options" style="display:<?= $currentStatus === 'Banned' ? 'block' : 'none' ?>; border-left:3px solid #dc3545; padding-left:12px; margin-bottom:12px;">
                    <p style="font-size:0.875rem; color:#6c757d; margin-bottom:8px;">
                        Leave both fields empty for a <strong>permanent ban</strong>.
                    </p>

                    <div class="form-group">
                        <label for="ban_days">Ban Duration (Days)</label>
                        <input
                            type="number"
                            id="ban_days"
                            name="ban_days"
                            class="form-control"
                            min="1"
                            max="3650"
                            placeholder="e.g. 7 (leave empty for permanent or use date below)"
                            value=""
                        >
                    </div>

                    <div class="form-group">
                        <label for="ban_until_date">— Or Ban Until Specific Date</label>
                        <input
                            type="date"
                            id="ban_until_date"
                            name="ban_until_date"
                            class="form-control"
                            min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                            value="<?= ($banUntil && $currentStatus === 'Banned') ? eXSS(date('Y-m-d', strtotime($banUntil))) : '' ?>"
                        >
                        <small style="color:#6c757d;">
                            If both fields are filled, the <em>number of days</em> takes priority.
                        </small>
                    </div>
                </div>

                <button type="submit" class="btn btn--primary">Update Status</button>
            </form>
        </div>
    </div>
</div>

<script>
function toggleBanOptions(status) {
    document.getElementById('ban-options').style.display =
        status === 'Banned' ? 'block' : 'none';
}
</script>
