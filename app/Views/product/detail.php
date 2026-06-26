<?php $pageTitle = eXSS(($product['Name'] ?? '')); ?>

<div class="container">

    <!-- ── Breadcrumb ─────────────────────────────── -->
    <nav class="breadcrumb">
        <a href="<?= url('') ?>">Home</a> /
        <a href="<?= url('product/list') ?>">Shop</a> /
        <?php if (!empty($product['Category_Name'])): ?>
            <a href="<?= url('product/list', ['category' => $product['Category_ID']]) ?>"><?= eXSS($product['Category_Name']) ?></a> /
        <?php endif; ?>
        <span><?= eXSS(($product['Name'] ?? '')) ?></span>
    </nav>

    <!-- ── Product Detail ─────────────────────────── -->
    <div class="product-detail">
        <div class="product-detail__gallery">
            <img
                src="<?= eXSS(product_image($product['Image_URL'] ?? null)) ?>"
                alt="<?= eXSS($product['Name']) ?>"
                class="product-card__img"
                loading="lazy"
                onerror="this.onerror=null;this.src='<?= asset('images/no-image.svg') ?>';"
            >
        </div>

        <div class="product-detail__info">
            <?php if (!empty($product['Brand'])): ?>
                <span class="product-detail__brand"><?= eXSS($product['Brand']) ?></span>
            <?php endif; ?>

            <h1 class="product-detail__title"><?= eXSS(($product['Name'] ?? '')) ?></h1>

            <div class="product-detail__rating">
                <?= \Core\View::stars((float) ($product['Rating_No'] ?? 0)) ?>
                <span>(<?= number_format((float) ($product['Rating_No'] ?? 0), 1) ?>/5)</span>
            </div>

            <p class="product-detail__price"><?= money(($product['Price'] ?? 0)) ?></p>

            <?php if (!empty($product['Description'])): ?>
                <p class="product-detail__desc"><?= nl2br(eXSS($product['Description'])) ?></p>
            <?php endif; ?>

            <?php if ((int) ($product['Product_Quantity'] ?? 0) > 0): ?>
                <p class="product-detail__stock stock--in">✔ In Stock (<?= eXSS(($product['Product_Quantity'] ?? '')) ?> available)</p>
                <form method="POST" action="<?= url('cart/add') ?>" class="product-detail__form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="product_id" value="<?= eXSS(($product['Product_ID'] ?? '')) ?>">
                    <div class="form-row">
                        <input type="number" name="qty" value="1" min="1" max="<?= eXSS(($product['Product_Quantity'] ?? '')) ?>" class="form-control qty-input">
                        <button type="submit" class="btn btn--primary">Add to Cart</button>
                    </div>
                </form>

                <form method="POST" action="<?= url('wishlist/toggle') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="product_id" value="<?= eXSS(($product['Product_ID'] ?? '')) ?>">
                    <button type="submit" class="btn btn--outline btn--sm">♡ Add to Wishlist</button>
                </form>
            <?php else: ?>
                <p class="product-detail__stock stock--out">✘ Out of Stock</p>
            <?php endif; ?>

            <!-- Specifications -->
            <?php if (!empty($product['specs'])): ?>
            <div class="product-detail__specs">
                <h3>Specifications</h3>
                <table class="specs-table">
                    <?php foreach ($product['specs'] as $spec): ?>
                    <tr>
                        <th><?= eXSS($spec['Spec_Key']) ?></th>
                        <td><?= eXSS($spec['Spec_Value']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ── Reviews ────────────────────────────────── -->
    <section class="reviews-section">
        <h2>Customer Reviews</h2>

        <?php if (!empty($reviews)): ?>
            <div class="reviews-list">
                <?php foreach ($reviews as $r): ?>

                <!-- <div class="review-card">
                    <div class="review-card__header">
                        <img src="<?= product_image($r['Profile_Img'] ?? null) ?>" alt="" class="review-card__avatar">
                        <div>
                            <strong><?= eXSS($r['Fname'] . ' ' . $r['Lname']) ?></strong>
                            <?php if ($r['AI_Rating']): ?>
                                <span class="review-card__rating"><?= \Core\View::stars((float) $r['AI_Rating']) ?></span>
                            <?php endif; ?>
                            <?php if ($r['AI_Sentiment']): ?>
                                <span class="badge badge--<?= strtolower(eXSS($r['AI_Sentiment'])) ?>"><?= eXSS($r['AI_Sentiment']) ?></span>
                            <?php endif; ?>
                        </div>
                        <time class="review-card__date"><?= fmt_date($r['Created_At']) ?></time>
                    </div>
                    <p class="review-card__comment"><?= nl2br(eXSS($r['Comment'])) ?></p>
                </div> -->

                <div class="review-card">
                    <div class="review-card__header" style="display: flex; align-items: center; gap: 15px; margin-bottom: 12px;">
                        
                        <img src="<?= eXSS(product_image($r['Profile_Img'] ?? null)) ?>"
                            alt="<?= eXSS($r['Fname']) ?>"
                            class="review-card__avatar"
                            style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #e2e8f0; display: block;"
                            loading="lazy">
                        
                        <div style="flex: 1;">
                            <strong style="display: block; color: #2c3e50; font-size: 1rem; margin-bottom: 4px;">
                                <?= eXSS($r['Fname'] . ' ' . $r['Lname']) ?>
                            </strong>
                            
                            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                <?php if ($r['AI_Rating']): ?>
                                    <span class="review-card__rating" style="color: #f1c40f; letter-spacing: 1px;">
                                        <?= \Core\View::stars((float) $r['AI_Rating']) ?>
                                    </span>
                                <?php endif; ?>
                                
                                <?php if ($r['AI_Sentiment']): ?>
                                    <span class="badge badge--<?= strtolower(eXSS($r['AI_Sentiment'])) ?>"><?= eXSS($r['AI_Sentiment']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <time class="review-card__date" style="color: #95a5a6; font-size: 0.85rem; align-self: flex-start;"><?= fmt_date($r['Created_At']) ?></time>
                    </div>
    
                    <p class="review-card__comment" style="line-height: 1.6; color: #34495e; margin: 0; padding-right: 60px;"><?= nl2br(eXSS($r['Comment'])) ?></p>
                </div>

                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="empty-state">No reviews yet. Be the first to review!</p>
        <?php endif; ?>

        <!-- Submit Review -->
        <?php if (is_logged_in() && !is_admin() && !($userReviewed ?? '')): ?>
        <div class="review-form">
            <h3>Write a Review</h3>
            <form method="POST" action="<?= url('review/submit') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= eXSS(($product['Product_ID'] ?? '')) ?>">
                <div class="form-group">
                    <textarea name="comment" rows="4" class="form-control" placeholder="Share your experience…" required></textarea>
                </div>
                <button type="submit" class="btn btn--primary">Submit Review</button>
            </form>
        </div>
        <?php elseif ($userReviewed ?? ''): ?>
            <p class="info-note">You have already reviewed this product.</p>
        <?php elseif (!is_logged_in()): ?>
            <p class="info-note"><a href="<?= url('auth/login') ?>">Log in</a> to leave a review.</p>
        <?php endif; ?>
    </section>

    <!-- ── Related Products ───────────────────────── -->
    <?php if (!empty($related)): ?>
    <section class="section">
        <h2 class="section__title">Related Products</h2>
        <div class="products-grid">
            <?php foreach ($related as $p): ?>
                <?php if ($p['Product_ID'] != ($product['Product_ID'] ?? '')): ?>
                    <?php include VIEW_PATH . '/product/_card.php'; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</div>
