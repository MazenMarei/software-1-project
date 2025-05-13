<?php

namespace App\models;

use App\core\Database;

class Artist extends User
{
    private $phone;
    private $bio;
    private $balance;
    private $address;
    private $bDate;
    /// should add payment method attribute
    private $paymentMethod;

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

    public function updateProfile($data)
    {
        try {
            $excut = parent::updateProfile($data);
            if (!$excut) {
                return false;
            }
            $artistSql = "UPDATE artist SET ";
            if (isset($data['address']) && !empty($data['address'])) {
                $artistSql .= "address = :address, ";
            }
            if (isset($data['bio']) && !empty($data['bio'])) {
                $artistSql .= "Bio = :bio, ";
            }
            if (isset($data['BDate']) && !empty($data['BDate'])) {
                $artistSql .= "BDate = :BDate, ";
            }
            if (isset($data['phone']) && !empty($data['phone'])) {
                $artistSql .= "phone = :phone, ";
            }

            if ($artistSql !== "UPDATE artist SET ") {
                $artistSql = rtrim($artistSql, ', ') . " WHERE artistID = :id";
                $stmt2 = Database::getInstance()->getConnection()->prepare($artistSql);
            } else {
                return true;
            }

            $stmt2 = Database::getInstance()->getConnection()->prepare($artistSql);
            if (isset($data['address']) && !empty($data['address'])) {
                $stmt2->bindParam(':address', $data['address'], \PDO::PARAM_STR);
            }
            if (isset($data['bio']) && !empty($data['bio'])) {
                $stmt2->bindParam(':bio', $data['bio'], \PDO::PARAM_STR);
            }
            if (isset($data['BDate']) && !empty($data['BDate'])) {
                $stmt2->bindParam(':BDate', $data['BDate'], \PDO::PARAM_STR);
            }
            if (isset($data['phone']) && !empty($data['phone'])) {
                $stmt2->bindParam(':phone', $data['phone'], \PDO::PARAM_STR);
            }

            $stmt2->bindParam(':id', $this->userID, \PDO::PARAM_INT);

            $execute2 = $stmt2->execute();
            if ($execute2) {
                if (isset($data['address']) && !empty($data['address'])) {
                    $this->address = $data['address'];
                }
                if (isset($data['bio']) && !empty($data['bio'])) {
                    $this->bio = $data['bio'];
                }
                if (isset($data['BDate']) && !empty($data['BDate'])) {
                    $this->bDate = $data['BDate'];
                }
                if (isset($data['phone']) && !empty($data['phone'])) {
                    $this->phone = $data['phone'];
                }
            }
            if ($execute2) {
                $_SESSION['success'] = "Profile has been updated successfully";
                return true;
            } else {
                $_SESSION['error'] = "Failed to update profil e " . $_SESSION['error'];
                return false;
            }
        } catch (\Throwable $th) {
            $_SESSION['error'] =  $th->getMessage();
            return false;
        }
    }

    public function withdrawRequest($amount, $type)
    {
        try {
            $amount = $type == "urgent" ? $amount - 5 : $amount;
            $transaction = new Transaction($amount, "withdraw", $type == "urgent" ? "Accepted" : "pending", $type == "urgent" ? date('Y-m-d') : date('Y-m-d', strtotime('+7 days')));
            $transactionId =  $transaction->createTransaction();
            if (!$transactionId) {
                return false;
            }
            $sql = "INSERT INTO artisttransaction (artistID, transactionID) VALUES (:artistID, :transactionID)";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':artistID', $this->userID, \PDO::PARAM_INT);
            $stmt->bindParam(':transactionID', $transactionId, \PDO::PARAM_INT);
            $success = $stmt->execute();
            if (!$success) {
                $_SESSION['error'] = "Failed to create transaction record.";
                return false;
            }
            // Deduct the amount from the artist's balance
            $sql = "UPDATE artist SET Balance = Balance - :amount WHERE artistID = :artistID";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':amount', $amount, \PDO::PARAM_STR);

