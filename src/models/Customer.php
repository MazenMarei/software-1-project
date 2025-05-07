<?php

namespace App\models;

use App\core\Database;

class Customer extends User
{
    private $customerID;
    private $phone;
    private $date;
    private $address;
    private $currency;
    private $balance;
    private $cartID;

    public function __construct($firstName = null, $lastName = null, $email = null, $username = null, $profilePic = null, $phone = null, $address = null, $currency = 'USD', $balance = 0.00)
    {
        parent::__construct($firstName, $lastName, $email, 'customer', $username, $profilePic);
        $this->phone = $phone;
        $this->address = $address;
        $this->currency = $currency;
        $this->balance = $balance;
        $this->date = date('Y-m-d');
    }

    public function getCustomerById($id)
    {
        // First get the user data
        parent::getUserById($id);

        // Then get the customer-specific data
        $sql = "SELECT * FROM customer WHERE customerID = :id";
        $stmt = Database::getInstance()->getConnection()->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();

        $customer = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($customer) {
            $this->customerID = $customer['customerID'];
            $this->phone = $customer['phone'];
            $this->date = $customer['date'];
            $this->address = $customer['address'];
            $this->currency = $customer['currency'];
            $this->balance = $customer['balance'];
            $this->cartID = $customer['cartID'];
            return $this;
        } else {
            return false;
        }
    }

    public function updateCustomerProfile($data)
    {
        try {
            // First update the user table
            $userUpdateSuccess = parent::updateProfile($data);

            if (!$userUpdateSuccess) {
                return false;
            }

            // Then update the customer table
            $sql = "UPDATE customer SET 
                phone = :phone, 
                address = :address, 
                currency = :currency
                WHERE customerID = :id";

            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':phone', $data['phone'], \PDO::PARAM_STR);
            $stmt->bindParam(':address', $data['address'], \PDO::PARAM_STR);
            $stmt->bindParam(':currency', $data['currency'], \PDO::PARAM_STR);
            $stmt->bindParam(':id', $this->userID, \PDO::PARAM_INT);

            if ($stmt->execute()) {
                $this->phone = $data['phone'];
                $this->address = $data['address'];
                $this->currency = $data['currency'];
                return true;
            }
            return false;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function updateBalance($amount)
    {
        try {
            $sql = "UPDATE customer SET balance = balance + :amount WHERE customerID = :id";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':amount', $amount, \PDO::PARAM_STR);
            $stmt->bindParam(':id', $this->userID, \PDO::PARAM_INT);

            if ($stmt->execute()) {
                $this->balance += $amount;
                return true;
            }
            return false;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function getCart()
    {
        if (!$this->cartID) {
            return false;
        }

        $sql = "SELECT a.* FROM artwork a 
                JOIN cart_art ca ON a.artworkID = ca.artworkID
                WHERE ca.cartID = :cartID";
        $stmt = Database::getInstance()->getConnection()->prepare($sql);
        $stmt->bindParam(':cartID', $this->cartID, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get the currently logged-in customer from session
     * 
     * @return Customer|false The current customer object or false if not logged in or not a customer
     */
    public static function getCurrentCustomer()
    {
        // First get the current user
        $user = User::getCurrentUser();

        // Check if user exists and is a customer
        if (!$user || $user->getRole() !== 'customer') {
            return false;
        }

        // Create a new Customer object and load data
        $customer = new Customer();
        return $customer->getCustomerById($user->getUserID());
    }

    // Getters
    public function getCustomerID()
    {
        return $this->customerID;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function getBalance()
    {
        return $this->balance;
    }

    public function getCartID()
    {
        return $this->cartID;
    }

    // Setters
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    public function setAddress($address)
    {
        $this->address = $address;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    public function setBalance($balance)
    {
        $this->balance = $balance;
    }

    public function setCartID($cartID)
    {
        $this->cartID = $cartID;
    }
}
