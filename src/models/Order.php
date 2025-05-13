<?php

namespace App\models;

use App\core\Database;

class Order
{
    private $orderID;
    private $customerID;
    private $transactionID;
    private $status;
    private $totalPrice;
    private $date;
    private $items;
    private $shippingAddress;
    private $shippingCity;
    private $shippingPostalCode;

    public function __construct($customerID, $transactionID, $status, $totalPrice, $shippingAddress, $shippingCity, $shippingPostalCode)
    {
        $this->customerID = $customerID;
        $this->transactionID = $transactionID;
        $this->status = $status;
        $this->totalPrice = $totalPrice;
        $this->shippingAddress = $shippingAddress;
        $this->shippingCity = $shippingCity;
        $this->shippingPostalCode = $shippingPostalCode;
    }
    public function getOrderById($orderID)
    {
        $sql = "SELECT * FROM `order` WHERE orderID = :orderID";
        $stmt = Database::getInstance()->getConnection()->prepare($sql);
        $stmt->bindParam(':orderID', $orderID);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    public function createOrder()
    {
        try {
            $sql = "INSERT INTO `order` (customerID, transactionID, orderStatus, totalPrice, postal_code, city, address) VALUES (:customerID, :transactionID, :status, :totalPrice, :shippingPostalCode, :shippingCity, :shippingAddress)";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':customerID', $this->customerID);
            $stmt->bindParam(':transactionID', $this->transactionID);
            $stmt->bindParam(':status', $this->status);
            $stmt->bindParam(':totalPrice', $this->totalPrice);
            $stmt->bindParam(':shippingAddress', $this->shippingAddress);
            $stmt->bindParam(':shippingCity', $this->shippingCity);
            $stmt->bindParam(':shippingPostalCode', $this->shippingPostalCode);
            $stmt->execute();
            return $this->orderID = Database::getInstance()->getConnection()->lastInsertId();
        } catch (\PDOException $e) {
            $_SESSION['error'] =  $e->getMessage();
            return false;
        }
    }

    public static function getAllOrders()
    {
        $sql = "SELECT 
                        o.orderID,
                        o.totalPrice,
                        o.orderDate,
                        o.orderStatus,
                        COUNT(oa.artworkID) AS totalItems,
                        c.customerID,
                        u.Fname,
                        u.Lname,
                        u.username,
                        u.profilePic AS customerPic
                    FROM 
                        `order` o
                    JOIN 
                        customer c ON o.customerID = c.customerID
                    JOIN 
                        user u ON c.customerID = u.userID
                    LEFT JOIN 
                        order_artwork oa ON o.orderID = oa.orderID
                    GROUP BY 
                        o.orderID;";
        $stmt = Database::getInstance()->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getOrderID()
    {
        return $this->orderID;
    }

    public function getCustomerID()
    {
        return $this->customerID;
    }
    public function getTransactionID()
    {
        return $this->transactionID;
    }
    public function getStatus()
    {
        return $this->status;
    }
    public function getTotalPrice()
    {
        return $this->totalPrice;
    }
    public function getDate()
    {
        return $this->date;
    }
    public function getItems()
    {
        return $this->items;
    }
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }
    public function getShippingCity()
    {
        return $this->shippingCity;
    }
    public function getShippingPostalCode()
    {
        return $this->shippingPostalCode;
    }
    public function setStatus($status)
    {
        $this->status = $status;
    }
}
