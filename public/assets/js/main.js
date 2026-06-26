/**
 * ShopZone – Main JavaScript (vanilla, no libraries)
 */

'use strict';

/* ── Auto-dismiss flash alerts after 5 seconds ───────────────────── */
(function dismissAlerts() {
    var alerts = document.querySelectorAll('.alert');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity .4s ease';
            alert.style.opacity = '0';
            setTimeout(function () { alert.remove(); }, 400);
        }, 5000);
    });
}());

/* ── Avatar preview before upload ────────────────────────────────── */
function previewAvatar(input) {
    if (!input.files || !input.files[0]) return;

    var reader = new FileReader();
    reader.onload = function (e) {
        var preview = document.getElementById('avatarPreview');
        if (preview) preview.src = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
}

/* ── Confirm delete forms ─────────────────────────────────────────── */
(function confirmDeletes() {
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (!confirm(el.dataset.confirm || 'Are you sure?')) {
                e.preventDefault();
            }
        });
    });
}());

/* ── Quantity input – clamp to valid range ────────────────────────── */
(function clampQty() {
    document.querySelectorAll('input[type="number"].qty-input').forEach(function (input) {
        input.addEventListener('change', function () {
            var min = parseInt(input.min) || 1;
            var max = parseInt(input.max) || 9999;
            var val = parseInt(input.value) || min;
            input.value = Math.min(Math.max(val, min), max);
        });
    });
}());

/* ── Coupon code – uppercase on input ────────────────────────────── */
(function uppercaseCoupon() {
    var couponInput = document.querySelector('[name="coupon_code"]');
    if (couponInput) {
        couponInput.addEventListener('input', function () {
            var pos = this.selectionStart;
            this.value = this.value.toUpperCase();
            this.setSelectionRange(pos, pos);
        });
    }
}());

/* ── Smooth scroll to anchor links ───────────────────────────────── */
(function smoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            var target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
}());

/* ── Product image preview in admin form ─────────────────────────── */
(function productImagePreview() {
    var fileInput = document.querySelector('input[name="image"]');
    if (!fileInput) return;

    fileInput.addEventListener('change', function () {
        if (!this.files || !this.files[0]) return;
        var reader = new FileReader();
        reader.onload = function (e) {
            var preview = document.createElement('img');
            preview.src = e.target.result;
            preview.style.cssText = 'max-height:150px; border-radius:8px; margin-top:.5rem;';

            var existing = fileInput.parentElement.querySelector('.img-new-preview');
            if (existing) existing.remove();

            preview.className = 'img-new-preview';
            fileInput.parentElement.appendChild(preview);
        };
        reader.readAsDataURL(this.files[0]);
    });
}());

/* ── Scroll to top button ─────────────────────────────────────────── */
(function scrollToTop() {
    var btn = document.getElementById('scrollTop');
    if (!btn) return;

    window.addEventListener('scroll', function () {
        if (window.scrollY > 300) {
            btn.classList.add('scroll-top-btn--visible');
        } else {
            btn.classList.remove('scroll-top-btn--visible');
        }
    }, { passive: true });

    btn.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}());
