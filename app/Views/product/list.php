<?php $pageTitle = 'Shop'; ?>

<div class="container shop-layout">

    <!-- ── Filters Sidebar ─────────────────────────── -->
    <aside class="shop-filters">
        <h3 class="shop-filters__title">Filters</h3>

        <form method="GET" action="<?= url('product/list') ?>">
            <div class="form-group">
                <label for="q">Search</label>
                <input type="text" id="q" name="q" value="<?= eXSS(($keyword ?? '')) ?>" placeholder="Search products…" class="form-control">
            </div>

            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category" class="form-control">
                    <option value="0">All Categories</option>
                    <?php foreach (($categories ?? []) as $cat): ?>
                        <option value="<?= eXSS($cat['Category_ID']) ?>"
                            <?= ($categoryId ?? 0) == $cat['Category_ID'] ? 'selected' : '' ?>>
                            <?= eXSS($cat['Name']) ?> (<?= eXSS($cat['product_count']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="sort">Sort By</label>
                <select id="sort" name="sort" class="form-control">
                    <option value="Product_ID" <?= ($sortBy ?? '') === 'Product_ID' ? 'selected' : '' ?>>Newest</option>
                    <option value="Price"      <?= ($sortBy ?? '') === 'Price'      ? 'selected' : '' ?>>Price</option>
                    <option value="Rating_No"  <?= ($sortBy ?? '') === 'Rating_No'  ? 'selected' : '' ?>>Rating</option>
                    <option value="Name"       <?= ($sortBy ?? '') === 'Name'       ? 'selected' : '' ?>>Name</option>
                </select>
            </div>

            <div class="form-group">
                <label>
                    <input type="radio" name="dir" value="DESC" <?= ($sortDir ?? '') === 'DESC' ? 'checked' : '' ?>> Descending
                </label>
                <label>
                    <input type="radio" name="dir" value="ASC"  <?= ($sortDir ?? '') === 'ASC'  ? 'checked' : '' ?>> Ascending
                </label>
            </div>

            <button type="submit" class="btn btn--primary btn--block">Apply</button>
            <a href="<?= url('product/list') ?>" class="btn btn--outline btn--block" style="margin-top:.5rem">Reset</a>
        </form>
    </aside>

    <!-- ── Product Grid ────────────────────────────── -->
    <main class="shop-main">
        <div class="shop-main__header">
            <p class="shop-main__count">
                <?= number_format(($paginator['total'] ?? '')) ?> product<?= ($paginator['total'] ?? '') !== 1 ? 's' : '' ?> found
            </p>
        </div>

        <?php if (empty($paginator['data'])): ?>
            <div class="empty-state">
                <p>No products found. Try adjusting your filters.</p>
                <a href="<?= url('product/list') ?>" class="btn btn--outline">Clear Filters</a>
            </div>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach ($paginator['data'] as $p): ?>
                    <?php include VIEW_PATH . '/product/_card.php'; ?>
                <?php endforeach; ?>
            </div>

            <?= \Core\View::pagination($paginator, url('product/list', [
                'q'        => ($keyword ?? ''),
                'category' => ($categoryId ?? ''),
                'sort'     => ($sortBy ?? ''),
                'dir'      => ($sortDir ?? ''),
            ])) ?>
        <?php endif; ?>
    </main>
</div>
