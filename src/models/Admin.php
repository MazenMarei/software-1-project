<?php

namespace App\models;

use App\core\Database;


class Admin extends User
{
    public function __construct($firstName = null, $lastName = null, $email = null, $username = null, $profilePic = null)
    {
        parent::__construct($firstName, $lastName, $email, 'admin', $username, $profilePic);
    }


    private function getAdminById($id)
    {
        // First get the user data
        $parent = parent::getUserById($id);
        if (!$parent) {
            return false;
        }
        // Then get the admin-specific data
        $sql = "SELECT * FROM admin WHERE adminID = :id";
        $stmt = Database::getInstance()->getConnection()->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();

        $admin = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($admin) {
            $this->userID = $admin['adminID'];
            return $this;
        } else {
            return false;
        }
    }


    public function getWithdrawRequests()
    {
        try {
            $sql = "SELECT 
            t.transactionID,
            t.amount,
            t.date AS transactionDate,
            t.type,
            t.status,
            a.artistID,
            u.Fname AS artistFirstName,
            u.Lname AS artistLastName,
            u.Email AS artistEmail,
            u.profilePic AS artistProfilePic,
            a.Balance AS artistBalance,
            a.phone AS artistPhone
        FROM 
            transaction t
        JOIN 
            artisttransaction at ON t.transactionID = at.transactionID
        JOIN 
            artist a ON at.artistID = a.artistID
        JOIN 
            user u ON a.artistID = u.userID
        ORDER BY 
            t.date DESC;";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->execute();

            $trasactionsData = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if (!$trasactionsData) {
                return [];
            }
            return $trasactionsData;
        } catch (\Throwable $th) {
            $_SESSION['error'] = 'Error fetching withdraw requests: ' . $th->getMessage();
            return [];
        }
    }


