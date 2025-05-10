<?php

namespace App\models;   
use App\core\Database;

class Offer
{
    private $offerID;
    private $discount;
    private $enabled;
    private static $instance;


    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }





    public function __construct()
    {
        $offer = $this->isExists();
        if ($offer) {
            $this->offerID = $offer['offerID'];
            $this->discount = $offer['discount'];
            $this->enabled = $offer['enabled'];
        } else {
            $this->createOffer();
        }
    }

    public function updateOffer($data)
    {
        try {
            $sql = "UPDATE offer";
            if (isset($data['discount'])) {
                $sql .= " SET discount = :discount";
                $this->discount = $data['discount'];
            }
            if (isset($data['enabled'])) {
                $sql .= ", enabled = :enabled";
                $this->enabled = $data['enabled'];
            }

            $sql .= " WHERE offerID = :offerID";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);

            if (isset($data['discount'])) {
                $stmt->bindParam(':discount', $this->discount);
            }
            if (isset($data['enabled'])) {
                $stmt->bindParam(':enabled', $this->enabled);
            }

            $stmt->bindParam(':offerID', $this->offerID);

            $stmt->execute();
            if (isset($data['discount'])) {
                $this->discount = $data['discount'];
            }
            if (isset($data['enabled'])) {
                $this->enabled = $data['enabled'];
            }
            return true;
        } catch (\PDOException $e) {
            $_SESSION['error'] =  $e->getMessage();
            return false;
        }
    }




    private function createOffer()
    {
        try {
            $sql = "INSERT INTO offer (discount, enabled) VALUES (0, 0)";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->execute();
            $this->offerID = Database::getInstance()->getConnection()->lastInsertId();
            $this->discount = 0;
            $this->enabled = 0;
            return true;
        } catch (\PDOException $e) {
            $_SESSION['error'] =  "lool ".$e->getMessage();
            return false;
        }
    }

    private function isExists()
    {
        $sql = "SELECT * FROM offer";
        $stmt = Database::getInstance()->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }


    public function getDiscount()
    {
        return $this->discount;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }
}
