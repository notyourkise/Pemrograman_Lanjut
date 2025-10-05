<?php

class Controller
{
    protected function view(string $path, array $data = []): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        extract($data, EXTR_SKIP);
        $viewFile = __DIR__ . '/../views/' . $path . '.php';
        if (!is_file($viewFile)) {
            http_response_code(404);
            echo 'View not found: ' . htmlspecialchars($path);
            return;
        }
        require $viewFile;
    }

    protected function flash(string $type, string $message): void
    {
        Flash::add($type, $message);
    }

    protected function takeFlashes(): array
    {
        return Flash::take();
    }
}
