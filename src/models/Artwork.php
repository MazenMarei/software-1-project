<?php

namespace App\models;

use App\core\Database;

class Artwork
{
    private $artworkID;
    private $artistID;
    private $title;
    private $description;
    private $price;
    private $images;
    private $dateCreated;
    private $medium;
    private $dimensions;
    private $category;
    private $status = 'Pending';
    private static $categories = [
        "Painting",
        "Photography",
        "Sculpture",
        "Drawing",
        "Digital Art",
        "Mixed Media",
        "Collage",
        "Prints",
        "Abstract",
        "Photorealism",
        "Watercolor"
    ];

    public static function getCategories()
    {
        return self::$categories;
    }

    public function __construct($data)
    {

        $this->artistID = $data['artistID'] ?? null;
        $this->title = $data['title'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->price = $data['price'] ?? null;
        $this->images = $data['images'] ?? null;
        $this->medium = $data['medium'] ?? null;
        $this->dimensions = $data['dimensions'] ?? null;
        $this->category = $data['category'] ?? null;
        $this->images = $data['images'] ?? null;
        $this->dateCreated = date('Y-m-d');
        $this->status = $data['status'] ?? 'Pending';
    }

    public function createArtwork()
    {
        try {
            $sql = "INSERT INTO artwork (artistID, title, description, price, images, createDate , medium , dimensions , category , status) VALUES (:artistID, :title, :description, :price, :images, :dateCreated, :medium, :dimensions, :category, :status)";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':artistID', $this->artistID);
            $stmt->bindParam(':title', $this->title);
            $stmt->bindParam(':description', $this->description);
            $stmt->bindParam(':price', $this->price);
            $stmt->bindParam(':images', $this->images);
            $stmt->bindParam(':dateCreated', $this->dateCreated);
            $stmt->bindParam(':medium', $this->medium);
            $stmt->bindParam(':dimensions', $this->dimensions);
            $stmt->bindParam(':category', $this->category);
            $stmt->bindParam(':status', $this->status);
            $suec =  $stmt->execute();
            if (!$suec) {
                return false;
            }
            $this->artworkID = Database::getInstance()->getConnection()->lastInsertId();
            return $this;
        } catch (\Throwable $th) {
            $_SESSION['error'] = $th->getMessage();
            return false;
        }
    }


    public static function getArtworkById($id)
    {
        try {
            $sql = "SELECT * FROM artwork WHERE artworkID = :artworkID";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':artworkID', $id);
            $stmt->execute();
            $artwork = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($artwork) {
                $artworkObj = new Artwork($artwork);
                $artworkObj->artworkID = $artwork['artworkID'];
                return $artworkObj;
            }
            return false;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function updateArtwork($data)
    {
        $this->title = $data['title'] ?? $this->title;
        $this->description = $data['description'] ?? $this->description;
        $this->price = $data['price'] ?? $this->price;
        $this->images = $data['images'] ?? $this->images;
        $this->medium = $data['medium'] ?? $this->medium;
        $this->dimensions = $data['dimensions'] ?? $this->dimensions;
        $this->category = $data['category'] ?? $this->category;
        $artworkID = $this->artworkID ?? $data['artworkID'];

        try {

            $sql = "UPDATE artwork SET title = :title, description = :description, price = :price, images = :images, medium = :medium, dimensions = :dimensions, category = :category WHERE artworkID = :artworkID";

            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':title', $this->title);
            $stmt->bindParam(':description', $this->description);
            $stmt->bindParam(':price', $this->price);
            $stmt->bindParam(':images', $this->images);
            $stmt->bindParam(':medium', $this->medium);
            $stmt->bindParam(':dimensions', $this->dimensions);
            $stmt->bindParam(':category', $this->category);
            $stmt->bindParam(':artworkID', $artworkID);
            $succ =  $stmt->execute();
            if (!$succ) {
                return false;
            }
            return $this;
        } catch (\Throwable $th) {
            $_SESSION['error'] = $th->getMessage();
            return false;
        }
    }


    public static function deleteArtwork($id)
    {
        $sql = "DELETE FROM artwork WHERE artworkID = :artworkID";
        $stmt = Database::getInstance()->getConnection()->prepare($sql);
        $stmt->bindParam(':artworkID', $id);

        return $stmt->execute();
    }

    public function getArtistID()
    {
        return $this->artistID;
    }
    public function getArtworkID()
    {
        return $this->artworkID;
    }
    public function getTitle()
    {
        return $this->title;
    }
    public function getDescription()
    {
        return $this->description;
    }
    public function getPrice()
    {
        return $this->price;
    }
    public function getImages()
    {
        return $this->images;
    }
    public function getDateCreated()
    {
        return $this->dateCreated;
    }
    public function getMedium()
    {
        return $this->medium;
    }
    public function getDimensions()
    {
        $dimensions = explode('x', $this->dimensions);
        if (count($dimensions) == 2) {
            return [
                'width' => trim($dimensions[0]),
                'height' => trim($dimensions[1], ' in')
            ];
        }
        if (count($dimensions) == 3) {
            return [
                'width' => trim($dimensions[0]),
                'height' => trim($dimensions[1]),
                'depth' => trim($dimensions[2],  ' in')
            ];
        }
    }
    public function getCategory()
    {
        return $this->category;
    }
    public function getStatus()
    {
        return $this->status;
    }
}
