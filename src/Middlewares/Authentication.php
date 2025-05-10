<?php

namespace App\Middlewares;

use App\models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


define('SECRET_KEY', 'ARTSHELF_SECRET_KEY');

class Authentication
{

    public function handle($request, $next, $roles = [])
    {
        // Start session if not started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Check if the user is authenticated
        if (!isset($_SESSION['Token'])) {
            $_SESSION['error'] = 'Please log in to access this page';
            $this->redirectLogin();
            exit;
        }

        $token = $_SESSION['Token'];

        try {
            // Decode the token to get the user information
            $decodedToken = JWT::decode($token, new Key(SECRET_KEY, 'HS256'));
            // Check if the token is valid
            if (!$decodedToken) {
                $_SESSION['error'] = 'Invalid token. Please log in again.';
                $this->redirectLogin();
                exit;
            }

            // Set user data for the current request if not already set
            if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
                $_SESSION['user'] = [
                    'id' => $decodedToken->id,
                    'email' => $decodedToken->email,
                    'role' => $decodedToken->role
                ];
            }

            // If roles are specified, check if the user has the required role
            if (!empty($roles) && !in_array($_SESSION['user']['role'], $roles)) {
                $_SESSION['error'] = 'You do not have permission to access this page';
                $this->redirectLogin();
                exit;
            }

            $user = User::getCurrentUser();
            if (!$user) {
                $_SESSION['error'] = 'User not found. Please log in again.';
                exit;
            }
            if ($user->getStatus() == 'Rejected') {
                $this->redirectLogin();
                $_SESSION['error'] = 'Your account is Rejected. Please contact support.';
            }

            $response = $next($request);
            return $response;
        } catch (\Exception $e) {
            // Clear invalid token
            unset($_SESSION['Token']);
            unset($_SESSION['user']);

            $_SESSION['error'] = 'Authentication error: ' . $e->getMessage() . '. Please log in again.';
            $this->redirectLogin();
            exit;
        }
    }

    public function redirectLogin()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        session_unset();

        if ($_SERVER['REQUEST_URI'] == '/index') {
            $_SESSION['error'] = 'Please log in to access this page';
        } else {
            header('Location: /index');
            exit;
        }
    }

    public static function validateToken($token)
    {
        try {
            $decoded = JWT::decode($token, new Key(SECRET_KEY, 'HS256'));
            return $decoded;
        } catch (\Exception $e) {
            return false;
        }
    }
    public static function generateToken($userId, $email, $role, $expiry = 7200)
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + $expiry;

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'id' => $userId,
            'email' => $email,
            'role' => $role
        ];

        return JWT::encode($payload, SECRET_KEY, 'HS256');
    }
}
