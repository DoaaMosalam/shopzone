<?php $pageTitle = 'Categories'; ?>

<div class="admin-card">
    <div class="admin-card__header">
        <h2>All Categories</h2>
        <a href="<?= url('admin/category/create') ?>" class="btn btn--primary">+ New Category</a>
    </div>

    <?php if (empty($categories)): ?>
        <p class="empty-note">No categories yet.</p>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Products</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                <tr>
                    <td><?= eXSS($cat['Category_ID']) ?></td>
                    <td>
                        <?php if ($cat['Image']): ?>
                            <img src="<?= url('storage/' . eXSS($cat['Image'])) ?>" alt="" class="table-thumb">
                        <?php else: ?>
                            <span class="no-image">—</span>
                        <?php endif; ?>
                    </td>
                    <td><?= eXSS($cat['Name']) ?></td>
                    <td><?= eXSS($cat['product_count']) ?></td>
                    <td class="table-actions">
                        <a href="<?= url('admin/category/edit/' . $cat['Category_ID']) ?>"
                           class="btn btn--outline btn--sm">Edit</a>
                        <form method="POST" action="<?= url('admin/category/delete/' . $cat['Category_ID']) ?>"
                              style="display:inline"
                              onsubmit="return confirm('Delete this category?')">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn--danger btn--sm">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
