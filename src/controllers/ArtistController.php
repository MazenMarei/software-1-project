<?php

namespace App\controllers;

use App\models\Admin;
use App\models\User;
use App\models\Artwork;
use App\core\Database;
use App\models\Artist;

class ArtistController
{

    public function dashboard($id = null)
    {
        $artist = Artist::getCurrentArtist();
        $artworks = $artist->getArtworks();
        $totalSales = count(array_filter($artworks, function ($artwork) {
            return $artwork['status'] === 'sold';
        }));
        $reviews = $artist->getReviews();
        $totalReviews = count($reviews);
        $reviewsAvg = array_reduce($reviews, function ($carry, $review) {
            return $carry + $review['rating'];
        }, 0) / max($totalReviews, 1);

        $totalEarnings = array_reduce($artworks, function ($carry, $artwork) {
            return $carry + ($artwork['status'] === 'sold' ? $artwork['price'] : 0);
        }, 0);


        require_once VIEWS . 'pages/Artist/index.php';
    }

    public function artworks()
    {
        $artist = Artist::getCurrentArtist();
        $artworks = $artist->getArtworks();
        $totalSales = count(array_filter($artworks, function ($artwork) {
            return $artwork['status'] === 'sold';
        }));
        $categories = Artwork::getCategories();

        require_once VIEWS . 'pages\Artist\artworks.php';
    }

    public function profile()
    {
        $artist = Artist::getCurrentArtist();


        require_once VIEWS . 'pages\Artist\profile.php';
    }


    public function profileUpdate()
    {
        if (!isset($_POST['firstName']) || !isset($_POST['lastName']) || !isset($_POST['email'])) {
            $_SESSION['error'] = 'Invalid request';
            header('Location: /artist/profile');
            exit;
        }

        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $BDate = $_POST['dob'];
        $bio = $_POST['bio'];
        $emailNotification = isset($_POST['emailNotifications']) ? 1 : 0;
        $artist = Artist::getCurrentArtist();
        if (!$artist) {
            $_SESSION['error'] = 'You must be logged in as an artist to perform this action';
            header('Location: /index');
            exit;
        }

        $success = $artist->updateProfile([
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'BDate' => $BDate,
            'bio' => $bio,
            'emailNotification' => $emailNotification,
        ]);

        if ($success) {
            $_SESSION['success'] = "Profile has been updated successfully";
        } else {
            $_SESSION['error'] = "Failed to update profile " . $_SESSION['error'];
        }

        header('Location: /artist/profile');
        exit;
    }

