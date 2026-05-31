<?php

declare(strict_types=1);

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        $token = $_SESSION['_csrf_token'] ?? bin2hex(random_bytes(32));
        $_SESSION['_csrf_token'] = $token;
        return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars($token) . '">';
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        $token = $_SESSION['_csrf_token'] ?? bin2hex(random_bytes(32));
        $_SESSION['_csrf_token'] = $token;
        return $token;
    }
}
