<?php $pageTitle = ($category ?? null) ? 'Edit Category' : 'New Category';
$isEdit = ($category ?? null) !== null;
$action = $isEdit ? url('admin/category/update/' . ($category['Category_ID'] ?? '')) : url('admin/category/store');
?>

<div class="admin-card">
    <div class="admin-card__header">
        <h2><?= $isEdit ? 'Edit Category' : 'Add New Category' ?></h2>
        <a href="<?= url('admin/category/list') ?>" class="btn btn--outline">← Back</a>
    </div>

    <form method="POST" action="<?= $action ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="name">Category Name *</label>
            <input type="text" id="name" name="name" class="form-control" required
                value="<?= eXSS($category['Name'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="image">Category Image</label>
            <?php if (!empty($category['Image'])): ?>
                <div class="current-image">
                    <img src="<?= url('storage/' . eXSS($category['Image'])) ?>" alt="" style="max-height:100px;">
                </div>
            <?php endif; ?>
            <input type="file" id="image" name="image" accept="image/*" class="form-control">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn--primary"><?= $isEdit ? 'Update' : 'Create' ?> Category</button>
            <a href="<?= url('admin/category/list') ?>" class="btn btn--ghost">Cancel</a>
        </div>
    </form>
</div>
