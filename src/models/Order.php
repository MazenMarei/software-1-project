<?php

namespace App\models;

use App\core\Database;

class Order
{
    private $orderID;
    private $customerID;
    private $transactionID;
    private $status;
    private $quantity;
    private $totalPrice;
    private $date;
    private $items;
    private $shippingAddress;

    public function __construct($customerID, $transactionID, $status, $quantity, $totalPrice, $items)
    {
        $this->customerID = $customerID;
        $this->transactionID = $transactionID;
        $this->status = $status;
        $this->quantity = $quantity;
        $this->totalPrice = $totalPrice;
        $this->items = $items;
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
    public function getQuantity()
    {
        return $this->quantity;
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
    public function setStatus($status)
    {
        $this->status = $status;
    }
}
