<?php

namespace App\models;

use App\core\Database;

class ArtFair
{
    private $id;
    private $name;
    private $location;
    private $startDate;
    private $description;
    private $image;
    private $status = 'Pending';
    private $artistID;
    private static $goverments = [
        "Cairo",
        "Giza",
        "Alexandria",
        "Dakahlia",
        "Red Sea",
        "Beheira",
        "Fayoum",
        "Gharbiya",
        "Ismailia",
        "Menofia",
        "Minya",
        "Qaliubiya",
        "New Valley",
        "Suez",
        "Aswan",
        "Assiut",
        "Beni Suef",
        "Port Said",
        "Damietta",
        "Sharkia",
        "South Sinai",
        "Kafr Al sheikh",
        "Matrouh",
        "Luxor",
        "Qena",
        "North Sinai",
        "Sohag"
    ];

    public static function getGoverments()
    {
        return self::$goverments;
    }

    public function __construct($data)
    {
        $this->name = $data['name'] ?? null;
        $this->location = $data['location'] ?? null;
        $this->startDate = $data['startDate'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->image = $data['image'] ?? null;
        $this->id = $data['eventID'] ?? null;
        $this->artistID = $data['artistID'] ?? null;
        $this->status = $data['status'] ?? 'pending';
    }

    public function createArtFair()
    {
        try {
            $sql = "INSERT INTO localfair (name, location, date, description, images, artistID) VALUES (:name, :location, :startDate, :description, :image, :artistID)";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':name', $this->name);
            $stmt->bindParam(':location', $this->location);
            $stmt->bindParam(':startDate', $this->startDate);
            $stmt->bindParam(':description', $this->description);
            $stmt->bindParam(':image', $this->image);
            $stmt->bindParam(':artistID', $this->artistID);

            $this->id = Database::getInstance()->getConnection()->lastInsertId();
            return $stmt->execute();
        } catch (\PDOException $e) {
            $_SESSION['error'] = "Error creating art fair: " . $e->getMessage();
            return false;
        }
    }

    public static function getArtFairsByArtistId($artistID)
    {
        try {
            $sql = "SELECT * FROM localfair WHERE artistID = :artistID";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':artistID', $artistID);
            $stmt->execute();
            $localFairs  = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $localFair = new ArtFair([
                    'eventID' => $row['eventID'],
                    'name' => $row['name'],
                    'location' => $row['location'],
                    'startDate' => $row['date'],
                    'description' => $row['description'],
                    'image' => $row['images'],
                    'status' => $row['status']
                ]);
                array_push($localFairs, $localFair);
            }
            return $localFairs;
        } catch (\PDOException $e) {
            $_SESSION['error'] = "Error fetching art fairs: " . $e->getMessage();
            return false;
        }
    }


    public function deleteArtFair($id)
    {
        try {
            $sql = "DELETE FROM localfair WHERE eventID = :id";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (\PDOException $e) {
            $_SESSION['error'] = "Error deleting art fair: " . $e->getMessage();
            return false;
        }
    }

    public static function getArtFairById($id)
    {
        try {
            $sql = "SELECT * FROM localfair WHERE eventID = :id";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $data = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!$data) {
                return false;
            }
            return new self([
                'eventID' => $data['eventID'],
                'name' => $data['name'],
                'location' => $data['location'],
                'startDate' => $data['date'],
                'description' => $data['description'],
                'image' => $data['images'],
                'status' => $data['status'],
                'artistID' => $data['artistID']
            ]);
        } catch (\PDOException $e) {
            $_SESSION['error'] = "Error fetching art fair: " . $e->getMessage();
            return false;
        }
    }


    public static function getAllArtFairs()
    {
        try {
            $sql = "SELECT * FROM localfair";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->execute();
            $localFairs  = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $localFair = new ArtFair([
                    'eventID' => $row['eventID'],
                    'name' => $row['name'],
                    'location' => $row['location'],
                    'startDate' => $row['date'],
                    'description' => $row['description'],
                    'image' => $row['images'],
                    'status' => $row['status']
                ]);
                array_push($localFairs, $localFair);
            }
            return $localFairs;
        } catch (\PDOException $e) {
            $_SESSION['error'] = "Error fetching art fairs: " . $e->getMessage();
            return false;
        }
    }

    public static function updateArtFairStatusById($id, $status)
    {
        try {
            $sql = "UPDATE localfair SET status = :status WHERE eventID = :id";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            return self::getArtFairById($id);
        } catch (\PDOException $e) {
            $_SESSION['error'] = "Error updating art fair status: " . $e->getMessage();
            return false;
        }
    }

    public function getArtistID()
    {
        return $this->artistID;
    }

    public function getStatus()
    {
        return $this->status;
    }
    public function getId()
    {
        return $this->id;
    }
    public function getName()
    {
        return $this->name;
    }
    public function getLocation()
    {
        return $this->location;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function getDescription()
    {
        return $this->description;
    }
    public function getImage()
    {
        return $this->image;
    }
}
