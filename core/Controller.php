<?php

declare(strict_types=1);

namespace Core;

class Controller
{
    protected function render(string $view, array $data = []): void
    {
        $viewPath = dirname(__DIR__) . '/views/' . $view . '.php';

        if (!file_exists($viewPath)) {
            http_response_code(500);
            echo "View file {$view}.php not found.";
            return;
        }

        extract($data, EXTR_SKIP);

        require $viewPath;
    }
}
