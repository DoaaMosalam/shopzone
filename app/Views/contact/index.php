<?php $pageTitle = 'Contact Us – ' . APP_NAME; ?>

<!-- ── Contact Hero ─────────────────────── -->
<section class="page-hero">
    <div class="container page-hero__inner">
        <h1 class="page-hero__title">Contact Us</h1>
        <p class="page-hero__subtitle">We'd love to hear from you. Send us a message and we'll respond within 24 hours.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="contact-layout">

            <!-- Contact Info -->
            <div class="contact-info">
                <h2 class="contact-info__title">Get In Touch</h2>
                <p class="contact-info__desc">Have a question about your order, need product advice, or just want to say hello? We're here to help!</p>

                <div class="contact-info__items">
                    <div class="contact-info__item">
                        <span class="contact-info__icon">📍</span>
                        <div>
                            <strong>Address</strong><br>
                            123 Tech Street, Maadi<br>
                            Cairo, Egypt 11431
                        </div>
                    </div>
                    <div class="contact-info__item">
                        <span class="contact-info__icon">📞</span>
                        <div>
                            <strong>Phone</strong><br>
                            +20 100 123 4567<br>
                            <small style="color:var(--color-text-muted);">Sun – Thu, 9am – 6pm</small>
                        </div>
                    </div>
                    <div class="contact-info__item">
                        <span class="contact-info__icon">✉️</span>
                        <div>
                            <strong>Email</strong><br>
                            support@shopzone.eg<br>
                            <small style="color:var(--color-text-muted);">We reply within 24 hours</small>
                        </div>
                    </div>
                    <div class="contact-info__item">
                        <span class="contact-info__icon">💬</span>
                        <div>
                            <strong>Live Chat</strong><br>
                            Available on our website<br>
                            <small style="color:var(--color-text-muted);">Daily, 8am – 10pm</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="contact-form-card">
                <h2 class="contact-form-card__title">Send a Message</h2>

                <?php if (!empty($flash)): ?>
                    <?php foreach ($flash as $type => $msg): ?>
                        <div class="alert alert--<?= eXSS($type) ?>" style="margin-bottom:1rem;">
                            <?= eXSS($msg) ?>
                            <button class="alert__close" onclick="this.parentElement.remove()">×</button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <form method="POST" action="<?= url('contact/index') ?>">
                    <?= csrf_field() ?>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Your Name *</label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="John Doe" required value="<?= eXSS($_POST['name'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="john@example.com" required value="<?= eXSS($_POST['email'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" class="form-control" placeholder="Order inquiry, Product question..." value="<?= eXSS($_POST['subject'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message" class="form-control" rows="6" placeholder="Write your message here..." required><?= eXSS($_POST['message'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn btn--primary">Send Message ✉️</button>
                </form>
            </div>
        </div>
    </div>
</section>
