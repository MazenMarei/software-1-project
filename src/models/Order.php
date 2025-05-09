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

       