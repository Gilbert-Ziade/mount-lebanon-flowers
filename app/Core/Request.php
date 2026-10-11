<?php
declare(strict_types=1);

namespace App\Core;

class Request
{
    private array $json = [];

    public function __construct()
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (stripos($contentType, 'application/json') !== false) {
            $decoded = json_decode((string) file_get_contents('php://input'), true);
            $this->json = is_array($decoded) ? $decoded : [];
        }
    }

    /** Value from JSON body, then POST, then query string. */
    public function input(string $key, mixed $default = null): mixed
    {
        return $this->json[$key] ?? $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    public function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }
}
