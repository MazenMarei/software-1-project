<?php

namespace App\models;

use App\core\Database;

class Artist extends User
{
    private $artistID; /// 
    private $phone;
    private $bio;
    private $balance;
    private $address;
    private $bDate;

    public function __construct($firstName = null, $lastName = null, $email = null, $username = null, $profilePic = null, $phone = null, $bio = null, $address = null, $bDate = null)
    {
        parent::__construct($firstName, $lastName, $email, 'artist', $username, $profilePic);
        $this->phone = $phone;
        $this->bio = $bio;
        $this->balance = 0.00;
        $this->address = $address;
        $this->bDate = $bDate;
    }

    public function getArtistById($id)
    {
        // First get the user data
        parent::getUserById($id);

        // Then get the artist-specific data
        $sql = "SELECT * FROM artist WHERE artistID = :id";
        $stmt = Database::getInstance()->getConnection()->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();

        $artist = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($artist) {
            $this->artistID = $artist['artistID'];
            $this->phone = $artist['phone'];
            $this->bio = $artist['Bio'];
            $this->balance = $artist['Balance'];
            $this->address = $artist['address'];
            $this->bDate = $artist['BDate'];
            return $this;
        } else {
            return false;
        }
    }

    /**
     * Update artist profile
     * 
     * @param array $data Profile data to update
     * @return bool Success status
     */
    public function updateArtistProfile($data)
    {
        try {
            // First update the user table
            $userUpdateSuccess = parent::updateProfile($data);

            if (!$userUpdateSuccess) {
                return false;
            }

            // Then update the artist table
            $sql = "UPDATE artist SET 
                phone = :phone, 
                Bio = :bio, 
                address = :address,
                BDate = :bDate
                WHERE artistID = :id";

            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':phone', $data['phone'], \PDO::PARAM_STR);
            $stmt->bindParam(':bio', $data['bio'], \PDO::PARAM_STR);
            $stmt->bindParam(':address', $data['address'], \PDO::PARAM_STR);
            $stmt->bindParam(':bDate', $data['bDate'], \PDO::PARAM_STR);
            $stmt->bindParam(':id', $this->userID, \PDO::PARAM_INT);

            if ($stmt->execute()) {
                $this->phone = $data['phone'];
                $this->bio = $data['bio'];
                $this->address = $data['address'];
                $this->bDate = $data['bDate'];
                return true;
            }
            return false;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Update artist balance
     * 
     * @param float $amount Amount to add to balance (use negative for withdrawal)
     * @return bool Success status
     */
    public function updateBalance($amount)
    {
        try {
            $sql = "UPDATE artist SET Balance = Balance + :amount WHERE artistID = :id";
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

    /**
     * Get artist's artworks
     * 
     * @param string $status Optional status filter
     * @return array Artworks
     */
    public function getArtworks($status = null)
    {
        try {
            $sql = "SELECT * FROM artwork WHERE artistID = :artistID";

            if ($status) {
                $sql .= " AND status = :status";
            }

            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':artistID', $this->userID, \PDO::PARAM_INT);

            if ($status) {
                $stmt->bindParam(':status', $status, \PDO::PARAM_STR);
            }

            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            return [];
        }
    }

    /**
     * Get artist's collections
     * 
     * @return array Collections
     */
    public function getCollections()
    {
        try {
            $sql = "SELECT * FROM artcollection WHERE atristID = :artistID";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':artistID', $this->userID, \PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            return [];
        }
    }

    /**
     * Get artist's local fairs
     * 
     * @return array Local fairs
     */
    public function getLocalFairs()
    {
        try {
            $sql = "SELECT * FROM localfair WHERE atristID = :artistID";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':artistID', $this->userID, \PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            return [];
        }
    }

    /**
     * Get artist's transactions
     * 
     * @return array Transactions
     */
    public function getTransactions()
    {
        try {
            $sql = "SELECT t.* FROM transaction t
                    JOIN artisttransaction at ON t.transactionID = at.transactionID
                    WHERE at.artistID = :artistID
                    ORDER BY t.datee DESC";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':artistID', $this->userID, \PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            return [];
        }
    }

    /**
     * Get the currently logged-in artist from session
     * 
     * @return Artist|false The current artist object or false if not logged in or not an artist
     */
    public static function getCurrentArtist()
    {
        // First get the current user
        $user = User::getCurrentUser();

        // Check if user exists and is an artist
        if (!$user || $user->getRole() !== 'artist') {
            return false;
        }

        // Create a new Artist object and load data
        $artist = new Artist();
        return $artist->getArtistById($user->getUserID());
    }

    // Getters
    public function getArtistID()
    {
        return $this->artistID;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function getBio()
    {
        return $this->bio;
    }

    public function getBalance()
    {
        return $this->balance;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function getBDate()
    {
        return $this->bDate;
    }

    // Setters
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    public function setBio($bio)
    {
        $this->bio = $bio;
    }

    public function setBalance($balance)
    {
        $this->balance = $balance;
    }

    public function setAddress($address)
    {
        $this->address = $address;
    }

    public function setBDate($bDate)
    {
        $this->bDate = $bDate;
    }
}
