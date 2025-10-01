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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
    }

    protected function takeFlashes(): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $flashes = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $flashes;
    }
}
