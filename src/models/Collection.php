<?php

namespace App\models;

use App\core\Database;
use App\models\Artwork;

class Collection
{

    private $collectionID;
    private $name;
    private $description;
    private $createDate;
    private $artistID;
    private $artworks;
    private $coverImage;



    public function __construct($date)
    {
        $this->name = $date['name'];
        $this->description = $date['description'];
        $this->artistID = $date['artistID'];
        $this->coverImage = $date['coverImage'];
        $this->createDate = date('Y-m-d');
        $this->collectionID = $date['collectionID'] ?? null;
    }

    public function createCollection()
    {
        try {
            $sql = "INSERT INTO artcollection (name, description, artistID, coverImage) VALUES (:name, :description, :artistID, :coverImage )";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':name', $this->name);
            $stmt->bindParam(':coverImage', $this->coverImage);
            $stmt->bindParam(':description', $this->description);
            $stmt->bindParam(':artistID', $this->artistID);
            $stmt->execute();
            $this->collectionID = Database::getInstance()->getConnection()->lastInsertId();
            return true;
        } catch (\PDOException $e) {
            $_SESSION['error'] =  $e->getMessage();
            return false;
        }
    }

    public static function getCollectionById($id)
    {
        // Get the collection data
        $sql = "SELECT * FROM artcollection WHERE collectionID = :id";
        $stmt = Database::getInstance()->getConnection()->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        $collection = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($collection) {
            return new self($collection);
        } else {
            return false;
        }
    }
    public function getCollectionArtworks()
    {
        return $this->artworks;
    }
    public function addArtworkToCollection($artworkID)
    {
        try {
            $sql = "INSERT INTO collection_artwork (collectionID, artworkID) VALUES (:collectionID, :artworkID)";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':collectionID', $this->collectionID, \PDO::PARAM_INT);
            $stmt->bindParam(':artworkID', $artworkID, \PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (\PDOException $e) {
            $_SESSION['error'] =  $e->getMessage();
            return false;
        }
    }
    public function fetchCollectionsArtworks()
    {
        if (isset($this->collectionID)) {
            $sql = "SELECT * FROM collection_artwork WHERE collectionID = :collectionID";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':collectionID', $this->collectionID, \PDO::PARAM_INT);
            $stmt->execute();
            $artworks = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $collectionArtworks = [];
            foreach ($artworks as $artwork) {
                $artworkID = $artwork['artworkID'];
                $artwork = Artwork::getArtworkById($artworkID);
                if ($artwork) {
                    array_push($collectionArtworks, $artwork);
                }
            }
            $this->artworks = $collectionArtworks;
            return $this->artworks;
        } else {
            return false;
        }
    }


    public function deleteCollection()
    {
        try {
            $sql = "DELETE FROM artcollection WHERE collectionID = :collectionID";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':collectionID', $this->collectionID, \PDO::PARAM_INT);
            $stmt->execute();
            $sql = "DELETE FROM collection_artwork WHERE collectionID = :collectionID";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':collectionID', $this->collectionID, \PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    public static function getCollectionsByArtistId($artistID)
    {
        $sql = "SELECT * FROM artcollection WHERE artistID = :artistID";
        $stmt = Database::getInstance()->getConnection()->prepare($sql);
        $stmt->bindParam(':artistID', $artistID, \PDO::PARAM_INT);
        $stmt->execute();
        $collectionsData = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if (!$collectionsData) {
            return [];
        }

        $collections = [];
        foreach ($collectionsData as $collectionData) {
            $collection = new self($collectionData);

            $collection->fetchCollectionsArtworks();
            array_push($collections, $collection);
        }
        return $collections;
    }
    public function getCoverImage()
    {
        return $this->coverImage;
    }
    public function getName()
    {
        return $this->name;
    }
    public function getDescription()
    {
        return $this->description;
    }
    public function getCreateDate()
    {
        return $this->createDate;
    }
    public function getArtistID()
    {
        return $this->artistID;
    }
    public function getCollectionID()
    {
        return $this->collectionID;
    }
}
