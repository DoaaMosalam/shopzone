<?php $pageTitle = 'Customers'; ?>

<div class="admin-card">
    <div class="admin-card__header">
        <h2>All Customers</h2>
    </div>

    <p class="table-count"><?= number_format(($paginator['total'] ?? '')) ?> customers</p>

    <?php if (empty($paginator['data'])): ?>
        <p class="empty-note">No customers found.</p>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($paginator['data'] as $c): ?>
                <tr>
                    <td><?= eXSS($c['Customer_ID']) ?></td>
                    <td><?= eXSS($c['Fname'] . ' ' . $c['Lname']) ?></td>
                    <td><?= eXSS($c['Email']) ?></td>
                    <td>
                        <?php
                        $statusClass = match($c['Account_Status']) {
                            'Active'    => 'badge-success',
                            'Suspended' => 'badge-warning',
                            'Banned'    => 'badge-danger',
                            default     => 'badge-secondary',
                        };
                        ?>
                        <span class="badge <?= $statusClass ?>"><?= eXSS($c['Account_Status']) ?></span>
                    </td>
                    <td><?= fmt_date($c['Created_At']) ?></td>
                    <td>
                        <a href="<?= url('admin/user/detail/' . $c['Customer_ID']) ?>"
                           class="btn btn--outline btn--sm">View</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?= \Core\View::pagination($paginator, url('admin/user/list')) ?>
    <?php endif; ?>
</div>
