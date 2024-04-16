<?php

namespace PicoPHP\Classes;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Router {
    private $routes = [];
    private $names = [];

    public function add($method, $path, $action, array $middleware = []) {
        $this->routes[strtoupper($method)][$path] = [
            'action' => $action,
            'middleware' => $middleware
        ];
    }

    public function get($path, $action, array $middleware = []) {
        $this->add('GET', $path, $action, $middleware);
    }

    public function post($path, $action, array $middleware = []) {
        $this->add('POST', $path, $action, $middleware);
    }

    public function put($path, $action, array $middleware = []) {
        $this->add('PUT', $path, $action, $middleware);
    }

    public function delete($path, $action, array $middleware = []) {
        $this->add('DELETE', $path, $action, $middleware);
    }

    public function name($name) {
        if (!empty($this->routes)) {
            $lastMethod = array_key_last($this->routes);
            $lastPath = array_key_last($this->routes[$lastMethod]);
            $this->names[$name] = ['method' => $lastMethod, 'path' => $lastPath];
        }
    }

    public function pathFor($name, array $params = []) {
        if (!isset($this->names[$name])) {
            throw new \Exception("No route defined for name: $name");
        }

        $path = $this->names[$name]['path'];
        return $this->replacePlaceholders($path, $params);
    }

    function jsonResponse($data, $statusCode = 200, $headers = []) {
        header('Content-Type: application/json');

        foreach ($headers as $header => $value) {
            header("$header: $value");
        }

        http_response_code($statusCode);

        echo json_encode($data);
        exit;
    }

    public function dispatch(Request $request) {
        $method = $request->getMethod();
        $path = $request->getPathInfo();

        foreach ($this->routes[$method] ?? [] as $routePath => $routeConfig) {
            if ($this->pathMatches($routePath, $path, $params)) {
                $request->attributes->add($params);
                return $this->handleMiddlewares($request, $routeConfig['action'], $routeConfig['middleware'], $params);
            }
        }

        $this->jsonResponse(['error' => 'Not Found'], Response::HTTP_NOT_FOUND);
    }

    private function handleMiddlewares(Request $request, $action, $middlewares, $params) {
        $handler = function ($req) use ($action, $params) {
            return $this->executeAction($action, $req, $params);
        };

        foreach (array_reverse($middlewares) as $middleware) {
            $handler = function ($req) use ($middleware, $handler, $params) {
                return $middleware->handle($req, $handler, $params);
            };
        }

        return $handler($request);
    }

    private function executeAction($action, Request $request, $params) {
        if (is_callable($action)) {
            return call_user_func($action, $request);
        }

        if (is_array($action) && class_exists($action[0]) && method_exists($action[0], $action[1])) {
            $controllerInstance = new $action[0]();
            return $controllerInstance->{$action[1]}($request);
        }

        $this->jsonResponse(['error' => 'Action not executable'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    private function pathMatches($routePath, $path, &$params) {
        $regex = $this->convertToRegex($routePath);
        if (preg_match($regex, $path, $matches)) {
            $params = $this->parseParams($routePath, $matches);
            return true;
        }
        return false;
    }

    private function convertToRegex($path) {
        return '/^' . preg_replace_callback('/\{(\w+)\}/', function ($matches) {
                return '(?P<' . $matches[1] . '>[^\/]+)';
            }, str_replace('/', '\/', $path)) . '$/';
    }

    private function parseParams($routePath, $matches) {
        $params = [];
        if (preg_match_all('/\{(\w+)\}/', $routePath, $paramNames)) {
            foreach ($paramNames[1] as $name) {
                if (isset($matches[$name])) {
                    $params[$name] = $matches[$name];
                }
            }
        }
        return $params;
    }

    private function replacePlaceholders($path, $params) {
        foreach ($params as $key => $value) {
            $path = str_replace('{' . $key . '}', $value, $path);
        }
        return $path;
    }
}
