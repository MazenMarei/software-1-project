<?php

namespace App\core;

use App\controllers\AuthController;
use App\controllers\AdminController;
use App\Middlewares\Authentication;
use App\Middlewares\Authorization;

class App
{

    public function __construct()
    {

        // connect to the database
        Database::getInstance();

        // Register routes
        $this->registerRoutes();
    }

    public function registerRoutes()
    {
        // Loading page route (no authentication required)
        Route::get('/', function () {
            if (!isset($_SESSION['Token'])) {
                header('Location: /index');
                exit;
            }

            if (isset($_SESSION['user'])) {
                switch ($_SESSION['user']['role']) {
                    case 'admin':
                        header('Location: /admin/dashboard');
                        exit;
                    case 'artist':
                        header('Location: /artist/dashboard');
                        exit;
                    case 'customer':
                        header('Location: /customer/dashboard');
                        exit;
                }
            }
        });

        // Authentication routes (no authentication required)
        Route::get('/index', AuthController::class, 'index'); /// login and register page
        Route::post('/login', AuthController::class, 'login'); 
        Route::post('/register', AuthController::class, 'register');
        Route::get('/logout', AuthController::class, 'logout');



        // Protected dashboard routes (authentication required with role check)
        Route::get('/admin/dashboard', AdminController::class, 'dashboard', [[Authentication::class, 'admin']]);

        Route::get('/artist/dashboard', AuthController::class, 'artistDashboard', [[Authentication::class, 'artist']]);
        Route::get('/customer/dashboard', AuthController::class, 'customerDashboard', [[Authentication::class, 'customer']]);

        // Admin functionality routes
        Route::post('/admin/approve-item', AdminController::class, 'approveItem', [[Authentication::class, 'admin']]);
        Route::post('/admin/reject-item', AdminController::class, 'rejectItem', [[Authentication::class, 'admin']]);

        
        Route::get('/admin/artworks', AdminController::class, 'artworks', [[Authentication::class, 'admin']]);
        Route::post('/admin/update-artwork-status', AdminController::class, 'updateArtworkStatus', [[Authentication::class, 'admin']]);
        Route::get('/admin/artists', AdminController::class, 'artists', [[Authentication::class, 'admin']]);
        Route::get('/admin/customers', AdminController::class, 'customers', [[Authentication::class, 'admin']]);
        Route::get('/admin/collections', AdminController::class, 'collections', [[Authentication::class, 'admin']]);
        Route::get('/admin/fairs', AdminController::class, 'fairs', [[Authentication::class, 'admin']]);
        Route::get('/admin/orders', AdminController::class, 'orders', [[Authentication::class, 'admin']]);
        Route::get('/admin/withdrawals', AdminController::class, 'withdrawals', [[Authentication::class, 'admin']]);
        Route::get('/admin/offers', AdminController::class, 'offers', [[Authentication::class, 'admin']]);
        Route::get('/admin/reports', AdminController::class, 'reports', [[Authentication::class, 'admin']]);
        Route::get('/admin/settings', AdminController::class, 'settings', [[Authentication::class, 'admin']]);
        Route::get('/admin/profile', AdminController::class, 'profile', [[Authentication::class, 'admin']]);
    }

    public function run()
    {
        Route::dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
    }
}
