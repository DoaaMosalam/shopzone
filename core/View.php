<?php

namespace Core;

/**
 * View – static helper used inside .php view files
 */
class View
{
    /** Escape a value for safe HTML output */
    public static function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /** Output an escaped value */
    public static function out(mixed $value): void
    {
        echo self::e($value);
    }

    /** Format a price as currency */
    public static function money(float|int $amount, string $currency = 'EGP'): string
    {
        return number_format((float) $amount, 2) . ' ' . $currency;
    }

    /** Format a date */
    public static function date(string|null $date, string $format = 'd M Y'): string
    {
        if (!$date) return '—';
        return date($format, strtotime($date));
    }

    /** Return a star-rating HTML string */
    public static function stars(float $rating): string
    {
        $full  = (int) floor($rating);
        $half  = ($rating - $full) >= 0.5 ? 1 : 0;
        $empty = 5 - $full - $half;
        return str_repeat('★', $full) . str_repeat('½', $half) . str_repeat('☆', $empty);
    }

    /** Build a query-string URL helper */
    public static function url(string $path, array $params = []): string
    {
        $url = APP_URL . '/' . ltrim($path, '/');
        if ($params) {
            $url .= '?' . http_build_query($params);
        }
        return $url;
    }

    /** Asset URL helper */
    public static function asset(string $path): string
    {
        return APP_URL . '/assets/' . ltrim($path, '/');
    }

    /** Return 'active' class if current URI segment matches */
    public static function activeClass(string $segment, string $class = 'active'): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        return str_contains($uri, $segment) ? $class : '';
    }

    /** Render a pagination bar */
    public static function pagination(array $paginator, string $baseUrl): string
    {
        if ($paginator['totalPages'] <= 1) return '';

        $html  = '<nav class="pagination"><ul>';
        $page  = $paginator['page'];
        $total = $paginator['totalPages'];

        for ($i = 1; $i <= $total; $i++) {
            $active = $i === $page ? ' class="page-active"' : '';
            $url    = $baseUrl . '?page=' . $i;
            $html  .= "<li{$active}><a href=\"{$url}\">{$i}</a></li>";
        }

        $html .= '</ul></nav>';
        return $html;
    }
}
