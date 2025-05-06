<?php


namespace App\Middlewares;  


class Test {
    public function handle($request, $next , $role) {
        var_dump($role);
        // Perform some action before the request is processed
        echo "Middleware Test: Before request<br>";
        
        // Call the next middleware/controller in the stack
        $response = $next($request);
        
        // Perform some action after the request is processed
        echo "Middleware Test: After request<br>";
        
        return $response;
    }
}