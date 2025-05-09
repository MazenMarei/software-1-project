<?php

namespace App\models;

use App\core\Database;

class ArtistPayments
{
    private $paymentMethodID;
    private $artistID;
    private $cardNumber;
    private $expiryMonth;
    private $expiryYear;
    private $cvv;

    public function __construct($artistID, $cardNumber, $expiryMonth, $expiryYear, $cvv)
    {
        $this->artistID = $artistID;
        $this->cardNumber = $cardNumber;
        $this->expiryMonth = $expiryMonth;
        $this->expiryYear = $expiryYear;
        $this->cvv = $cvv;
    }
    public function getPaymentMethodID()
    {
        return $this->paymentMethodID;
    }
    public function getArtistID()
    {
        return $this->artistID;
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
            $sql = "INSERT INTO artist_payments (artistID, cardNumber, expMonth, expYear, cvv) VALUES (:artistID, :cardNumber, :expMonth, :expYear, :cvv)";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':artistID', $this->artistID);
            $stmt->bindParam(':cardNumber', $this->cardNumber);
            $stmt->bindParam(':expMonth', $this->expiryMonth);
            $stmt->bindParam(':expYear', $this->expiryYear);
            $stmt->bindParam(':cvv', $this->cvv);

            return $stmt->execute();
        } catch (\Throwable $th) {
            $_SESSION['error'] = "Error saving payment method: " . $th->getMessage();
            return false;
        }
    }

    public static function getPaymentMethodByArtistId($artistID)
    {
        try {
            $sql = "SELECT * FROM artist_payments WHERE artistID = :artistID";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':artistID', $artistID, \PDO::PARAM_INT);
            $stmt->execute();
            $paymentMethod = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!$paymentMethod) {
                return false;
            }
            return new self(
                $paymentMethod['artistID'],
                $paymentMethod['cardNumber'],
                $paymentMethod['expMonth'],
                $paymentMethod['expYear'],
                $paymentMethod['cvv']
            );
        } catch (\Throwable $th) {
            $_SESSION['error'] = "Error fetching payment method: " . $th->getMessage();
            return false;
        }
    }

    public function updatePaymentMethod($artistID, $data)
    {
        try {
            $sql = "UPDATE artist_payments SET cardNumber = :cardNumber, expMonth = :expMonth, expYear = :expYear, cvv = :cvv WHERE artistID = :artistID";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':artistID', $artistID, \PDO::PARAM_INT);
            $stmt->bindParam(':cardNumber', $data['cardNumber']);
            $stmt->bindParam(':expMonth', $data['expMonth']);
            $stmt->bindParam(':expYear', $data['expYear']);
            $stmt->bindParam(':cvv', $data['cvv']);

            return $stmt->execute();
        } catch (\Throwable $th) {
            $_SESSION['error'] = "Error updating payment method: " . $th->getMessage();
            return false;
        }
    }
}
