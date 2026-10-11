<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Minimal router.
 *   $router->get('/flowers/{slug}', 'FlowerPageController@show');
 *   $router->post('/api/v1/get-flowers', 'Api\FlowersController@getFlowers');
 *
 * Handlers are resolved under App\Controllers\. A handler may return:
 *   - a string  -> echoed as the HTML response
 *   - nothing   -> the controller already sent its response (JSON / redirect)
 *
 * If you already have your own router, replace this file with it and keep the
 * same get()/post()/dispatch() calls used in public/index.php and routes/*.php.
 */
class Router
{
    /** @var array<string, array<int, array{regex:string, handler:string}>> */
    private array $routes = [];

    public function get(string $path, string $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, string $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    private function add(string $method, string $path, string $handler): void
    {
        // '/flowers/{slug}' -> '#^/flowers/(?P<slug>[^/]+)$#'
        $regex = '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $path) . '$#';
        $this->routes[$method][] = ['regex' => $regex, 'handler' => $handler];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = '/' . trim(rawurldecode($uri), '/');

        foreach ($this->routes[strtoupper($method)] ?? [] as $route) {
            if (preg_match($route['regex'], $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $this->call($route['handler'], array_values($params));
                return;
            }
        }

        echo Response::notFound();
    }

    private function call(string $handler, array $params): void
    {
        [$class, $action] = explode('@', $handler);
        $class = 'App\\Controllers\\' . $class;

        $result = (new $class())->$action(...$params);

        if (is_string($result)) {
            header('Content-Type: text/html; charset=utf-8');
            echo $result;
        }
    }
}
