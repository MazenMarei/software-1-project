<?php

namespace App\core;

class Route
{
    private static $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => [],
    ];



    public static function add(string $method, string $path, $controller, string $action = null, array $middleware = [])
    {
        self::$routes[$method][ltrim($path, '/')] = [
            'controller' => $controller,
            'action' => $action,
            'middleware' => $middleware
        ];
    }


    public static function get(string $path, $controller,  string $action = null, array $middleware = [])
    {
        return self::add('GET', $path, $controller,   $action, $middleware);
    }
    public static function post(string $path, $controller,  string $action = null, array $middleware = [])
    {
        return self::add('POST', $path, $controller,   $action, $middleware);
    }
    public static function put(string $path, $controller,  string $action = null, array $middleware = [])
    {
        return self::add('PUT', $path, $controller,   $action, $middleware);
    }
    public static function delete(string $path, $controller,  string $action = null, array $middleware = [])
    {
        return self::add('DELETE', $path, $controller,   $action, $middleware);
    }

    public static function dispatch(string $method, string $path)
    {
        // Strip query parameters from the path
        $path = parse_url($path, PHP_URL_PATH);

        // Remove leading slash for comparison with route patterns
        $path = ltrim($path, '/');

        foreach (self::$routes[$method] as $route => $routeInfo) {
            /// check if the route is a regex pattern for dynamic routes  => /{id} or {name} 
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[a-zA-Z0-9_]+)', $route);
            $pattern = "#^$pattern$#";

            // get the route parameters from the regex pattern => /{id} or {name} => $matches['id'] or $matches['name']
            if (preg_match($pattern, $path, $matches)) {
                /// fillter the array to get only the parameters from the regex pattern and get the values from the $matches array 
                /// with string keys only
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                // get the controller and action from the route info
                $controller = $routeInfo['controller'];
                $action = $routeInfo['action'];
                $middleware = $routeInfo['middleware'];

                // check if the controller is a callable function or a class name
                if (is_object($controller)) {

                    /// make a closure function to call the controller with the parameters
                    $next = function ($request) use ($controller, $params) {
                        return $controller(...$params);
                    };
                    // handle the middleware for the closure function
                    $next = self::handleMiddleware($middleware, $next);
                    return $next($path);
                } elseif (class_exists($controller)) {
                    // create an instance of the class
                    $controllerInstance = new $controller();
                    // check if the action is a callable function or a method of the class
                    if (method_exists($controllerInstance, $action)) {
                        // make a closure function to call the controller with the parameters
                        $next = function ($request) use ($controllerInstance, $action, $params) {
                            return $controllerInstance->$action(...$params);
                        };
                        // handle the middleware for the closure function
                        $next = self::handleMiddleware($middleware, $next);
                        return $next($path);
                    } else {
                        throw new \Exception("Action not found: " . $action);
                    }
                } else {
                    throw new \Exception("Controller not found: " . $controller);
                }
            }
        }

        throw new \Exception("No route found for $method $path");
    }


    public static function handleMiddleware($middlewares, $next)
    {
        foreach (array_reverse($middlewares) as $middleware) {
            // Check if the middleware is a class with parameters
            if (is_string($middleware) && strpos($middleware, ',') !== false) {
                $parts = explode(',', $middleware);
                $middlewareClass = $parts[0];
                $params = array_slice($parts, 1);

                $next = function ($request) use ($middlewareClass, $next, $params) {
                    return (new $middlewareClass())->handle($request, $next, $params);
                };
            }
            // Handle case where middleware is just a class
            elseif (is_string($middleware)) {
                $next = function ($request) use ($middleware, $next) {
                    return (new $middleware())->handle($request, $next);
                };
            }
            // Handle case where middleware is an array [Class, 'role']
            elseif (is_array($middleware) && count($middleware) >= 1) {
                $middlewareClass = $middleware[0];
                $params = array_slice($middleware, 1);

                $next = function ($request) use ($middlewareClass, $next, $params) {
                    return (new $middlewareClass())->handle($request, $next, $params);
                };
            }
        }
        return $next;
    }
}
