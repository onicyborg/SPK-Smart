<?php

declare(strict_types=1);

namespace Core;

class Response
{
    public static function redirect(string $url, int $statusCode = 302): never
    {
        http_response_code($statusCode);
        header("Location: {$url}");
        exit;
    }

    public static function json(array $data, int $statusCode = 200): never
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
