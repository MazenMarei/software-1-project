<?php

namespace App\models;

use App\core\Database;


class SpecialCollection
{
    private $collectionID;
    private $name;
    private $artworks;
    private $coverImage;
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
        if ($this->checkExist() == false) {
            $this->createCollection();
        }
        $SpecialCollection = $this->getSpecialCollection();
        $this->collectionID = $SpecialCollection['collectionID'];
        $this->name = $SpecialCollection['name'];
        $this->artworks = $this->fetchArtworks();
    }


    private function deleteArtworks($artworkID)
    {
        try {
            $sql = "DELETE FROM collection_artwork WHERE collectionID = :collectionID";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':collectionID', $this->collectionID);
            return $stmt->execute();
        } catch (\PDOException $e) {
            $_SESSION['error'] =  $e->getMessage();
            return false;
        }
    }

    public function updateCollection($data)
    {
        try {
            $sql = "UPDATE artcollection";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            if (isset($data['name'])) {
                $sql .= " SET name = :name";
            }


            if (isset($data['coverImage'])) {
                $sql .= ", coverImage = :coverImage";
            }
            if (isset($data['adminID'])) {
                $sql .= ", adminID = :adminID";
            }

            $sql .= " WHERE collectionID = :collectionID";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            if (isset($data['name'])) {
                $stmt->bindParam(':name', $data['name']);
            }
            if (isset($data['coverImage'])) {
                $stmt->bindParam(':coverImage', $data['coverImage']);
            }
            if (isset($data['adminID'])) {
                $stmt->bindParam(':adminID', $data['adminID']);
            }
            $stmt->bindParam(':collectionID', $this->collectionID, \PDO::PARAM_INT);
            return $stmt->execute();
        } catch (\PDOException $e) {
            $_SESSION['error'] =  $e->getMessage();
            return false;
        }
    }

    public function setArtworks($artworkID)
    {
        try {
            $delete = $this->deleteArtworks($artworkID);
            if ($delete == false) {
                return false;
            }

            $added = 0;
            foreach ($artworkID as $artwork) {
                if (empty($artwork) || $artwork == null || $artwork == "") {
                    continue;
                }
                $artwork = Artwork::getArtworkById($artwork);
                if (!$artwork) {
                    continue;
                }
                $artworkID = $artwork->getArtworkID();
                if (!$artworkID) {
                    continue;
                }
                $sql = "INSERT INTO collection_artwork (artworkID, collectionID) VALUES (:artworkID, :collectionID)";
                $stmt = Database::getInstance()->getConnection()->prepare($sql);
                $stmt->bindParam(':artworkID', $artworkID);
                $stmt->bindParam(':collectionID', $this->collectionID);
                if ($stmt->execute()) {
                    $added++;
                } else {
                    $_SESSION['error'] = "Error adding artwork to collection.";
                    --$added;
                }
            }

            return $added > -1;
        } catch (\PDOException $e) {
            $_SESSION['error'] =  $e->getMessage();
            return false;
        }
    }

    public function fetchArtworks()
    {
        $sql = "SELECT * FROM collection_artwork WHERE collectionID = :collectionID";
        $stmt = Database::getInstance()->getConnection()->prepare($sql);
        $stmt->bindParam(':collectionID', $this->collectionID, \PDO::PARAM_INT);
        $stmt->execute();
        $artWorksIDs =  $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $artworks = [];
        foreach ($artWorksIDs as $artworkID) {
            $artwork = Artwork::getArtworkById($artworkID['artworkID']);
            if ($artwork && $artwork->getStatus() !== "Accepted") {
                $this->deleteArtworks($artworkID['artworkID']);
                continue;
            }
            if ($artwork) {
                $artworks[] = $artwork;
            }
        }
        return $artworks;
    }

    private function getSpecialCollection()
    {
        $sql = "SELECT * FROM artcollection WHERE special = 1 AND artistID IS NULL";
        $stmt = Database::getInstance()->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    private function checkExist()
    {
        $sql = "SELECT * FROM artcollection WHERE special = 1 AND artistID IS NULL";
        $stmt = Database::getInstance()->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    private function createCollection()
    {
        try {
            $sql = "INSERT INTO artcollection (name, special , coverImage) VALUES ('Spectail Realas', 1, 'pexels-photo.jpeg')";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->execute();
            $this->collectionID = Database::getInstance()->getConnection()->lastInsertId();
            return true;
        } catch (\PDOException $e) {
            $_SESSION['error'] =  $e->getMessage();
            return false;
        }
    }

    public function getCollectionID()
    {
        return $this->collectionID;
    }
    public function getName()
    {
        return $this->name;
    }
    public function getArtworks()
    {
        return $this->artworks;
    }
    public function getCoverImage()
    {
        return $this->coverImage;
    }
}
