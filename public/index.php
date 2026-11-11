<?php
declare(strict_types=1);

require __DIR__ . '/../app/bootstrap.php';

use App\Core\DatabaseException;
use App\Core\ErrorLogger;
use App\Core\Response;
use App\Core\Router;
use App\Core\View;

$router = new Router();

require BASE_PATH . '/routes/web.php';
require BASE_PATH . '/routes/api.php';

// Remove the sub-folder prefix if the site is not served at the domain root.
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$base = rtrim((string) config('base_url', ''), '/');
if ($base !== '' && str_starts_with($path, $base)) {
    $path = substr($path, strlen($base)) ?: '/';
}

try {
    $router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $path);
} catch (PDOException | DatabaseException $e) {
    // Full details go to storage/logs/error.log; visitors only see a generic message.
    ErrorLogger::log('ERROR', 'Database unavailable', $e);

    if (str_starts_with($path, '/api/')) {
        Response::json(['error' => 'Service temporarily unavailable'], 503);
    }

    http_response_code(503);
    header('Content-Type: text/html; charset=utf-8');
    echo View::render('errors/database', ['title' => 'Temporarily unavailable']);
}
