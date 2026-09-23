<?php

namespace App\Core;

abstract class Controller
{
    protected function view(string $view, array $data = [], string|false $layout = 'main'): void
    {
        $viewFile = dirname(__DIR__) . '/Views/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View not found: {$view}");
        }

        extract($data);
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        if ($layout === false) {
            echo $content;
            return;
        }

        $layoutFile = dirname(__DIR__) . "/Views/layouts/{$layout}.php";
        require $layoutFile;
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . url($path));
        exit;
    }

    /** Redirects to the page the request came from (same-origin only), or a default path. */
    protected function redirectBack(string $default): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if ($referer !== '' && $this->refererPath() !== null) {
            header('Location: ' . $referer);
            exit;
        }
        $this->redirect($default);
    }

    /**
     * Decoded path of the HTTP Referer, if present and same-origin as this app — or null otherwise.
     * HTTP_REFERER is always an absolute, percent-encoded URL (e.g. "http://host/alumni%20portal/public/jobs"),
     * while BASE_URL is a raw, unencoded path (e.g. "/alumni portal/public"); comparing them directly with
     * str_starts_with() never matches, so both the host and the decoded path must be checked explicitly.
     */
    protected function refererPath(): ?string
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if ($referer === '') {
            return null;
        }

        $refHost = parse_url($referer, PHP_URL_HOST);
        $host = $_SERVER['HTTP_HOST'] ?? null;
        if ($refHost !== null && $host !== null && strcasecmp($refHost, explode(':', $host)[0]) !== 0) {
            return null;
        }

        $refPath = parse_url($referer, PHP_URL_PATH);
        if (!is_string($refPath)) {
            return null;
        }
        $refPath = rawurldecode($refPath);

        $base = rtrim(defined('BASE_URL') ? BASE_URL : '', '/');
        if ($base !== '' && !str_starts_with($refPath, $base)) {
            return null;
        }

        return $refPath;
    }

    /**
     * A "Back to X" [url, label] pair reflecting wherever the visitor actually came from, for
     * detail pages reachable from more than one place (an alumni profile from the Directory,
     * Connections, Messages, a cohort page, etc). Falls back to $defaultUrl/$defaultLabel when
     * there's no usable referer, or it's a page this map doesn't recognize.
     *
     * The referer's query string is preserved, so e.g. a Directory view filtered to
     * ?view=programme&program=MBA is returned to as-is, not reset to the plain listing.
     *
     * @return array{0:string,1:string}
     */
    protected function smartBack(string $defaultUrl, string $defaultLabel): array
    {
        static $labels = [
            'directory' => 'Back to Directory',
            'connections' => 'Back to Connections',
            'messages' => 'Back to Messages',
            'cohorts' => 'Back to Cohorts',
            'businesses' => 'Back to Businesses',
            'jobs' => 'Back to Jobs',
            'companies' => 'Back to Companies',
            'events' => 'Back to Events',
            'spotlight' => 'Back to Spotlight',
            'feed' => 'Back to Feed',
            'mentorship' => 'Back to Mentorship',
        ];

        $refPath = $this->refererPath();
        if ($refPath === null) {
            return [$defaultUrl, $defaultLabel];
        }

        $base = rtrim(defined('BASE_URL') ? BASE_URL : '', '/');
        $relative = $base !== '' && str_starts_with($refPath, $base) ? substr($refPath, strlen($base)) : $refPath;
        $relative = trim($relative, '/');

        // profile/edit is a special case: it lives under /profile, not its own top-level segment.
        if ($relative === 'profile/edit') {
            $refQuery = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY);
            return [url('profile/edit') . ($refQuery ? '?' . $refQuery : ''), 'Back to Edit Profile'];
        }

        $segment = explode('/', $relative)[0] ?? '';
        if ($segment === '' || !isset($labels[$segment])) {
            return [$defaultUrl, $defaultLabel];
        }

        $refQuery = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY);
        $url = url($relative) . ($refQuery ? '?' . $refQuery : '');

        return [$url, $labels[$segment]];
    }

    protected function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    protected function file(string $key): ?array
    {
        return $_FILES[$key] ?? null;
    }

    protected function requireCsrf(): void
    {
        if (!Csrf::verify($_POST['_csrf'] ?? null)) {
            http_response_code(400);
            die('Your form submission expired or was invalid. Please go back and try again.');
        }
    }

    /** Enforces the admin-configured audience for a page key: 'public', 'alumni' (any logged-in user), or 'admin'. */
    protected function requireVisibility(string $pageKey): void
    {
        $viewer = Auth::user();
        if (\App\Models\PageVisibility::isVisibleTo($pageKey, $viewer)) {
            return;
        }
        if ($viewer === null && \App\Models\PageVisibility::audienceOf($pageKey) === 'alumni') {
            $this->redirect('login');
        }
        http_response_code(404);
        (new \App\Controllers\ErrorController())->notFound();
        exit;
    }

    protected function flash(string $key, string $message): void
    {
        $_SESSION['_flash'][$key] = $message;
    }

    protected function old(array $data): void
    {
        $_SESSION['_old'] = $data;
    }
}