    public function profilePicUpdate()
    {
        if (!isset($_FILES['profilePic']) || $_FILES['profilePic']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Invalid request';
            header('Location: /artist/profile');
            exit;
        }

        $artist = Artist::getCurrentArtist();
        if (!$artist) {
            $_SESSION['error'] = 'You must be logged in as an artist to perform this action';
            header('Location: /index');
            exit;
        }

        $profilePic = $_FILES['profilePic'];
        $fileName = $profilePic['name'];
        $fileType = $profilePic['type'];

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg', 'image/webp'];
        $fileType = strtolower($fileType);
        if (!in_array($fileType, $allowedTypes)) {
            $_SESSION['error'] = 'Only JPG, JPEG, PNG, Webp and GIF files are allowed';
            header('Location: /artist/profile');
            exit;
        }

        // Generate unique filename
        $newFileName = uniqid('artist_') . '.' . pathinfo($fileName, PATHINFO_EXTENSION);
        $uploadPath = UPLOADS . 'profiles/' . $newFileName;

        // Check if directory exists and is writable
        $directory = UPLOADS . 'profiles/';
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        if (!is_writable($directory)) {
            $_SESSION['error'] = "Upload directory is not writable";
            header('Location: /artist/profile');
            exit;
        }

        // Move uploaded file
        $fileTmpName = $profilePic['tmp_name'];
        if (move_uploaded_file($fileTmpName, $uploadPath)) {
            // Delete old profile pic if not default
            $oldPic = $artist->getProfilePic();
            if ($oldPic !== 'default.jpg') {
                $oldPath = UPLOADS . 'profiles/' . $oldPic;
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $success = $artist->updateProfile([
                'profilePic' => $newFileName
            ]);

            if ($success) {
                $_SESSION['success'] = "Profile picture has been updated successfully";
            } else {
                // If database update fails, delete the uploaded file
                if (file_exists($uploadPath)) {
                    unlink($uploadPath);
                }
                $_SESSION['error'] = "Failed to update profile picture in database";
            }

            header('Location: /artist/profile');
            exit;
        } else {
            $_SESSION['error'] = "Failed to upload profile picture. Check file permissions.";
            header('Location: /artist/profile');
            exit;
        }
    }



    public function deleteArtwork()
    {
        if (!isset($_POST['id'])) {
            $_SESSION['error'] = 'Invalid request';
            header('Location: /artist/artworks');
            exit;
        }

        $artworkId = $_POST['id'];
        $artist = Artist::getCurrentArtist();
        if (!$artist) {
            $_SESSION['error'] = 'You must be logged in as an artist to perform this action';
            header('Location: /index');
            exit;
        }

        $success = $artist->deleteArtwork($artworkId);

        if ($success) {
            $_SESSION['success'] = "Artwork has been deleted successfully";
        } else {
            $_SESSION['error'] = "Failed to delete artwork";
        }

        header('Location: /artist/artworks');
        exit;
    }

    public function changePassword()
    {
        if (!isset($_POST['currentPassword']) || !isset($_POST['newPassword'])) {
            $_SESSION['error'] = 'Invalid request';
            header('Location: /artist/profile');
            exit;
        }

        $oldPassword = $_POST['currentPassword'];
        $newPassword = $_POST['newPassword'];

        $artist = Artist::getCurrentArtist();
        if (!$artist) {
            $_SESSION['error'] = 'You must be logged in as an artist to perform this action';
            header('Location: /index');
            exit;
        }

        if ($newPassword == $oldPassword) {
            $_SESSION['error'] = 'New password must be different from old password';
            header('Location: /artist/profile');
            exit;
        }

        if (strlen($newPassword) < 8) {
            $_SESSION['error'] = 'New password must be at least 6 characters long';
            header('Location: /artist/profile');
            exit;
        }

        // Check if the old password is correct
        if (!$artist->checkPassword($oldPassword)) {
            $_SESSION['error'] = 'Old password is incorrect';
            header('Location: /artist/profile');
            exit;
        }



        // Update the password
        $success = $artist->changePassword($oldPassword, $newPassword);

        if ($success) {
            $_SESSION['success'] = "Password has been changed successfully";
        } else {
            $_SESSION['error'] = "Failed to change password : " . $_SESSION['error'];
        }

        header('Location: /artist/profile');
        exit;
    }

    public function addArtwork()
    {
        if (!isset($_POST['title']) || !isset($_POST['description']) || !isset($_POST['price']) || !isset($_POST['category']) || !isset($_FILES['image'])) {
            $_SESSION['error'] = 'Invalid request';
            header('Location: /artist/artworks');
            exit;
        }
        $title = $_POST['title'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $category = $_POST['category'];
        $medium = $_POST['medium'];
        $height = $_POST['height'];
        $width = $_POST['width'];
        $depth = $_POST['depth'] ?? "";
        $image = $_FILES['image']['name'];

        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Invalid image upload';
            header('Location: /artist/artworks');
            exit;
        }

        if (empty($title) || empty($description) || empty($price) || empty($category) || empty($image) || empty($medium) || empty($height) || empty($width) || empty($depth)) {
            $_SESSION['error'] = 'All fields are required';
            header('Location: /artist/artworks');
            exit;
        }

        if (!is_numeric($price) || $price <= 0) {
            $_SESSION['error'] = 'Price must be a positive number';
            header('Location: /artist/artworks');
            exit;
        }

        if (!is_numeric($height) || $height <= 0 || !is_numeric($width) || $width <= 0 || !is_numeric($depth) || $depth <= 0) {
            $_SESSION['error'] = 'Dimensions must be positive numbers';
            header('Location: /artist/artworks');
            exit;
        }

        /// Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg', 'image/webp'];
        $fileType = strtolower($_FILES['image']['type']);
        if (!in_array($fileType, $allowedTypes)) {
            $_SESSION['error'] = 'Only JPG, JPEG, PNG, Webp and GIF files are allowed';
            header('Location: /artist/artworks');
            exit;
        }
        // Generate unique filename
        $newFileName = uniqid('artwork_') . '.' . pathinfo($image, PATHINFO_EXTENSION);
        $uploadPath = UPLOADS . 'artworks/' . $newFileName;

        // Check if directory exists and is writable
        $directory = UPLOADS . 'artworks/';
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        if (!is_writable($directory)) {
            $_SESSION['error'] = "Upload directory is not writable";
            header('Location: /artist/artworks');
            exit;
        }
        $artwork = new Artwork([
            'artistID'    => Artist::getCurrentArtist()->getUserID(),
            'title'       => $title,
            'description' => $description,
            'price'       => $price,
            'images'      => $image,
            'medium'      => $medium,
            'dimensions'  => $height . 'x' . $width . 'x' . $depth . ' in',
            'category'    => $category,
        ]);


        $fileTmpName = $_FILES['image']['tmp_name'];
        if (!move_uploaded_file($fileTmpName, $uploadPath)) {
            $_SESSION['error'] = "Failed to upload artwork image. Check file permissions.";
            header('Location: /artist/artworks');
            exit;
        }
        $success = $artwork->createArtwork();
        if ($success) {
            $_SESSION['success'] = "Artwork has been added successfully";
        } else {
            $_SESSION['error'] = "Failed to add artwork: " . $_SESSION['error'];
        }
        header('Location: /artist/artworks');
        exit;
    }

    public function newArtwork()
    {
        $artist = Artist::getCurrentArtist();
        if (!$artist) {
            $_SESSION['error'] = 'You must be logged in as an artist to perform this action';
            header('Location: /index');
            exit;
        }
        $categories = Artwork::getCategories();

        require_once VIEWS . 'pages\Artist\new-artwork.php';
    }
}
