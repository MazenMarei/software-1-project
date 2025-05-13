<?php

namespace App\Controllers;

use App\core\App;
use App\models\Artist;
use App\models\Artwork;
use App\models\Cart;
use App\Models\Customer;
use App\models\SpecialCollection;
use App\models\ArtFair;
use App\models\Questionnaire;

class CustomerController
{
    public function index()
    {
        $customer = Customer::getCurrentCustomer();

        $featuredArtworks = Artwork::getAllAcceptedArtworks();

        uasort($featuredArtworks, function ($a, $b) {
            return strtotime($b->getDateCreated()) - strtotime($a->getDateCreated());
        });
        $featuredArtworks = array_slice($featuredArtworks, 0, 3);

        $featuredArtists = Artist::getFeaturedArtists();

        $specialCollections = SpecialCollection::getInstance();
        $specialCollections->fetchArtworks();


        $cart = $customer->getCart();
        $cartItems = $cart->getItems();


        include_once  VIEWS . 'pages/Customer/index.php';
    }
    public function cart()
    {
        $customer = Customer::getCurrentCustomer();
        if (!$customer) {
            $_SESSION['error'] = "You must be logged in to view your cart.";
            header('Location: /index');
            exit;
        }
        $cart = new Cart($customer->getCustomerID());
        $cartItems = $cart->getItems();
        $governments = ArtFair::getGoverments();
        include_once  VIEWS . 'pages/Customer/cart.php';
    }

    public function addToCart($id)
    {
        $previousUrl = $_SERVER['HTTP_REFERER'] ?? "/customer/dashboard";

        $customer = Customer::getCurrentCustomer();
        $artwork = Artwork::getArtworkById($id);
        if (!$customer) {
            $_SESSION['error'] = "You must be logged in to add items to your cart.";
            header('Location: ' . $previousUrl);
            exit;
        }
        if (!$artwork) {
            $_SESSION['error'] = "Artwork not found.";
            header('Location: ' . $previousUrl);
            exit;
        }
        if ($artwork->getStatus() !==  "Accepted") {
            $_SESSION['error'] = "Artwork is not available for purchase.";
            header('Location: ' . $previousUrl);
            exit;
        }
        $cart = new Cart($customer->getCustomerID());
        $addItemResult = $cart->addItem($id);
        if ($addItemResult) {
            $_SESSION['success'] = "Artwork added to cart successfully.";
        } else {
            $_SESSION['error'] = "Failed to add artwork to cart. " . $_SESSION['error'];
        }
        header('Location: ' . $previousUrl);
        exit;
    }

    public function removeFromCart($id)
    {
        $previousUrl = $_SERVER['HTTP_REFERER'] ?? "/customer/dashboard";

        $customer = Customer::getCurrentCustomer();
        if (!$customer) {
            $_SESSION['error'] = "You must be logged in to remove items from your cart.";
            header('Location: ' . $previousUrl);
            exit;
        }
        $cart = new Cart($customer->getCustomerID());
        $removeItemResult = $cart->removeItem($id);
        if ($removeItemResult) {
            $_SESSION['success'] = "Artwork removed from cart successfully.";
        } else {
            $_SESSION['error'] = "Failed to remove artwork from cart. " . $_SESSION['error'];
        }
        header('Location: ' . $previousUrl);
        exit;
    }
    public function checkout()
    {
        $customer = Customer::getCurrentCustomer();
        if (!$customer) {
            $_SESSION['error'] = "You must be logged in to checkout.";
            header('Location: /customer/dashboard');
            exit;
        }

        if (!isset($_POST['city']) || !isset($_POST['address']) || !isset($_POST['postal_code'])) {
            $_SESSION['error'] = "All fields are required.";
            header('Location: /customer/cart');
            exit;
        }
        $city = $_POST['city'];
        $address = $_POST['address'];
        $postal_code = $_POST['postal_code'];
        if (empty($city) || empty($address) || empty($postal_code)) {
            $_SESSION['error'] = "All fields are required.";
            header('Location: /customer/cart');
            exit;
        }
        $cart = new Cart($customer->getCustomerID());
        $checkoutResult = $cart->checkout($address, $city, $postal_code);

        if ($checkoutResult) {
            $_SESSION['success'] = "Checkout successful.";
        } else {
            $_SESSION['error'] = "Failed to checkout. " . $_SESSION['error'];
        }
        header('Location: /customer/cart');
        exit;
    }

