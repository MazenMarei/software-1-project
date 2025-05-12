<?php


namespace App\models;

use App\core\Database;

class Cart
{
    private $cartID;
    private $customerID;
    private $artworks = [];
    private $totalPrice = 0.00;



    public function __construct($customerID)
    {
        $this->customerID = $customerID;
        $this->cartID = $this->createCart() ?? null;
        $this->getItems();
    }


    private function createCart()
    {
        if ($this->checkExists()) {
            return $this->cartID;
        } else {
            $sql = "INSERT INTO cart_customer (customerID) VALUES (:customerID)";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':customerID', $this->customerID, \PDO::PARAM_INT);
            if ($stmt->execute()) {
                return Database::getInstance()->getConnection()->lastInsertId();
            } else {
                return false;
            }
        }
    }

    private function checkExists()
    {
        $sql = "SELECT * FROM cart_customer WHERE customerID = :customerID";
        $stmt = Database::getInstance()->getConnection()->prepare($sql);
        $stmt->bindParam(':customerID', $this->customerID, \PDO::PARAM_INT);
        $stmt->execute();
        $cart = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($cart) {
            $this->cartID = $cart['cartID'];
            return true;
        } else {
            return false;
        }
    }


    public function getItems()
    {
        try {
            $sql = "SELECT * FROM cart_art WHERE cartID = :cartID";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':cartID', $this->cartID, \PDO::PARAM_INT);
            $stmt->execute();
            $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if ($items) {
                $this->artworks = [];
                $this->totalPrice = 0.00;
                foreach ($items as $item) {
                    $artwork = Artwork::getArtworkById($item['artworkID']);
                    if (!$artwork) {
                        continue;
                    }
                    array_push(
                        $this->artworks,
                        $artwork
                    );
                    $this->totalPrice += $artwork->getPrice();
                }
                return $this->artworks;
            } else {
                return [];
            }
        } catch (\Throwable $th) {
            $_SESSION['error'] = "Error fetching cart items: " . $th->getMessage();
            return false;
        }
    }

    public function addItem($artworkID)
    {
        try {
            if (empty($artworkID) || $artworkID == null || $artworkID == "") {
                $_SESSION['error'] = "Invalid artwork ID";
                return false;
            }
            $artwork = Artwork::getArtworkById($artworkID);
            if (!$artwork) {
                $_SESSION['error'] = "Artwork not found";
                return false;
            }
            if ($artwork->getStatus() !== 'Accepted') {
                $_SESSION['error'] = "Artwork is not available";
                return false;
            }
            if (in_array($artwork, $this->artworks)) {
                $_SESSION['error'] = "Artwork already in cart";
                return false;
            }

            $sql = "INSERT INTO cart_art (cartID, artworkID) VALUES (:cartID, :artworkID)";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':cartID', $this->cartID, \PDO::PARAM_INT);
            $stmt->bindParam(':artworkID', $artworkID, \PDO::PARAM_INT);
            if ($stmt->execute()) {
                array_push($this->artworks, $artwork);
                $this->totalPrice += $artwork->getPrice();
                return true;
            }
        } catch (\Throwable $th) {
            $_SESSION['error'] = "Error adding item to cart: " . $th->getMessage();
            return false;
        }
    }
    public function removeItem($artworkID)
    {
        try {
            if (empty($artworkID) || $artworkID == null || $artworkID == "") {
                $_SESSION['error'] = "Invalid artwork ID";
                return false;
            }
            $artwork = Artwork::getArtworkById($artworkID);
            if (!$artwork) {
                $_SESSION['error'] = "Artwork not found";
                return false;
            }
            if (!in_array($artwork, $this->artworks)) {
                $_SESSION['error'] = "Artwork not in cart";
                return false;
            }

            $sql = "DELETE FROM cart_art WHERE cartID = :cartID AND artworkID = :artworkID";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':cartID', $this->cartID, \PDO::PARAM_INT);
            $stmt->bindParam(':artworkID', $artworkID, \PDO::PARAM_INT);
            if ($stmt->execute()) {
                $this->artworks = array_filter($this->artworks, function ($artwork) use ($artworkID) {
                    return $artwork->getArtworkID() !== $artworkID;
                });
                $this->totalPrice -= $artwork->getPrice();
                if ($this->totalPrice < 0) {
                    $this->totalPrice = 0.00;
                }
                return true;
            }
            return false;
        } catch (\Throwable $th) {
            $_SESSION['error'] = "Error removing item from cart: " . $th->getMessage();
            return false;
        }
    }

    public function getTotalPrice()
    {
        return $this->totalPrice;
    }
    public function checkOut() {}
}
