<?php

namespace App\models;

use App\core\Database;
use App\models\Customer;

class Egift
{
    private $amount;
    private $code;
    private $claimed;
    private $ownerID;
    private $recipientID;
    private $style;


    public function __construct($data)
    {
        $this->amount = $data['amount'] ?? null;
        $this->code = $data['code'] ?? null;
        $this->claimed = $data['claimed'] ?? null;
        $this->ownerID = $data['ownerID'] ?? null;
        $this->recipientID = $data['recipientID'] ?? null;
        $this->style = $data['style'] ?? null;
    }

    public function createEgift()
    {
        try {
            $sql = "INSERT INTO egift (amount, code, claimed, ownerID, recipientID, style) VALUES (:amount, :code, :claimed, :ownerID, :recipientID, :style)";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':amount', $this->amount);
            $stmt->bindParam(':code', $this->code);
            $stmt->bindParam(':ownerID', $this->ownerID);
            $stmt->bindParam(':recipientID', $this->recipientID);
            $stmt->bindParam(':style', $this->style);
            return $stmt->execute();
        } catch (\PDOException $e) {
            $_SESSION['error'] =  $e->getMessage();
            return false;
        }
    }

    public function getEgiftByCode($code)
    {
        $sql = "SELECT * FROM egift WHERE code = :code";
        $stmt = Database::getInstance()->getConnection()->prepare($sql);
        $stmt->bindParam(':code', $code, \PDO::PARAM_STR);
        $stmt->execute();
        return new self($stmt->fetch(\PDO::FETCH_ASSOC));
    }


    public function redeem($code, $recipientID)
    {
        try {
            $sql = "UPDATE egift SET claimed = 1, recipientID = :recipientID WHERE code = :code";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':code', $code, \PDO::PARAM_STR);
            $stmt->bindParam(':recipientID', $recipientID, \PDO::PARAM_INT);

            if ($stmt->execute()) {
                $customer = new Customer();
                $customer->getCustomerById($recipientID);
                $customer->updateBalance($this->amount);
                $_SESSION['success'] = "Egift claimed successfully!";
                return true;
            } else {
                $_SESSION['error'] = "Error claiming egift";
                return false;
            }
        } catch (\PDOException $e) {
            $_SESSION['error'] =  $e->getMessage();
            return false;
        }
    }


    public function getStatus()
    {
        return $this->claimed;
    }
}
