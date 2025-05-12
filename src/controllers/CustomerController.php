<?php

namespace App\Controllers;

use App\models\Artist;
use App\models\Artwork;
use App\models\Cart;
use App\Models\Customer;
use App\models\SpecialCollection;

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
        $cart = new Cart($customer->getCustomerID());
        $checkoutResult = $cart->checkout();
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
        include_once  VIEWS . 'pages/Customer/profile.php';
    }
}
