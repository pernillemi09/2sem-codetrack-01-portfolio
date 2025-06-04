<?php

declare(strict_types=1);

namespace App;

use App\Http\Request;
use App\Http\Response;

/**
 * Simple router for mapping HTTP requests to controller actions.
 *
 * Supports GET and POST routes, and dispatches requests based on method and URI.
 */
class Router
{
    /**
     * The registered routes, grouped by HTTP method.
     * Each route is mapped to a controller action [Controller::class, 'method'].
     */
    protected array $routes = [];

    /**
     * The Template instance used for rendering views in controllers.
     */
    protected Template $template;

    /**
     * Construct a new Router instance.
     *
     * @param Template $template The template engine instance to use for controllers.
     */
    public function __construct(Template $template)
    {
        $this->template = $template;
    }

    /**
     * Register a GET route with the router.
     *
     * @param string $path The URI path for the route.
     * @param array $handler The controller action for the route.
     */
    public function get(string $path, array $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    /**
     * Register a POST route with the router.
     *
     * @param string $path The URI path for the route.
     * @param array $handler The controller action for the route.
     */
    public function post(string $path, array $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    /**
     * Add a route for a specific HTTP method.
     *
     * @param string $method The HTTP method (GET, POST, etc.).
     * @param string $path The URI path for the route.
     * @param array $handler The controller action for the route.
     */
    protected function addRoute(string $method, string $path, array $handler): void
    {
        // Convert {param} segments to named capture groups
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>\d+)', $path);
        $pattern = str_replace('/', '\/', $pattern);
        
        $this->routes[$method][$path] = [
            'pattern' => '/^' . $pattern . '$/',
            'handler' => $handler
        ];
    }

    /**
     * Dispatch the current request to the appropriate controller action.
     * If no route matches, sends a 404 response.
     *
     * @param string $method The HTTP method of the request.
     * @param string $uri The URI of the request.
     */
    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        $matched = false;
        $routeHandler = null;
        $routeParams = [];

        // Find matching route pattern
        foreach ($this->routes[$method] ?? [] as $route) {
            if (preg_match($route['pattern'], $path, $matches)) {
                // Extract named parameters
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $routeParams[$key] = $value;
                    }
                }
                $routeHandler = $route['handler'];
                $matched = true;
                break;
            }
        }

        if (!$matched || !$this->isValidHandler($routeHandler)) {
            $this->sendErrorResponse(404, '404 Not Found');
            return;
        }

        /**
         * @var class-string<\App\Controller> $class
         * @var string $methodName
         */
        [$class, $methodName] = $routeHandler;

        if (!class_exists($class) || !method_exists($class, $methodName)) {
            $this->sendErrorResponse(
                500,
                "Handler method not found: {$class}::{$methodName}"
            );
            return;
        }

        $controller = new $class();
        $controller->setTemplate($this->template);

        // Merge route parameters with query parameters
        $_GET = array_merge($_GET, $routeParams);
        $request = Request::fromGlobals();
        
        // Add route parameters to controller method arguments if needed
        if (!empty($routeParams)) {
            $response = $controller->$methodName($request, ...array_values($routeParams));
        } else {
            $response = $controller->$methodName($request);
        }

        if (!$response instanceof Response) {
            throw new \RuntimeException(
                "Expected Response object from: {$class}::{$methodName}"
            );
        }

        if ($response->getHeadersSent()) {
            throw new \RuntimeException('Response has already been sent.');
        }

        $response->send();
    }

    /**
     * Check if the handler is a valid controller action.
     *
     * @param mixed $handler
     * @return bool
     */
    protected function isValidHandler(mixed $handler): bool
    {
        return is_array($handler) && count($handler) === 2 && is_string($handler[0]) && is_string($handler[1]);
    }

    /**
     * Send an error response with the given status and message.
     *
     * @param int $status
     * @param string $message
     */
    protected function sendErrorResponse(int $status, string $message): void
    {
        $response = new Response();
        $response->setStatus($status);
        $response->setBody($message);
        $response->send();
    }
}
