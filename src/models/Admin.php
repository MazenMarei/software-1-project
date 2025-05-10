<?php

namespace App\models;

use App\core\Database;

class Admin extends User
{
    private $adminID;
    private $secureCode;

    public function __construct($firstName = null, $lastName = null, $email = null, $username = null, $profilePic = null, $secureCode = null)
    {
        parent::__construct($firstName, $lastName, $email, 'admin', $username, $profilePic);
        $this->secureCode = $secureCode;
    }

    /**
     * Get admin by ID
     * 
     * @param int $id Admin ID
     * @return Admin|false Admin object or false if not found
     */
    public function getAdminById($id)
    {
        // First get the user data
        parent::getUserById($id);

        // Then get the admin-specific data
        $sql = "SELECT * FROM admin WHERE adminID = :id";
        $stmt = Database::getInstance()->getConnection()->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();

        $admin = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($admin) {
            $this->adminID = $admin['adminID'];
            $this->secureCode = $admin['secureCode'];
            return $this;
        } else {
            return false;
        }
    }

    /**
     * Generate a new secure code for the admin
     * 
     * @return bool Success status
     */
    public function generateSecureCode()
    {
        try {
            // Generate a random 6-digit code
            $secureCode = sprintf("%06d", mt_rand(1, 999999));

            $sql = "UPDATE admin SET secureCode = :secureCode WHERE adminID = :id";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':secureCode', $secureCode, \PDO::PARAM_STR);
            $stmt->bindParam(':id', $this->userID, \PDO::PARAM_INT);

            if ($stmt->execute()) {
                $this->secureCode = $secureCode;
                return true;
            }
            return false;
        } catch (\Throwable $th) {
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
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            $_SESSION['error'] = 'Error fetching withdraw requests: ' . $th->getMessage();
            return [];
        }
    }

    /**
     * Verify an admin's secure code
     * 
     * @param string $secureCode Code to verify
     * @return bool True if code matches
     */
    public function verifySecureCode($secureCode)
    {
        return $this->secureCode === $secureCode;
    }

    /**
     * Get pending artist registrations
     * 
     * @return array List of pending artists
     */
    public function getPendingArtists()
    {
        try {
            $sql = "SELECT u.* FROM user u
                    JOIN artist a ON u.userID = a.artistID
                    WHERE u.role = 'artist' AND u.status = 'Pending'";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            return [];
        }
    }

    /**
     * Get pending artworks for approval
     * 
     * @return array List of pending artworks
     */
    public function getPendingArtworks()
    {
        try {
            $sql = "SELECT a.*, u.Fname, u.Lname FROM artwork a
                    JOIN user u ON a.artistID = u.userID
                    WHERE a.status = 'Pending'";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            return [];
        }
    }

    public function getAllArtworks()
    {
        try {
            $sql = "SELECT a.*, u.Fname, u.Lname, u.profilePic FROM artwork a
                    JOIN user u ON a.artistID = u.userID";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            return [];
        }
    }
    /**
     * Get pending local fairs for approval
     * 
     * @return array List of pending local fairs
     */
    public function getPendingLocalFairs()
    {
        try {
            $sql = "SELECT f.*, u.Fname, u.Lname FROM localfair f
                    JOIN user u ON f.atristID = u.userID
                    WHERE f.status = 'Pending'";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            return [];
        }
    }

    /**
     * Approve or reject a user registration
     * 
     * @param int $userId User ID to approve/reject
     * @param string $status New status (Accepted/Rejected)
     * @param string $reason Reason for rejection (optional)
     * @return bool Success status
     */
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
                $this->addNotification($userId, $message);
            } elseif ($status === 'Accepted') {
                $message = "Your account has been approved. Welcome to ArtShelf!";
                $this->addNotification($userId, $message);
            }

            Database::getInstance()->getConnection()->commit();
            return true;
        } catch (\Throwable $th) {
            Database::getInstance()->getConnection()->rollBack();
            return false;
        }
    }

    /**
     * Approve or reject an artwork
     * 
     * @param int $artworkId Artwork ID to approve/reject
     * @param string $status New status (Approved/Rejected)
     * @param string $reason Reason for rejection (optional)
     * @return bool Success status
     */
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
                $this->addNotification($artistId, $message);
            } elseif ($status === 'Approved') {
                $message = "Your artwork has been approved and is now available on ArtShelf!";
                $this->addNotification($artistId, $message);
            }

            Database::getInstance()->getConnection()->commit();
            return true;
        } catch (\Throwable $th) {
            Database::getInstance()->getConnection()->rollBack();
            return false;
        }
    }
    /**
     * Create a special collection
     * 
     * @param string $name Collection name
     * @param string $description Collection description
     * @return bool|int Collection ID on success, false on failure
     */
    public function createSpecialCollection($name, $description)
    {
        try {
            $sql = "INSERT INTO artcollection (createDate, name, description, adminID, special) 
                    VALUES (:createDate, :name, :description, :adminID, 1)";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);

            $createDate = date('Y-m-d');

            $stmt->bindParam(':createDate', $createDate, \PDO::PARAM_STR);
            $stmt->bindParam(':name', $name, \PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, \PDO::PARAM_STR);
            $stmt->bindParam(':adminID', $this->userID, \PDO::PARAM_INT);

            if ($stmt->execute()) {
                return Database::getInstance()->getConnection()->lastInsertId();
            }
            return false;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Add a notification for a user
     * 
     * @param int $userId User ID to notify
     * @param string $message Notification message
     * @return bool Success status
     */
    public function addNotification($userId, $message)
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

    /**
     * Create a new artwork offer
     * 
     * @param int $artworkId Artwork ID for the offer
     * @param int $discount Discount percentage (1-100)
     * @param string $endDate End date (YYYY-MM-DD)
     * @return bool Success status
     */
    public function createOffer($artworkId, $discount, $endDate)
    {
        try {
            if ($discount < 1 || $discount > 100) {
                return false;
            }

            $sql = "INSERT INTO offer (artworkID, discount, enabled, endDate) 
                    VALUES (:artworkID, :discount, 1, :endDate)";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);

            $stmt->bindParam(':artworkID', $artworkId, \PDO::PARAM_INT);
            $stmt->bindParam(':discount', $discount, \PDO::PARAM_INT);
            $stmt->bindParam(':endDate', $endDate, \PDO::PARAM_STR);

            return $stmt->execute();
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Get all transactions
     * 
     * @param string $type Optional transaction type filter
     * @return array List of transactions
     */
    public function getAllTransactions($type = null)
    {
        try {
            $sql = "SELECT * FROM transaction";

            if ($type) {
                $sql .= " WHERE type = :type";
            }

            $sql .= " ORDER BY datee DESC";

            $stmt = Database::getInstance()->getConnection()->prepare($sql);

            if ($type) {
                $stmt->bindParam(':type', $type, \PDO::PARAM_STR);
            }

            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            return [];
        }
    }

    /**
     * Get the currently logged-in admin from session
     * 
     * @return Admin|false The current admin object or false if not logged in or not an admin
     */
    public static function getCurrentAdmin()
    {
        // Make sure session is started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Check if user session data exists and role is admin
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id']) || !isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'admin') {
            return false;
        }

        // Create a new Admin object and load data
        $admin = new Admin();
        return $admin->getAdminById($_SESSION['user']['id']);
    }

    /**
     * Get all artists
     * 
     * @param string $status Optional status filter
     * @return array List of all artists
     */
    public function getAllArtists($status = null)
    {
        try {
            $sql = "SELECT  u.* FROM user u
                    INNER JOIN artist a ON u.userID = a.artistID
                    WHERE u.role = :role";

            // Add status filter if provided
            if ($status !== null) {
                $sql .= " AND u.status = :status";
            }

            // Single ORDER BY with valid column
            $sql .= " ORDER BY u.registerDate DESC";

            $stmt = Database::getInstance()->getConnection()->prepare($sql);

            // Always bind role parameter
            $stmt->bindValue(':role', 'artist', \PDO::PARAM_STR);

            // Conditionally bind status
            if ($status !== null) {
                $stmt->bindValue(':status', $status, \PDO::PARAM_STR);
            }

            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            error_log('Error in getAllArtists: ' . $th->getMessage());
            return [];
        }
    }


    public function updateFairStatus($fairId, $status, $reason = '')
    {

        try {
            $update = ArtFair::updateArtFairStatusById($fairId, $status);
            $_SESSION['success'] = "AArtist ID " . $update->getArtistID();
            if (!$update) {
                return false;
            }
            if ($reason && $status === 'rejected') {
                $message = "Your local fair registration was rejected. Reason: $reason";
                if ($update->getArtistID() !== null) {
                    $this->addNotification($update->getArtistID(), $message);
                }
            } elseif ($status === 'accepted') {
                $message = "Your local fair has been approved. Welcome to ArtShelf!";
                if ($update->getArtistID() !== null) {
                    $this->addNotification($update->getArtistID(), $message);
                }
            }
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }


    public function getAllCollections() {
        try {
       
        } catch (\Throwable $th) {
            return [];
        }
    }

    /**
     * Get all customers
     * 
     * @param string $status Optional status filter
     * @return array List of all customers
     */
    public function getAllCustomers($status = null)
    {
        try {
            $sql = "SELECT u.* FROM user u
                    WHERE u.role = :role";

            // Add status filter if provided
            if ($status !== null) {
                $sql .= " AND u.status = :status";
            }

            // Single ORDER BY with valid column
            $sql .= " ORDER BY u.registerDate DESC";

            $stmt = Database::getInstance()->getConnection()->prepare($sql);

            // Always bind role parameter
            $stmt->bindValue(':role', 'customer', \PDO::PARAM_STR);

            // Conditionally bind status
            if ($status !== null) {
                $stmt->bindValue(':status', $status, \PDO::PARAM_STR);
            }

            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            error_log('Error in getAllCustomers: ' . $th->getMessage());
            return [];
        }
    }


    public function getAllLocalFairs()
    {
        try {
            return ArtFair::getAllArtFairs();
        } catch (\Throwable $th) {
            $_SESSION['error'] = 'Error fetching local fairs: ' . $th->getMessage();
            return [];
        }
    }

    // Getters
    public function getAdminID()
    {
        return $this->adminID;
    }

    public function getSecureCode()
    {
        return $this->secureCode;
    }

    // Setters
    public function setSecureCode($secureCode)
    {
        $this->secureCode = $secureCode;
    }
}
