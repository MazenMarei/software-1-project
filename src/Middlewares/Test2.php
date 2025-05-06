<?php


namespace App\Middlewares;  


class Test2 {
    public function handle($request, $next) {
        // Perform some action before the request is processed
        echo "Middleware Test2: Before request<br>";
        
        // Call the next middleware/controller in the stack
        $response = $next($request);
        
        // Perform some action after the request is processed
        echo "Middleware Test2: After request<br>";
        
        return $response;
    }
}