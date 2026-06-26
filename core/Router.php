<?php

namespace Core;
// core/Router.php
/**
 * Router – maps URL segments to Controller@action
 * 
 * URL format:
 *   /controller/action/param…            → App\Controllers\<Name>Controller
 *   /admin/controller/action/param…      → App\Controllers\Admin\<Name>Controller
 *   /auth/controller/action/param…       → App\Controllers\Auth\<Name>Controller
 *
 * Action names are converted from kebab-case to camelCase automatically.
 * e.g. update-status → updateStatus
 */
class Router
{
    private const NAMESPACE_GROUPS = ['admin', 'auth'];

    private string $namespace   = '';
    private string $controller  = '';
    private string $action      = '';
    private array  $params      = [];

    public function __construct()
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $uri = parse_url($uri, PHP_URL_PATH) ?: '/';

        // Strip the script directory prefix (for subdirectory installs)
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
        if ($base && str_starts_with($uri, $base)) {
            $uri = substr($uri, strlen($base));
        }

        $uri  = '/' . ltrim($uri, '/');
        $parts = array_values(array_filter(explode('/', $uri)));

        $this->parse($parts);
    }

    /** Dispatch to the resolved controller and action */
    public function dispatch(): void
    {
        $class = $this->resolveClass();

        if (!class_exists($class)) {
            $this->abort(404, "Controller not found: {$class}");
            return;
        }

        $ctrl = new $class();

        if (!method_exists($ctrl, $this->action)) {
            $this->abort(404, "Action not found: {$this->action} in {$class}");
            return;
        }

        call_user_func_array([$ctrl, $this->action], $this->params);
    }

    // ------------------------------------------------------------------
    // Parsing
    // ------------------------------------------------------------------

    private function parse(array $parts): void
    {
        if (empty($parts)) {
            // Root URL → default controller
            $this->controller = DEFAULT_CONTROLLER;
            $this->action     = DEFAULT_ACTION;
            return;
        }

        $first = strtolower($parts[0]);

        if (in_array($first, self::NAMESPACE_GROUPS, true)) {
            // Namespaced: admin|auth / controller / action / params…
            $this->namespace  = ucfirst($first);
            $this->controller = isset($parts[1]) ? ucfirst(strtolower($parts[1])) : DEFAULT_CONTROLLER;
            $this->action     = isset($parts[2]) ? $this->toMethod($parts[2]) : DEFAULT_ACTION;
            $this->params     = array_slice($parts, 3);
        } else {
            // Standard: controller / action / params…
            $this->controller = ucfirst(strtolower($parts[0]));
            $this->action     = isset($parts[1]) ? $this->toMethod($parts[1]) : DEFAULT_ACTION;
            $this->params     = array_slice($parts, 2);
        }
    }

    private function resolveClass(): string
    {
        if ($this->namespace) {
            return "App\\Controllers\\{$this->namespace}\\{$this->controller}Controller";
        }
        return "App\\Controllers\\{$this->controller}Controller";
    }

    /**
     * Convert kebab-case segment to camelCase PHP method name.
     * e.g. "update-status" → "updateStatus"
     */
    private function toMethod(string $segment): string
    {
        return lcfirst(str_replace('-', '', ucwords($segment, '-')));
    }

    private function abort(int $code, string $message): void
    {
        http_response_code($code);
        $errorFile = VIEW_PATH . "/errors/{$code}.php";
        if (file_exists($errorFile)) {
            require $errorFile;
        } else {
            echo "<h1>Error {$code}</h1><p>" . htmlspecialchars($message) . "</p>";
        }
        exit;
    }
}
