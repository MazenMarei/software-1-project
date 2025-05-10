<?php

namespace App\core;


use App\controllers\AuthController;
use App\controllers\AdminController;
use App\controllers\ArtistController;

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

        Route::get('/artist/dashboard', ArtistController::class, 'dashboard', [[Authentication::class, 'artist']]);
        Route::get('/artist/artworks', ArtistController::class, 'artworks', [[Authentication::class, 'artist']]);
        Route::get('/artist/profile', ArtistController::class, 'profile', [[Authentication::class, 'artist']]);
        Route::get('/artist/new-artwork', ArtistController::class, 'newArtwork', [[Authentication::class, 'artist']]);
        Route::get('/artist/edit-artwork', ArtistController::class, 'editArtwork', [[Authentication::class, 'artist']]);
        Route::get('/artist/collections', ArtistController::class, 'collections', [[Authentication::class, 'artist']]);
        Route::get('/artist/withdraw', ArtistController::class, 'withdraw', [[Authentication::class, 'artist']]);
        Route::get('/artist/followers', ArtistController::class, 'followers', [[Authentication::class, 'artist']]);
        Route::get('/artist/selling-history', ArtistController::class, 'sellingHistory', [[Authentication::class, 'artist']]);
        Route::get('/artist/notifications', ArtistController::class, 'notifications', [[Authentication::class, 'artist']]);
        Route::get('/artist/fairs', ArtistController::class, 'fairs', [[Authentication::class, 'artist']]);

        Route::post('/artist/profilePicUpdate', ArtistController::class, 'profilePicUpdate', [[Authentication::class, 'artist']]);
        Route::post('/artist/changePassword', ArtistController::class, 'changePassword', [[Authentication::class, 'artist']]);
        Route::post("/artist/profileUpdate", ArtistController::class, 'profileUpdate', [[Authentication::class, 'artist']]);
        Route::post('/artist/add-artwork', ArtistController::class, 'addArtwork', [[Authentication::class, 'artist']]);
        Route::post('/artist/delete-artwork', ArtistController::class, 'deleteArtwork', [[Authentication::class, 'artist']]);
        Route::post('/artist/edit-artwork', ArtistController::class, 'updateArtwork', [[Authentication::class, 'artist']]);
        Route::post('/artist/updatePayment', ArtistController::class, 'updatePayment', [[Authentication::class, 'artist']]);
        Route::post('/artist/withdraw', ArtistController::class, 'withdrawRequst', [[Authentication::class, 'artist']]);
        Route::post('/artist/register-fair', ArtistController::class, 'registerFair', [[Authentication::class, 'artist']]);
        Route::post('/artist/delete-fair', ArtistController::class, 'deleteFair', [[Authentication::class, 'artist']]);
        Route::post('/artist/create-collection', ArtistController::class, 'createCollection', [[Authentication::class, 'artist']]);
        Route::post('/artist/delete-collection', ArtistController::class, 'deleteCollection', [[Authentication::class, 'artist']]);

        Route::get('/customer/dashboard', AuthController::class, 'customerDashboard', [[Authentication::class, 'customer']]);

        // Admin functionality routes
        Route::post('/admin/update-artist-status', AdminController::class, 'updateArtistStatus', [[Authentication::class, 'admin']]);
        Route::post('/admin/update-customer-status', AdminController::class, 'updateCustomerStatus', [[Authentication::class, 'admin']]);
        Route::post('/admin/update-artwork-status', AdminController::class, 'updateArtworkStatus', [[Authentication::class, 'admin']]);
        Route::post("/admin/profilePicUpdate",  AdminController::class, 'profilePicUpdate', [[Authentication::class, 'admin']]);
        Route::post("/admin/profileUpdate",  AdminController::class, 'profileUpdate', [[Authentication::class, 'admin']]);
        Route::post("/admin/changePassword",  AdminController::class, 'changePassword', [[Authentication::class, 'admin']]);
        Route::post("/admin/updateFairStatus",  AdminController::class, 'updateFairStatus', [[Authentication::class, 'admin']]);

        Route::get('/admin/artworks', AdminController::class, 'artworks', [[Authentication::class, 'admin']]);
        Route::get('/admin/withdrawals', AdminController::class, 'withdrawals', [[Authentication::class, 'admin']]);





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
        Route::get('/admin/orders', AdminController::class, 'orders', [[Authentication::class, 'admin']]);
    }

    public function run()
    {
        Route::dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
    }

    static function handleUploadImage($file, $path)
    {
        try {

            $fileName = $file['name'];
            $fileType = $file['type'];

            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg', 'image/webp'];
            $fileType = strtolower($fileType);
            if (!in_array($fileType, $allowedTypes)) {
                $_SESSION['error'] = 'Only JPG, JPEG, PNG, Webp and GIF files are allowed';
                return false;
            }

            // Generate unique filename
            $newFileName = uniqid('artist_') . '.' . pathinfo($fileName, PATHINFO_EXTENSION);
            $uploadPath = UPLOADS . $path . DS . $newFileName;

            // Check if directory exists and is writable
            $directory = UPLOADS . $path . DS;
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            if (!is_writable($directory)) {
                $_SESSION['error'] = "Upload directory is not writable";
                return false;
            }

            // Move uploaded file
            $fileTmpName = $file['tmp_name'];
            if (move_uploaded_file($fileTmpName, $uploadPath)) {
                return $newFileName;
            } else {
                return false;
            }
        } catch (\Throwable $th) {
            $_SESSION['error'] = "Error uploading file: " . $th->getMessage();
            return false;
        }
    }
}
