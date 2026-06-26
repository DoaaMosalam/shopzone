<?php $pageTitle = 'Products'; ?>

<div class="admin-card">
    <div class="admin-card__header">
        <h2>All Products</h2>
        <a href="<?= url('admin/product/create') ?>" class="btn btn--primary">+ New Product</a>
    </div>

    <!-- Filters -->
    <form method="GET" action="<?= url('admin/product/list') ?>" class="admin-filter-bar">
        <input type="text" name="q" value="<?= eXSS($_GET['q'] ?? '') ?>"
               class="form-control" placeholder="Search products…">
        <select name="category" class="form-control">
            <option value="0">All Categories</option>
            <?php foreach (($categories ?? []) as $cat): ?>
                <option value="<?= eXSS($cat['Category_ID']) ?>"
                    <?= (($_GET['category'] ?? 0) == $cat['Category_ID']) ? 'selected' : '' ?>>
                    <?= eXSS($cat['Name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn--outline">Filter</button>
        <a href="<?= url('admin/product/list') ?>" class="btn btn--ghost">Reset</a>
    </form>

    <p class="table-count"><?= number_format(($paginator['total'] ?? '')) ?> products</p>

    <?php if (empty($paginator['data'])): ?>
        <p class="empty-note">No products found.</p>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Rating</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($paginator['data'] as $p): ?>
                <tr>
                    <td><?= eXSS($p['Product_ID']) ?></td>
                    <td>
                        <img src="<?= product_image($p['Image_URL'] ?? null) ?>"
                             alt="" class="table-thumb">
                    </td>
                    <td><?= eXSS($p['Name']) ?></td>
                    <td><?= eXSS($p['Category_Name'] ?? '—') ?></td>
                    <td><?= money($p['Price']) ?></td>
                    <td>
                        <?php if ((int)$p['Product_Quantity'] < 5): ?>
                            <span class="badge badge-danger"><?= eXSS($p['Product_Quantity']) ?></span>
                        <?php else: ?>
                            <?= eXSS($p['Product_Quantity']) ?>
                        <?php endif; ?>
                    </td>
                    <td><?= number_format((float)$p['Rating_No'], 1) ?></td>
                    <td class="table-actions">
                        <a href="<?= url('admin/product/edit/' . $p['Product_ID']) ?>"
                           class="btn btn--outline btn--sm">Edit</a>
                        <form method="POST" action="<?= url('admin/product/delete/' . $p['Product_ID']) ?>"
                              style="display:inline"
                              onsubmit="return confirm('Delete this product?')">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn--danger btn--sm">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?= \Core\View::pagination($paginator, url('admin/product/list', ['q' => $_GET['q'] ?? '', 'category' => $_GET['category'] ?? 0])) ?>
    <?php endif; ?>
</div>