    public function updateUserStatus($userId, $status, $reason = '')
    {
        try {
            Database::getInstance()->getConnection()->beginTransaction();

            $sql = "UPDATE user SET status = :status WHERE userID = :id";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':status', $status, \PDO::PARAM_STR);
            $stmt->bindParam(':id', $userId, \PDO::PARAM_INT);
            $stmt->execute();


            if ($stmt->rowCount() === 0) {
                Database::getInstance()->getConnection()->rollBack();
                return false;
            }
            // If there's a reason, add a notification
            if ($reason && $status === 'Rejected') {
                $message = "Your account registration was rejected. Reason: $reason";
                $this->sendNotification($userId, $message);
            } elseif ($status === 'Accepted') {
                $message = "Your account has been approved. Welcome to ArtShelf!";
                $this->sendNotification($userId, $message);
            }

            Database::getInstance()->getConnection()->commit();
            return true;
        } catch (\Throwable $th) {
            // Database::getInstance()->getConnection()->rollBack();
            $_SESSION['error'] = 'Error updating user status: ' . $th->getMessage();
            return false;
        }
    }

 
    public function updateArtworkStatus($artworkId, $status, $reason = '')
    {
        try {
            Database::getInstance()->getConnection()->beginTransaction();

            // First get the artwork to find the artist
            $sql = "SELECT artistID FROM artwork WHERE artworkID = :id";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':id', $artworkId, \PDO::PARAM_INT);
            $stmt->execute();
            $artwork = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$artwork) {
                Database::getInstance()->getConnection()->rollBack();
                return false;
            }

            // Update artwork status
            $sql = "UPDATE artwork SET status = :status WHERE artworkID = :id";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':status', $status, \PDO::PARAM_STR);
            $stmt->bindParam(':id', $artworkId, \PDO::PARAM_INT);
            $stmt->execute();

            // Add notification to the artist
            $artistId = $artwork['artistID'];
            if ($reason && $status === 'Rejected') {
                $message = "Your artwork submission was rejected. Reason: $reason";
                $this->sendNotification($artistId, $message);
            } elseif ($status === 'Approved') {
                $message = "Your artwork has been approved and is now available on ArtShelf!";
                $this->sendNotification($artistId, $message);
            }

            Database::getInstance()->getConnection()->commit();
            return true;
        } catch (\Throwable $th) {
            // Database::getInstance()->getConnection()->rollBack();
            $_SESSION['error'] = 'Error updating artwork status: ' . $th->getMessage();
            return false;
        }
    }


    public function sendNotification($userId, $message)
    {
        try {
            $sql = "INSERT INTO notification (userid, Message, datesent) 
                    VALUES (:userId, :message, :dateSent)";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);

            $dateSent = date('Y-m-d');

            $stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
            $stmt->bindParam(':message', $message, \PDO::PARAM_STR);
            $stmt->bindParam(':dateSent', $dateSent, \PDO::PARAM_STR);

            return $stmt->execute();
        } catch (\Throwable $th) {
            $_SESSION['error'] = 'Error adding notification: ' . $th->getMessage();
            return false;
        }
    }

    public static function getCurrentAdmin()
    {

        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id']) || !isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'admin') {
            return false;
        }

        $admin = new Admin();
        return $admin->getAdminById($_SESSION['user']['id']);
    }


    public function getAllArtists()
    {
        try {
            $sql = "SELECT  u.* FROM user u
                    INNER JOIN artist a ON u.userID = a.artistID
                    WHERE u.role = :role";


            // Single ORDER BY with valid column
            $sql .= " ORDER BY u.registerDate DESC";

            $stmt = Database::getInstance()->getConnection()->prepare($sql);

            $stmt->bindValue(':role', 'artist', \PDO::PARAM_STR);


            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            error_log('Error in getAllArtists: ' . $th->getMessage());
            return [];
        }
    }


    public function updateArtFairStatus($artFairId, $status, $reason = '')
    {

        try {
            $update = ArtFair::updateArtFairStatusById($artFairId, $status);
            $_SESSION['success'] = "Artist ID " . $update->getArtistID();
            if (!$update) {
                return false;
            }
            if ($reason && $status === 'rejected') {
                $message = "Your local fair registration was rejected. Reason: $reason";
                if ($update->getArtistID() !== null) {
                    $this->sendNotification($update->getArtistID(), $message);
                }
            } elseif ($status === 'accepted') {
                $message = "Your local fair has been approved. Welcome to ArtShelf!";
                if ($update->getArtistID() !== null) {
                    $this->sendNotification($update->getArtistID(), $message);
                }
            }
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function updateSpecialCollections($collectionName, $artworksID)
    {
        try {
            $specialCollection = SpecialCollection::getInstance();
            $update = $specialCollection->updateCollection([
                'name' => $collectionName,
            ]);
            if (!$update) {
                $_SESSION['error'] = "Failed to update special collection. " . $_SESSION['error'];
                return false;
            }

            $artworks = $specialCollection->setArtworks($artworksID);
            if (!$artworks) {
                $_SESSION['error'] = "Failed to update artworks in special collection.";
                return false;
            }
            return true;
        } catch (\Throwable $th) {
            $_SESSION['error'] = "Failed to update special collection: " . $th->getMessage();
            return false;
        }
    }


    public function getAllCustomers()
    {
        try {
            $sql = "SELECT u.* FROM user u
                    WHERE u.role = :role";

            // Single ORDER BY with valid column
            $sql .= " ORDER BY u.registerDate DESC";

            $stmt = Database::getInstance()->getConnection()->prepare($sql);

            // Always bind role parameter
            $stmt->bindValue(':role', 'customer', \PDO::PARAM_STR);


            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            error_log('Error in getAllCustomers: ' . $th->getMessage());
            return [];
        }
    }

    public function getAllArtFairs()
    {
        try {
            return ArtFair::getAllArtFairs();
        } catch (\Throwable $th) {
            $_SESSION['error'] = 'Error fetching art fairs: ' . $th->getMessage();
            return [];
        }
    }

    public function getAdminID()
    {
        return $this->userID;
    }
}