    public function profile()
    {
        $customer = Customer::getCurrentCustomer();
        if (!$customer) {
            $_SESSION['error'] = "You must be logged in to view your profile.";
            header('Location: /index');
            exit;
        }
        $payment = $customer->getPaymentMethod();
        $governments = ArtFair::getGoverments();
        $notifications = $customer->getNotifications();
        include_once  VIEWS . 'pages/Customer/profile.php';
    }


    public function artworks($id)
    {
        $customer = Customer::getCurrentCustomer();
        if (!$customer) {
            $_SESSION['error'] = "You must be logged in to view your artworks.";
            header('Location: /index');
            exit;
        }
        $artwork = Artwork::getArtworkById($id);
        if (!$artwork) {
            $_SESSION['error'] = "Artwork not found.";
            header('Location: /customer/dashboard');
            exit;
        }
        include_once  VIEWS . 'pages/Customer/artworks.php';
    }

    public function livePreview()
    {
        $customer = Customer::getCurrentCustomer();
        if (!$customer) {
            $_SESSION['error'] = "You must be logged in to view your artworks.";
            header('Location: /index');
            exit;
        }
        $artworks = $customer->getCart()->getItems();
        if (!$artworks) {
            $_SESSION['error'] = "Artwork not found.";
            header('Location: /customer/dashboard');
            exit;
        }
        $selectedArtworks  = [];
        include_once  VIEWS . 'pages/Customer/live-preview.php';
    }

    public function updateProfile()
    {
        $customer = Customer::getCurrentCustomer();
        if (!$customer) {
            $_SESSION['error'] = "You must be logged in to update your profile.";
            header('Location: /index');
            exit;
        }

        if (isset($_FILES['profilePic']) && $_FILES['profilePic']['error'] === UPLOAD_ERR_OK) {
            $upload = App::handleUploadImage($_FILES['profilePic'], 'profiles');
            if ($upload === false) {
                $_SESSION['error'] = "Failed to upload profile picture.";
                header('Location: /customer/profile');
                exit;
            }
        }

        $updateResult = $customer->updateProfile([
            "email" => $_POST['email'] ?? null,
            'firstName' => $_POST['firstName'] ?? null,
            'lastName' => $_POST['lastName'] ?? null,
            'profilePic' => $upload ?? null,
            'phone' => $_POST['phone'] ?? null,
            'address' => $_POST['address'] ?? null,
            'city' => $_POST['city'] ?? null,
            'postal_code' => $_POST['postal_code'] ?? null,
            'currency' => $_POST['currency'] ?? null
        ]);
        if ($updateResult) {
            $_SESSION['success'] = "Profile updated successfully.";
        } else {
            $_SESSION['error'] = "Failed to update profile. " . $_SESSION['error'];
        }
        header('Location: /customer/profile');
        exit;
    }

    public function changePassword()
    {
        $customer = Customer::getCurrentCustomer();
        if (!$customer) {
            $_SESSION['error'] = "You must be logged in to change your password.";
            header('Location: /index');
            exit;
        }

        $currentPassword = $_POST['currentPassword'] ?? null;
        $newPassword = $_POST['newPassword'] ?? null;
        $confirmPassword = $_POST['confirmPassword'] ?? null;

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $_SESSION['error'] = "All fields are required.";
            header('Location: /customer/profile');
            exit;
        }

        if (strlen($newPassword) < 8 || !preg_match('/[A-Z]/', $newPassword) || !preg_match('/[0-9]/', $newPassword)) {
            $_SESSION['error'] = "New password must be at least 8 characters long and contain at least one uppercase letter and one number.";
            header('Location: /customer/profile');
            exit;
        }

        if ($newPassword === $currentPassword) {
            $_SESSION['error'] = "New password cannot be the same as the current password.";
            header('Location: /customer/profile');
            exit;
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = "New password and confirmation do not match.";
            header('Location: /customer/profile');
            exit;
        }


