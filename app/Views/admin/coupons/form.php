<?php $pageTitle = 'New Coupon'; ?>

<div class="admin-card">
    <div class="admin-card__header">
        <h2>Create New Coupon</h2>
        <a href="<?= url('admin/coupon/list') ?>" class="btn btn--outline">← Back</a>
    </div>

    <form method="POST" action="<?= url('admin/coupon/store') ?>">
        <?= csrf_field() ?>

        <div class="form-grid-2">
            <div class="form-group">
                <label for="coupon_code">Coupon Code *</label>
                <input type="text" id="coupon_code" name="coupon_code" class="form-control"
                    required placeholder="e.g. SUMMER25" style="text-transform:uppercase">
            </div>

            <div class="form-group">
                <label for="discount">Discount Value (EGP) *</label>
                <input type="number" id="discount" name="discount" class="form-control"
                    step="0.01" required min="1" placeholder="50.00">
            </div>

            <div class="form-group">
                <label for="min_order">Minimum Order Value (EGP)</label>
                <input type="number" id="min_order" name="min_order" class="form-control"
                    step="0.01" min="0" value="0">
            </div>

            <div class="form-group">
                <label for="usage_limit">Usage Limit</label>
                <input type="number" id="usage_limit" name="usage_limit" class="form-control"
                    min="1" value="100">
            </div>

            <div class="form-group">
                <label for="start_date">Start Date *</label>
                <input type="date" id="start_date" name="start_date" class="form-control"
                    value="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="form-group">
                <label for="end_date">End Date *</label>
                <input type="date" id="end_date" name="end_date" class="form-control"
                    value="<?= date('Y-m-d', strtotime('+30 days')) ?>" required>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn--primary">Create Coupon</button>
            <a href="<?= url('admin/coupon/list') ?>" class="btn btn--ghost">Cancel</a>
        </div>
    </form>
</div>
