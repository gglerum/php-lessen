<?php

/**
 * This is a simple router to illustrate how routing works in a web application.
 */
class Router
{
    // Define the routes for the application
    private array $routes = [
        ['get', 'book/:id', [BookController::class, 'show']],
        ['get', 'book', [BookController::class, 'createBook']],
        ['post', 'book', [BookController::class, 'store']],
        ['get', '', [BookController::class, 'index']],
    ];

    private array $pathParts;

    public function __construct()
    {
        $pathInfo = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        $this->pathParts = explode('/', substr($pathInfo, 1));
    }

    /**
     * Process the route and call the appropriate controller method.
     * @return void
     */
    public function processRoute(): void
    {
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        foreach ($this->routes as $route) {
            [$routeMethod, $routePath, $routeAction] = $route;
            if ($method === $routeMethod && $this->matchRoute($routePath)) {
                if (isset($this->pathParts[1])) {
                    $id = (int)$this->pathParts[1];
                    $routeAction($id);
                    return;
                }
                $routeAction();
                return;
            }
        }
        header('HTTP/1.1 404 Not Found');
        print '404 Not Found';
    }

    /**
     * Check if the route matches the current path.
     * @param string $routePath The route path
     * @return bool True if the route matches the current path, false otherwise
     */
    private function matchRoute(string $routePath): bool
    {
        $routePathParts = explode('/', $routePath);
        if (count($routePathParts) !== count($this->pathParts)) {
            return false;
        }
        foreach ($routePathParts as $key => $routePathPart) {
            // If the path part is a parameter, skip the check
            if (@$routePathPart[0] === ':') {
                continue;
            }
            if ($routePathPart !== $this->pathParts[$key]) {
                return false;
            }
        }
        return true;
    }
}
