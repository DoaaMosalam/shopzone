<?php
// Fix: use strict null check so the create form correctly shows "New Product" / "Create Product"
$isEdit   = isset($product) && $product !== null;
$pageTitle = $isEdit ? 'Edit Product' : 'New Product';
$action    = $isEdit
    ? url('admin/product/update/' . ($product['Product_ID'] ?? ''))
    : url('admin/product/store');
?>

<div class="admin-card">
    <div class="admin-card__header">
        <h2><?= $isEdit ? 'Edit Product' : 'Add New Product' ?></h2>
        <a href="<?= url('admin/product/list') ?>" class="btn btn--outline">← Back</a>
    </div>

    <form method="POST" action="<?= $action ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="form-grid-2">
            <div class="form-group">
                <label for="name">Product Name *</label>
                <input type="text" id="name" name="name" class="form-control" required
                    value="<?= eXSS($product['Name'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="brand">Brand</label>
                <input type="text" id="brand" name="brand" class="form-control"
                    value="<?= eXSS($product['Brand'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="price">Price (EGP) *</label>
                <input type="number" id="price" name="price" class="form-control" step="0.01" required
                    value="<?= eXSS($product['Price'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="quantity">Stock Quantity *</label>
                <input type="number" id="quantity" name="quantity" class="form-control" required
                    value="<?= eXSS($product['Product_Quantity'] ?? 0) ?>">
            </div>

            <div class="form-group">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id" class="form-control">
                    <option value="">— No Category —</option>
                    <?php foreach (($categories ?? []) as $cat): ?>
                        <option value="<?= eXSS($cat['Category_ID']) ?>"
                            <?= (($product['Category_ID'] ?? '') == $cat['Category_ID']) ? 'selected' : '' ?>>
                            <?= eXSS($cat['Name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="release_date">Release Date</label>
                <input type="date" id="release_date" name="release_date" class="form-control"
                    value="<?= eXSS($product['Release_Date'] ?? '') ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4" class="form-control"><?= eXSS($product['Description'] ?? '') ?></textarea>
        </div>

        <!-- Image Upload -->
        <div class="form-group">
            <label for="image">Product Image</label>
            <?php if (!empty($product['Image_URL'])): ?>
                <div class="current-image">
                    <img src="<?= product_image($product['Image_URL']) ?>" alt="Current image" style="max-height:120px; border-radius:6px;">
                    <p class="hint">Upload new to replace current image.</p>
                </div>
            <?php endif; ?>
            <input type="file" id="image" name="image" accept="image/*" class="form-control">
        </div>

        <!-- Specifications -->
        <div class="form-group">
            <label>Specifications</label>
            <div id="specs-container">
                <?php if (!empty($product['specs'])): ?>
                    <?php foreach ($product['specs'] as $spec): ?>
                    <div class="spec-row">
                        <input type="text" name="spec_key[]" placeholder="Key (e.g. RAM)"
                            class="form-control" value="<?= eXSS($spec['Spec_Key']) ?>">
                        <input type="text" name="spec_value[]" placeholder="Value (e.g. 16GB)"
                            class="form-control" value="<?= eXSS($spec['Spec_Value']) ?>">
                        <button type="button" class="btn btn--danger btn--sm spec-remove">✕</button>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button type="button" id="add-spec" class="btn btn--outline btn--sm">+ Add Specification</button>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn--primary"><?= $isEdit ? 'Update Product' : 'Create Product' ?></button>
            <a href="<?= url('admin/product/list') ?>" class="btn btn--ghost">Cancel</a>
        </div>
    </form>
</div>

<script>
document.getElementById('add-spec').addEventListener('click', function() {
    const container = document.getElementById('specs-container');
    const row = document.createElement('div');
    row.className = 'spec-row';
    row.innerHTML = `
        <input type="text" name="spec_key[]" placeholder="Key" class="form-control">
        <input type="text" name="spec_value[]" placeholder="Value" class="form-control">
        <button type="button" class="btn btn--danger btn--sm spec-remove">✕</button>
    `;
    container.appendChild(row);
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('spec-remove')) {
        e.target.closest('.spec-row').remove();
    }
});
</script>
