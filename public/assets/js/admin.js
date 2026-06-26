/**
 * ShopZone – Admin-specific JavaScript
 */

'use strict';

/* ── Sidebar active link highlight ───────────────────────────────── */
(function highlightSidebarLink() {
    var currentPath = window.location.pathname;
    var links       = document.querySelectorAll('.sidebar__link');

    links.forEach(function (link) {
        var href = link.getAttribute('href') || '';
        if (href && currentPath.includes(href.split('/').slice(-2)[0])) {
            link.classList.add('active');
        }
    });
}());

/* ── Spec rows dynamic add/remove (already in view, backup here) ─── */
(function specRows() {
    var addBtn    = document.getElementById('add-spec');
    var container = document.getElementById('specs-container');
    if (!addBtn || !container) return;

    addBtn.addEventListener('click', function () {
        var row = document.createElement('div');
        row.className = 'spec-row';
        row.innerHTML  = '<input type="text" name="spec_key[]"   placeholder="Key"   class="form-control">';
        row.innerHTML += '<input type="text" name="spec_value[]" placeholder="Value" class="form-control">';
        row.innerHTML += '<button type="button" class="btn btn--danger btn--sm spec-remove">✕</button>';
        container.appendChild(row);
    });

    container.addEventListener('click', function (e) {
        if (e.target.classList.contains('spec-remove')) {
            e.target.closest('.spec-row').remove();
        }
    });
}());

/* ── Table row clickable ─────────────────────────────────────────── */
(function rowClick() {
    document.querySelectorAll('[data-href]').forEach(function (row) {
        row.style.cursor = 'pointer';
        row.addEventListener('click', function () {
            window.location.href = row.dataset.href;
        });
    });
}());
