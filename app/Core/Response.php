<?php
declare(strict_types=1);

namespace App\Core;

class Response
{
    public static function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        exit;
    }

    public static function redirect(string $url, int $status = 302): void
    {
        http_response_code($status);
        header('Location: ' . $url);
        exit;
    }

    /** Sets a 404 status and returns the rendered 404 page. */
    public static function notFound(string $message = 'Page not found'): string
    {
        http_response_code(404);
        return View::render('errors/404', ['title' => 'Not found', 'message' => $message]);
    }
}