            $stmt->bindParam(':artistID', $this->userID, \PDO::PARAM_INT);
            $success = $stmt->execute();
            if (!$success) {
                $_SESSION['error'] = "Failed to update artist balance.";
                return false;
            }
            return true;
        } catch (\Throwable $th) {
            $_SESSION['error'] =  $th->getMessage();
            return false;
        }
    }

    public function getAllTransactions()
    {
        try {
            $sql = "SELECT * FROM artisttransaction WHERE artistID = :artistID";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':artistID', $this->userID, \PDO::PARAM_INT);
            $stmt->execute();
            $artistTransactions = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if (!$artistTransactions || count($artistTransactions) == 0) {
                return [];
            }

            $Transactions = [];
            foreach ($artistTransactions as $artistTransaction) {
                $transaction = Transaction::getTransactionById($artistTransaction['transactionID']);
                if ($transaction) {
                    array_push($Transactions, $transaction);
                }
            }

            return $Transactions;
        } catch (\Throwable $th) {
            return [];
        }
    }


    public function getFollowers()
    {
        try {
            $db = Database::getInstance()->getConnection();

            $sql = "SELECT u.userID, u.Fname, u.username, u.Lname, u.profilePic, u.Email FROM following f JOIN user u ON f.customerID = u.userID WHERE f.artistID = :artistID";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':artistID', $this->userID, \PDO::PARAM_INT);
            $stmt->execute();
            $followers = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if (!$followers || count($followers) == 0) {
                return [];
            }
            $Customers = [];
            foreach ($followers as $key => $value) {
                $user = new Customer($value['Fname'], $value['Lname'], $value['Email'], $value["username"],  $value['profilePic']);
                array_push($Customers, $user);
            }
            return $Customers;
        } catch (\PDOException $e) {
            error_log("Error fetching artist followers: " . $e->getMessage());
            return [];
        }
    }

    public function getSoldArtworks()
    {
        try {
            $db = Database::getInstance()->getConnection();

            $sql = "SELECT 
                        a.*,
                        o.orderID,
                        o.orderDate,
                        o.totalPrice,
                        t.transactionID,
                        o.orderStatus,
                        c.customerID,
                        u.profilePic AS customerProfilePic,
                        u.username AS customerUsername,
                        u.Email AS customerEmail
                    FROM artwork a
                    JOIN order_artwork oa ON a.artworkID = oa.artworkID
                    JOIN `order` o ON oa.orderID = o.orderID
                    JOIN transaction t ON o.transactionID = t.transactionID
                    JOIN customer c ON o.customerID = c.customerID
                    JOIN user u ON c.customerID = u.userID
                    WHERE a.artistID = :artistId
                    AND t.status = 'completed'";

            $stmt = $db->prepare($sql);
            $stmt->bindValue(':artistId', $this->userID, \PDO::PARAM_INT);
            $stmt->execute();
            $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $data;
        } catch (\PDOException $e) {
            return false;
        }
    }
    public function getPaymentMethod()
    {
        try {
            $paymentMethod = Payments::getPaymentMethodByUserId($this->userID);
            if (!$paymentMethod) {
                return false;
            }
            $this->paymentMethod = $paymentMethod;
            return $this->paymentMethod;
        } catch (\Throwable $th) {
            $_SESSION['error'] = $th->getMessage();
            return false;
        }
    }



    private function addPaymentMethod($data)
    {
        try {

            $payment = new Payments([
                'userID' => $this->userID,
                'cardNumber' => $data['cardNumber'],
                'expMonth' => $data['expMonth'],
                'expYear' => $data['expYear'],
                'cvv' => $data['cvv']
            ]);
            return $payment->save();
        } catch (\Throwable $th) {
            $_SESSION['error'] =  $th->getMessage();
            return false;
        }
    }
    public function updatePayment($data)
    {
        try {
            /// check if there is a payment method
            $paymentMethod = $this->getPaymentMethod();
            if (!$paymentMethod) {
                return $this->addPaymentMethod($data);
            } else {
                $payment = new Payments($data);
                $_SESSION['success'] = "Payment method updated successfully 214124.";
                $_SESSION['error'] = $data['cardNumber'];
                return $payment->updatePaymentMethod($this->userID, $data);
            }
        } catch (\Throwable $th) {
            $_SESSION['error'] =  $th->getMessage();
            return false;
        }
    }


    public function getArtworks()
    {
        try {
            $sql = "SELECT * FROM artwork WHERE artistID = :artistID";


            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':artistID', $this->userID, \PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            return [];
        }
    }


    public function getCollections()
    {
        try {
            return Collection::getCollectionsByArtistId($this->userID);
        } catch (\Throwable $th) {
            return [];
        }
    }


    public static function getFeaturedArtists()
    {
        try {
            $sql = "SELECT * FROM artist JOIN user ON artist.artistID = user.userID ORDER BY Balance DESC LIMIT 3";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $featuredArtists = [];
            foreach ($data as $artist) {
                $artistObj = new Artist($artist['Fname'], $artist['Lname'], $artist['Email'], $artist['username'], $artist['profilePic']);
                $artistObj->getArtistById($artist['artistID']);
                array_push($featuredArtists, $artistObj);
            }
            return $featuredArtists;
        } catch (\Throwable $th) {
            $_SESSION['error'] = $th->getMessage();
            return [];
        }
    }

    public static function getCurrentArtist()
    {
        // First get the current user
        $user = User::getCurrentUser();

        // Check if user exists and is an artist
        if (!$user || $user->getRole() !== 'artist') {
            return false;
        }

        // Create a new Artist object and load data
        return (new self())->getArtistById($user->getUserID());
    }

    public function deleteArtwork($artworkId)
    {
        try {
            $artwork = Artwork::getArtworkById($artworkId);
            if (!$artwork) {
                return false;
            }
            if ($artwork['artistID'] !== $this->getUserID()) {
                $_SESSION['error'] = "You are not authorized to delete this artwork.";
                return false;
            }

            $success = Artwork::deleteArtwork($artworkId);
            if ($success) {
                $_SESSION['success'] = "Artwork deleted successfully.";
                return true;
            } else {
                $_SESSION['error'] = "Failed to delete artwork.";
                return false;
            }
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function deleteCollectionByID($collectionId)
    {
        try {
            $collection = Collection::getCollectionById($collectionId);
            if (!$collection) {
                return false;
            }
            if ($collection->getArtistID() !== $this->getUserID()) {
                $_SESSION['error'] = "You are not authorized to delete this collection.";
                return false;
            }
            $success = $collection->deleteCollection();
            if ($success) {
                $_SESSION['success'] = "Collection deleted successfully.";
                return true;
            } else {
                $_SESSION['error'] = "Failed to delete collection.";
                return false;
            }
        } catch (\Throwable $th) {
            return false;
        }
    }

    // Getters
    public function getArtistID()
    {
        return $this->userID;
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



    public function getReviews()
    {
        try {
            $sql = "SELECT   r.rating FROM  review r JOIN   artistreview ar ON r.reviewID = ar.reviewID WHERE ar.artistID = :artistID";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':artistID', $this->userID, \PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            return [];
        }
    }

    public function getArtFairs()
    {
        try {
            $localFairs = ArtFair::getArtFairsByArtistId($this->userID);
            if (!$localFairs || count($localFairs) == 0) {
                return [];
            }
            return $localFairs;
        } catch (\Throwable $th) {
            return [];
        }
    }

    public function deleteArtFair($id)
    {
        try {
            $artFair = ArtFair::getArtFairById($id);
            if (!$artFair) {
                $_SESSION['error'] = "Art fair not found.";
                return false;
            }
            return $artFair->deleteArtFair($id);
        } catch (\Throwable $th) {
            $_SESSION['error'] = "Failed to delete art fair: " . $th->getMessage();
            return false;
        }
    }

    public function createCollection($data)
    {
        try {
            $collection = new Collection([
                'name' => $data['name'],
                'description' => $data['description'],
                'coverImage' => $data['coverImage'],
                'artistID' => $this->userID,
            ]);
            $success = $collection->createCollection();
            if (!$success) {
                return false;
            }
            $artworks = explode(',', $data['artworks']) ?? [];
            foreach ($artworks as $artworkId) {
                $artworkId = trim($artworkId);
                if (empty($artworkId)) {
                    continue;
                }
                $collection->addArtworkToCollection($artworkId);
            }
            return true;
        } catch (\Throwable $th) {
            $_SESSION['error'] =  $th->getMessage();
            return false;
        }
    }
}
