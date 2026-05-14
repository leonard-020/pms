<?php

namespace App\Core;

abstract class Controller
{
    protected Request $request;
    protected Session $session;

    public function __construct()
    {
        $this->request = new Request();
        $this->session = new Session();
    }

    protected function view(string $path, array $data = []): void
    {
        extract($data, EXTR_SKIP);

        $viewFile = dirname(__DIR__, 2) . "/resources/views/{$path}.php";

        if (!file_exists($viewFile)) {
            Response::error(500);
        }

        // Start buffer to rewrite URLs if in a subfolder
        ob_start();
        include $viewFile;
        $output = ob_get_clean();

        $base = defined('BASE_URL') ? BASE_URL : '';
        if ($base) {
            $output = $this->rewriteUrls($output, $base);
        }

        echo $output;
    }

    protected function layout(string $layout, string $viewPath, array $data = []): void
    {
        // Capture the inner view WITHOUT rewriting (to prevent double rewrite)
        $content = $this->captureView($viewPath, $data);
        $data['content'] = $content;
        
        // The layout view will handle the final rewrite at the end
        $this->view("layouts/{$layout}", $data);
    }

    private function captureView(string $path, array $data = []): string
    {
        ob_start();
        extract($data, EXTR_SKIP);
        $viewFile = dirname(__DIR__, 2) . "/resources/views/{$path}.php";
        
        if (file_exists($viewFile)) {
            include $viewFile;
        }
        
        // Return raw output. Do NOT rewrite here!
        return ob_get_clean();
    }

    /**
     * Automatically rewrites action="/..." and href="/..." to include the base path.
     */
    private function rewriteUrls(string $html, string $base): string
    {
        return preg_replace(
            '/(action|href|src)=(["\'])\/([a-zA-Z0-9\-_])/i',
            '$1=$2' . $base . '/$3',
            $html
        );
    }

    protected function json(array $data, int $code = 200): void
    {
        Response::json($data, $code);
    }

    protected function redirect(string $url): void
    {
        Response::redirect($url);
    }

    protected function back(): void
    {
        Response::back();
    }

    protected function auth(): ?array
    {
        return Session::get('user');
    }

    protected function userId(): ?int
    {
        $user = $this->auth();
        return $user['id'] ?? null;
    }
}