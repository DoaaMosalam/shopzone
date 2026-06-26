<?php $pageTitle = 'Coupons'; ?>

<div class="admin-card">
    <div class="admin-card__header">
        <h2>All Coupons</h2>
        <a href="<?= url('admin/coupon/create') ?>" class="btn btn--primary">+ New Coupon</a>
    </div>

    <p class="table-count"><?= number_format(($paginator['total'] ?? 0)) ?> coupons</p>

    <?php if (empty($paginator['data'])): ?>
        <p class="empty-note">No coupons found.</p>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Discount</th>
                    <th>Min Order</th>
                    <th>Valid Until</th>
                    <th>Usage</th>
                    <th>Status</th>
                    <th>Created By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($paginator['data'] as $cp): ?>
                <tr>
                    <td><code><?= eXSS($cp['Coupon_Code']) ?></code></td>
                    <td><?= money($cp['Discount_Value']) ?></td>
                    <td><?= money($cp['Min_Order_Value']) ?></td>
                    <td><?= fmt_date($cp['End_Date']) ?></td>
                    <td><?= eXSS($cp['Used_Count']) ?> / <?= eXSS($cp['Usage_Limit']) ?></td>
                    <td>
                        <?php $cls = $cp['Status'] === 'Active' ? 'badge-success' : 'badge-secondary'; ?>
                        <span class="badge <?= $cls ?>"><?= eXSS($cp['Status']) ?></span>
                    </td>
                    <td><?= eXSS($cp['Fname'] . ' ' . $cp['Lname']) ?></td>
                    <td class="table-actions">
                        <form method="POST" action="<?= url('admin/coupon/toggle/' . urlencode($cp['Coupon_Code'])) ?>"
                              style="display:inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn--outline btn--sm">
                                <?= $cp['Status'] === 'Active' ? 'Deactivate' : 'Activate' ?>
                            </button>
                        </form>
                        <form method="POST" action="<?= url('admin/coupon/delete/' . urlencode($cp['Coupon_Code'])) ?>"
                              style="display:inline"
                              onsubmit="return confirm('Delete coupon?')">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn--danger btn--sm">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?= \Core\View::pagination($paginator, url('admin/coupon/list')) ?>
    <?php endif; ?>
</div>
