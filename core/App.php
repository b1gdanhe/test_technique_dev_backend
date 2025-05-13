<?php

namespace Core;

class App
{
    private static $instance = null;
    private static $routes = [
        "GET" => [],
        "POST" => [],
        "PUT" => [],
        "PATCH" => [],
        "DELETE" => [],
    ];
    
    private $request;
    private $response;

    private function __construct()
    {
        $this->request = new Request();
        $this->response = new Response();
        error_log('Instance d\'application créée');
    }

    public static function getInstance(): App
    {
        if (self::$instance === null) {
            self::$instance = new App();
        }
        return self::$instance;
    }

    public function get(string $route, array|callable $action)
    {
        $this->addRoute('GET', $route, $action);
        return $this;
    }

    public function post(string $route, array|callable $action)
    {
        $this->addRoute('POST', $route, $action);
        return $this;
    }

    public function put(string $route, array|callable $action)
    {
        $this->addRoute('PUT', $route, $action);
        return $this;
    }

    public function patch(string $route, array|callable $action)
    {
        $this->addRoute('PATCH', $route, $action);
        return $this;
    }

    public function delete(string $route, array|callable $action)
    {
        $this->addRoute('DELETE', $route, $action);
        return $this;
    }

    private function addRoute(string $method, string $route, array|callable $action)
    {
        self::$routes[$method][$route] = $action;
    }

    public function run()
    {
        $method = $this->request->getMethod();
        $uri = $this->request->getUri();
        
        if (!isset(self::$routes[$method][$uri])) {
            $this->response->setStatusCode(404);
            $this->response->json(['error' => 'Route not found']);
            return;
        }

        $action = self::$routes[$method][$uri];
        
        if (is_callable($action)) {
            $result = $action($this->request, $this->response);
            if (!$this->response->isSent()) {
                $this->response->json($result ?? []);
            }
        } elseif (is_array($action) && count($action) === 2) {
            [$controllerClass, $method] = $action;
            
            // Check for middleware
            if (isset($action[2]) && is_array($action[2])) {
                foreach ($action[2] as $middleware) {
                    $middlewareInstance = new $middleware();
                    if (!$middlewareInstance->handle($this->request, $this->response)) {
                        return;
                    }
                }
            }
            
            $controller = new $controllerClass();
            $result = $controller->$method($this->request, $this->response);
            
            if (!$this->response->isSent()) {
                $this->response->json($result ?? []);
            }
        }
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }
}