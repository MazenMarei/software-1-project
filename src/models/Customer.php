<?php

namespace App\models;

use App\core\Database;

class Customer extends User
{
    private $phone;
    private $address;
    private $city;
    private $postal_code;
    private $currency;
    private $balance;
    private $cartID;

    private $cart;
    private $paymentMethod;

    public function __construct($firstName = null, $lastName = null, $email = null, $username = null, $profilePic = null, $phone = null, $address = null, $currency = 'USD', $balance = 0.00)
    {
        parent::__construct($firstName, $lastName, $email, 'customer', $username, $profilePic);
        $this->phone = $phone;
        $this->address = $address;
        $this->currency = $currency;
        $this->balance = $balance;
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
            $this->userID = $customer['customerID'];
            $this->phone = $customer['phone'];
            $this->address = $customer['address'];
            $this->currency = $customer['currency'];
            $this->balance = $customer['balance'];
            $this->postal_code = $customer['postal_code'];
            $this->city = $customer['city'];
            $this->cart = new Cart($this->userID);
            return $this;
        } else {
            return false;
        }
    }

    public function updateProfile($data)
    {
        try {
            // First update the user table
            $userUpdateSuccess = parent::updateProfile($data);

            if (!$userUpdateSuccess) {
                return false;
            }

            // Then update the customer table
            $sql = "UPDATE customer SET ";
            if (isset($data['phone'])) {
                $sql .= "phone = :phone,";
            }
            if (isset($data['address'])) {
                $sql .= "address = :address ,";
            }
            if (isset($data['city'])) {
                $sql .= "city = :city ,";
            }
            if (isset($data['postal_code'])) {
                $sql .= "postal_code = :postal_code ,";
            }
            if (isset($data['currency'])) {
                $sql .= "currency = :currency ,";
            }
            if ($sql !== "UPDATE artist SET ") {
                
                $sql = rtrim($sql, ',') . " WHERE customerID = :id";
                $stmt = Database::getInstance()->getConnection()->prepare($sql);
            } else {
                return true;
            }
            $stmt = Database::getInstance()->getConnection()->prepare($sql);

            if (isset($data['phone'])) {
                $stmt->bindParam(':phone', $data['phone'], \PDO::PARAM_STR);
            }
            if (isset($data['address'])) {
                $stmt->bindParam(':address', $data['address'], \PDO::PARAM_STR);
            }
            if (isset($data['city'])) {
                $stmt->bindParam(':city', $data['city'], \PDO::PARAM_STR);
            }
            if (isset($data['postal_code'])) {
                $stmt->bindParam(':postal_code', $data['postal_code'], \PDO::PARAM_STR);
            }
            if (isset($data['currency'])) {
                $stmt->bindParam(':currency', $data['currency'], \PDO::PARAM_STR);
            }

            $stmt->bindParam(':id', $this->userID, \PDO::PARAM_INT);


            if ($stmt->execute()) {
                $this->phone = $data['phone'];
                $this->address = $data['address'];
                $this->city = $data['city'];
                $this->postal_code = $data['postal_code'];
                $this->currency = $data['currency'];
                return true;
            }
            return false;
        } catch (\Throwable $th) {
            $_SESSION['error'] = "Failed to update profile. " . $th->getMessage();
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

    public function getPaymentMethod()
    {
        try {
            $paymentMethod = Payments::getPaymentMethodByUserId($this->userID);
            if (!$paymentMethod) {
                return false;
            }
            $this->paymentMethod = $paymentMethod;
            return $this->paymentMethod;
        } catch (\Throwable $th) {
            $_SESSION['error'] = $th->getMessage();
            return false;
        }
    }
    private function addPaymentMethod($data)
    {
        try {

            $payment = new Payments([
                'userID' => $this->userID,
                'cardNumber' => $data['cardNumber'],
                'expMonth' => $data['expMonth'],
                'expYear' => $data['expYear'],
                'cvv' => $data['cvv']
            ]);
            return $payment->save();
        } catch (\Throwable $th) {
            $_SESSION['error'] =  $th->getMessage();
            return false;
        }
    }
    public function updatePayment($data)
    {
        try {
            /// check if there is a payment method
            $paymentMethod = $this->getPaymentMethod();
            if (!$paymentMethod) {
                return $this->addPaymentMethod($data);
            } else {
                $payment = new Payments($data);
                $_SESSION['success'] = "Payment method updated successfully 214124.";
                $_SESSION['error'] = $data['cardNumber'];
                return $payment->updatePaymentMethod($this->userID, $data);
            }
        } catch (\Throwable $th) {
            $_SESSION['error'] =  $th->getMessage();
            return false;
        }
    }


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


    public function getOrders() {
        $sql = "SELECT * FROM orders WHERE customerID = :id";
        $stmt = Database::getInstance()->getConnection()->prepare($sql);
        $stmt->bindParam(':id', $this->userID, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getCart()
    {
        return $this->cart;
    }

    // Getters
    public function getCustomerID()
    {
        return $this->userID;
    }

    public function getPhone()
    {
        return $this->phone;
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
    public function getCity()
    {
        return $this->city;
    }
    public function getPostalCode()
    {
        return $this->postal_code;
    }
}
