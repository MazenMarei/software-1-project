<?php

namespace App\models;

use App\core\Database;

class Payments
{
    private $paymentMethodID;
    private $userID;
    private $cardNumber;
    private $expiryMonth;
    private $expiryYear;
    private $cvv;

    public function __construct($data)
    {
        $this->userID = $data['userID'] ?? null;
        $this->cardNumber = $data['cardNumber'] ?? null;
        $this->expiryMonth = $data['expMonth'] ?? null;
        $this->expiryYear = $data['expYear'] ?? null;
        $this->cvv = $data['cvv'] ?? null;
    }

    public function getPaymentMethodID()
    {
        return $this->paymentMethodID;
    }
    public function getUserID()
    {
        return $this->userID;
    }
    public function getCardNumber()
    {
        return $this->cardNumber;
    }
    public function getExpiryMonth()
    {
        return $this->expiryMonth;
    }
    public function getExpiryYear()
    {
        return $this->expiryYear;
    }
    public function getCvv()
    {
        return $this->cvv;
    }

    public function save()
    {
        try {
            $sql = "INSERT INTO payments (userID, cardNumber, expMonth, expYear, cvv) VALUES (:userID, :cardNumber, :expMonth, :expYear, :cvv)";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':userID', intval($this->userID));
            $stmt->bindParam(':cardNumber', intval($this->cardNumber));
            $stmt->bindParam(':expMonth', intval($this->expiryMonth));
            $stmt->bindParam(':expYear', intval($this->expiryYear));
            $stmt->bindParam(':cvv', intval($this->cvv));

            return $stmt->execute();
        } catch (\Throwable $th) {
            $_SESSION['error'] = "Error saving payment method: " . $th->getMessage();
            return false;
        }
    }

    public static function getPaymentMethodByUserId($userID)
    {
        try {
            $sql = "SELECT * FROM payments WHERE userID = :userID";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':userID', $userID, \PDO::PARAM_INT);
            $stmt->execute();
            $paymentMethod = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!$paymentMethod) {
                return false;
            }
            return new self(
                [
                    'userID' => $paymentMethod['userID'],
                    'cardNumber' => $paymentMethod['cardNumber'],
                    'expMonth' => $paymentMethod['expMonth'],
                    'expYear' => $paymentMethod['expYear'],
                    'cvv' => $paymentMethod['cvv']
                ]
            );
        } catch (\Throwable $th) {
            $_SESSION['error'] = "Error fetching payment method: " . $th->getMessage();
            return false;
        }
    }

    public function updatePaymentMethod($userID, $data)
    {
        try {

            $sql = "UPDATE payments SET cardNumber = :cardNumber, expMonth = :expMonth, expYear = :expYear, cvv = :cvv WHERE userID = :userID";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':userID', $userID, \PDO::PARAM_INT);
            $stmt->bindParam(':cardNumber', intval($data['cardNumber']));
            $stmt->bindParam(':expMonth', intval($data['expMonth']));
            $stmt->bindParam(':expYear', intval($data['expYear']));
            $stmt->bindParam(':cvv', intval($data['cvv']));

            return $stmt->execute();
        } catch (\Throwable $th) {
            $_SESSION['error'] = "Error updating payment method: " . $th->getMessage();
            return false;
        }
    }
}
