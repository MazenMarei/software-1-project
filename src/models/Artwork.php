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
    }

    public function createArtwork()
    {
        try {
            $sql = "INSERT INTO artwork (artistID, title, description, price, images, createDate , medium , dimensions , category) VALUES (:artistID, :title, :description, :price, :images, :dateCreated, :medium, :dimensions, :category)";
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
            $suec =  $stmt->execute();
            if (!$suec) {
                return false;
            }
            $this->artworkID = Database::getInstance()->getConnection()->lastInsertId();
            return $this;
        } catch (\Throwable $th) {
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

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function updateArtwork($id, $data)
    {
        $sql = "UPDATE artwork SET title = :title, description = :description, price = :price, imagePath = :imagePath WHERE artworkID = :artworkID";
        $stmt = Database::getInstance()->getConnection()->prepare($sql);
        $stmt->bindParam(':artworkID', $id);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':imagePath', $data['imagePath']);

        return $stmt->execute();
    }


    public static function deleteArtwork($id)
    {
        $sql = "DELETE FROM artwork WHERE artworkID = :artworkID";
        $stmt = Database::getInstance()->getConnection()->prepare($sql);
        $stmt->bindParam(':artworkID', $id);

        return $stmt->execute();
    }
}
