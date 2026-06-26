<?php

/**
 * Global helper functions
 */

use Core\View;

/** Shortcut for View::e() */
function eXSS(mixed $v): string
{
    return View::e($v);
}

/** Shortcut for View::url() */
function url(string $path, array $params = []): string
{
    return View::url($path, $params);
}

/** Shortcut for View::asset() */
function asset(string $path): string
{
    return View::asset($path);
}

/** Format money */
function money(float $amount, string $currency = 'EGP'): string
{
    return View::money($amount, $currency);
}

/** Format date */
function fmt_date(?string $date, string $format = 'd M Y'): string
{
    return View::date($date, $format);
}

/** Status badge HTML */
function order_badge(string $status): string
{
    $map = [
        'Pending'    => 'badge-warning',
        'Processing' => 'badge-info',
        'Shipped'    => 'badge-primary',
        'Delivered'  => 'badge-success',
        'Cancelled'  => 'badge-danger',
        'Refunded'   => 'badge-secondary',
    ];
    $class = $map[$status] ?? 'badge-secondary';
    return "<span class=\"badge {$class}\">" . eXSS($status) . "</span>";
}

/** Check if user is logged in */
function is_logged_in(): bool
{
    return !empty($_SESSION['user_id']);
}

/** Check if current user is admin */
function is_admin(): bool
{
    return !empty($_SESSION['is_admin']);
}

/** Generate CSRF token */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Render CSRF hidden input */
function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . eXSS(csrf_token()) . '">';
}

/** Verify CSRF token */
function verify_csrf(): bool
{
    $token = $_POST['_csrf'] ?? '';
    return hash_equals(csrf_token(), $token);
}

/** Truncate long text */
function truncate(string $text, int $length = 120): string
{
    return mb_strlen($text) > $length
        ? mb_substr($text, 0, $length) . '...'
        : $text;
}

/** Product image URL with fallback — handles HTTP/HTTPS URLs and local uploads */
function product_image(string|null $path): string
{
    if (!$path) {
        return asset('images/no-image.svg');
    }

    // Already a full HTTP/HTTPS URL — use it directly
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        return $path;
    }

    // Local upload — return the /storage/ URL directly (router.php serves it).
    // We intentionally skip file_exists() here because:
    //   1. The router already handles /storage/ → storage/uploads/ mapping.
    //   2. file_exists() can fail due to working-directory differences.
    //   3. If the file is missing, onerror on the <img> tag shows the fallback.
    return APP_URL . '/storage/' . ltrim($path, '/');
}