        $updateResult = $customer->changePassword($currentPassword, $newPassword);
        if ($updateResult) {
            $_SESSION['success'] = "Password updated successfully.";
        } else {
            $_SESSION['error'] = "Failed to update password. " . $_SESSION['error'];
        }
        header('Location: /customer/profile');
        exit;
    }

    public function updatePayment()
    {
        if (!isset($_POST['expYear']) || !isset($_POST['expMonth']) || !isset($_POST['cardNumber']) || !isset($_POST['cvvNumber'])) {
            $_SESSION['error'] = 'Invalid request';
            header('Location: /customer/profile');
            exit;
        }
        $expYear = $_POST['expYear'];
        $expMonth = $_POST['expMonth'];
        $cardNumber = str_replace("-", "", $_POST['cardNumber']);
        $cvvNumber = $_POST['cvvNumber'];

        if (empty($expYear) || empty($expMonth) || empty($cardNumber) || empty($cvvNumber)) {
            $_SESSION['error'] = 'All fields are required';
            header('Location: /customer/profile');
            exit;
        }
        if (!is_numeric($cardNumber) || strlen($cardNumber) != 16) {
            $_SESSION['error'] = 'Card number must be a 16-digit number';
            header('Location: /customer/profile');
            exit;
        }

        if (!is_numeric($cvvNumber) || strlen($cvvNumber) != 3) {
            $_SESSION['error'] = 'CVV number must be a 3-digit number';
            header('Location: /customer/profile');
            exit;
        }
        if (!is_numeric($expYear) || strlen($expYear) != 4) {
            $_SESSION['error'] = 'Expiration year must be a 4-digit number';
            header('Location: /customer/profile');
            exit;
        }
        if (!is_numeric($expMonth) || strlen($expMonth) > 2) {
            $_SESSION['error'] = 'Expiration month must be a 2-digit number';
            header('Location: /customer/profile');
            exit;
        }
        if ($expMonth < 1 || $expMonth > 12) {
            $_SESSION['error'] = 'Expiration month must be between 01 and 12';
            header('Location: /customer/profile');
            exit;
        }
        if ($expYear < date('Y')) {
            $_SESSION['error'] = 'Expiration year must be greater than or equal to the current year';
            header('Location: /customer/profile');
            exit;
        }
        if ($expYear == date('Y') && $expMonth < date('m')) {
            $_SESSION['error'] = 'Expiration month must be greater than or equal to the current month';
            header('Location: /customer/profile');
            exit;
        }
        $customer = Customer::getCurrentCustomer();

        if (!$customer) {
            $_SESSION['error'] = 'You must be logged in as a customer to perform this action';
            header('Location: /index');
            exit;
        }

        $success = $customer->updatePayment([
            'expYear' => $expYear,
            'expMonth' => $expMonth,
            'cardNumber' => $cardNumber,
            'cvv' => $cvvNumber,
        ]);

        if ($success) {
            $_SESSION['success'] = "Payment information has been updated successfully";
        } else {
            $_SESSION['error'] = "Failed to update payment information: " . $_SESSION['error'];
        }
        header('Location: /customer/profile');
    }

    public function artAdvisor()
    {
        $customer = Customer::getCurrentCustomer();
        if (!$customer) {
            $_SESSION['error'] = "You must be logged in to view your artworks.";
            header('Location: /index');
            exit;
        }
        if (!isset($_POST['art_style']) || !isset($_POST['budget']) || !isset($_POST['art_type'])) {
            $_SESSION['error'] = "All fields are required.";
            header('Location: /customer/dashboard');
            exit;
        }
        $art_style = $_POST['art_style'];
        $budget = $_POST['budget'];
        $art_type = $_POST['art_type'];
        if (empty($art_style) || empty($budget) || empty($art_type)) {
            $_SESSION['error'] = "All fields are required.";
            header('Location: /customer/dashboard');
            exit;
        }

        $questions = new Questionnaire($customer->getUserID(), $art_style, $budget, $art_type);
        $success = $questions->create();
        if ($success) {
            $_SESSION['success'] = "Questionnaire submitted successfully.";
        } else {
            $_SESSION['error'] = "Failed to submit questionnaire. " . $_SESSION['error'];
        }
        header('Location: /customer/dashboard');
    }

}
