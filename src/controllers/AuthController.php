<?php

namespace App\controllers;

use App\models\Gust;
use App\Middlewares\Authentication;
use App\models\User;

class AuthController
{

    public function index()
    {
        // Check if user is already logged in
        if (isset($_SESSION['Token']) && isset($_SESSION['user'])) {
            // Redirect to appropriate dashboard based on role
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
                default:
                    // If role not recognized, show login page
                    require_once VIEWS . "index.php";
            }
        } else {
            // If not logged in, display the login page
            require_once VIEWS . "index.php";
        }
    }


    public function login()
    {
        if (!isset($_POST['email']) || !isset($_POST['password'])) {
            $_SESSION['error'] = 'Email and password are required';
            header('Location: /index');
            exit;
        }

        $email = $_POST['email'];
        $password = $_POST['password'];

        // Use Gust model to handle login

        $user = new User();
        $loginResult = $user->login($email, $password);

        if ($loginResult['status']) {
            // Login successful
            $_SESSION['Token'] = $loginResult['token'];
            $_SESSION['success'] = 'Login successful. Welcome back!';

            // Store user ID and role in session for easier access
            $_SESSION['user'] = [
                'id' => $loginResult['userId'],
                'email' => $email,
                'role' => $loginResult['role']
            ];

            // Redirect based on role
            switch ($loginResult['role']) {
                case 'admin':
                    header('Location: /admin/dashboard');
                    break;
                case 'artist':
                    header('Location: /artist/dashboard');
                    break;
                case 'customer':
                    header('Location: /customer/dashboard');
                    break;
                default:
                    header('Location: /index');
            }
            exit;
        } else {
            // Login failed
            $_SESSION['error'] = $loginResult['message'];
            header('Location: /index');
            exit;
        }
    }

  
    public function register()
    {
        // Validate all required fields
        if (
            !isset($_POST['username']) || !isset($_POST['password']) || !isset($_POST['email']) ||
            !isset($_POST['firstName']) || !isset($_POST['lastName']) || !isset($_POST['accountType']) ||
            !isset($_POST['termsAgreement'])
        ) {
            $_SESSION['error'] = 'All fields are required';
            header('Location: /index');
            exit;
        }

        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $email = trim($_POST['email']);
        $firstName = trim($_POST['firstName']);
        $lastName = trim($_POST['lastName']);
        $accountType = $_POST['accountType'];
        $termsAgreement = $_POST['termsAgreement'];

        // Check if terms are agreed to
        if ($termsAgreement !== 'on') {
            $_SESSION['error'] = 'You must agree to the terms and conditions';
            header('Location: /index');
            exit;
        }

        // Additional validation
        if (strlen($password) < 8) {
            $_SESSION['error'] = 'Password must be at least 8 characters long';
            header('Location: /index');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Please enter a valid email address';
            header('Location: /index');
            exit;
        }

        if (strlen($username) < 4) {
            $_SESSION['error'] = 'Username must be at least 4 characters long';
            header('Location: /index');
            exit;
        }

        if ($accountType !== 'customer' && $accountType !== 'artist') {
            $_SESSION['error'] = 'Invalid account type';
            header('Location: /index');
            exit;
        }

        $profilePic = 'default.jpg';

        // Process registration
        $guest = new Gust();
        $registrationResult = $guest->register($firstName, $lastName, $username, $email, $password, $accountType, $profilePic);

        if ($registrationResult['status']) {
            // Registration successful
            if ($accountType === 'artist') {
                // Artists need approval
                $_SESSION['success'] = 'Your artist account has been registered successfully. Please wait for admin approval.';
                header('Location: /index');
            } else {
                // Customers can log in immediately
                $_SESSION['Token'] = $registrationResult['token'];
                $_SESSION['success'] = 'Your account has been created successfully. Welcome to ArtShelf!';

                // Store user data in session
                $userData = Authentication::validateToken($registrationResult['token']);
                $_SESSION['user'] = [
                    'id' => $userData->id,
                    'email' => $userData->email,
                    'role' => $userData->role
                ];

                header('Location: /customer/dashboard');
            }
            exit;
        } else {
            // Registration failed
            $_SESSION['error'] = $registrationResult['message'];
            header('Location: /index');
            exit;
        }
    }

 
    public function logout()
    {
        // Clear session data
        session_unset();
        session_destroy();

        // Start a new session for flash messages
        session_start();
        $_SESSION['success'] = 'You have been successfully logged out.';

        // Redirect to login page
        header('Location: /index');
        exit;
    }

    public function customerDashboard()
    {
        require_once VIEWS . 'pages/Customer/index.php';
    }


    public function artistDashboard()
    {
        require_once VIEWS . 'pages/Artist/index.php';
    }


    public function adminDashboard()
    {
        require_once VIEWS . 'pages/Admin/index.php';
    }
}
