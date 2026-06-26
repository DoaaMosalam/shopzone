<?php

namespace Core;

/**
 * Base Controller
 *
 * All application controllers extend this class.
 * Provides view rendering, session flash messages,
 * redirects, and auth guards.
 */
abstract class Controller
{
    // ------------------------------------------------------------------
    // View rendering
    // ------------------------------------------------------------------

    /**
     * Render a view inside a layout.
     *
     * @param string $view   Dot-notation path relative to Views/ (e.g. 'product.list')
     * @param array  $data   Variables to extract into the view scope
     * @param string $layout Layout file name (without extension) inside Views/layouts/
     */
    protected function render(string $view, array $data = [], string $layout = 'main'): void
    {
        extract($data, EXTR_SKIP);

        $viewFile   = VIEW_PATH . '/' . str_replace('.', '/', $view) . '.php';
        $layoutFile = VIEW_PATH . '/layouts/' . $layout . '.php';

        if (!file_exists($viewFile)) {
            die("View file not found: {$viewFile}");
        }

        // Capture the view content
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Inject into layout
        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content;
        }
    }

    /**
     * Render a view without any layout (partial / AJAX fragment).
     */
    protected function renderPartial(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $viewFile = VIEW_PATH . '/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewFile)) {
            die("Partial view not found: {$viewFile}");
        }

        require $viewFile;
    }

    // ------------------------------------------------------------------
    // Redirects
    // ------------------------------------------------------------------

    protected function redirect(string $url): void
    {
        header('Location: ' . APP_URL . '/' . ltrim($url, '/'));
        exit;
    }

    protected function redirectBack(): void
    {
        $ref = $_SERVER['HTTP_REFERER'] ?? APP_URL;
        header('Location: ' . $ref);
        exit;
    }

    // ------------------------------------------------------------------
    // Flash messages (stored in $_SESSION)
    // ------------------------------------------------------------------

    protected function flash(string $type, string $message): void
    {
        $_SESSION['flash'][$type] = $message;
    }

    protected function getFlash(): array
    {
        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $flash;
    }

    // ------------------------------------------------------------------
    // Auth guards
    // ------------------------------------------------------------------

    protected function requireLogin(): void
    {
        if (empty($_SESSION['user_id'])) {
            $this->flash('error', 'Please log in first.');
            $this->redirect('auth/login');
        }
    }

    protected function requireAdmin(): void
    {
        if (empty($_SESSION['is_admin'])) {
            $this->flash('error', 'Access denied.');
            $this->redirect('');
        }
    }

    protected function requireCustomer(): void
    {
        $this->requireLogin();
        if (!empty($_SESSION['is_admin'])) {
            $this->redirect('admin/dashboard');
        }
    }

    // ------------------------------------------------------------------
    // Input helpers
    // ------------------------------------------------------------------

    protected function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    protected function post(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $default;
    }

    protected function get(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }

    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
}
