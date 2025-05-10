<?php

namespace App\controllers;

use App\models\Admin;
use App\models\User;
use App\models\Artwork;
use App\core\Database;
use App\models\Artist;
use App\models\ArtFair;
use App\core\App;

class ArtistController
{

    public function dashboard()
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
        $salesData = $this->getSalesChartData();

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
        $payment = $artist->getPaymentMethod();

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
            $_SESSION['error'] = 'Invalid image upload ' . $_FILES['image']['error'];
            header('Location: /artist/artworks');
            exit;
        }

        if (empty($title) || empty($description) || empty($price) || empty($category) || empty($image) || empty($medium) || empty($height) || empty($width)) {
            $_SESSION['error'] = 'All fields are required';
            header('Location: /artist/artworks');
            exit;
        }

        if (!is_numeric($price) || $price <= 0) {
            $_SESSION['error'] = 'Price must be a positive number';
            header('Location: /artist/artworks');
            exit;
        }

        if (!is_numeric($height) || $height <= 0 || !is_numeric($width) || $width <= 0) {
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
            'images'      => $newFileName,
            'medium'      => $medium,
            'dimensions'  => $height . 'x' . $width . (!empty($depth) ? 'x' . $depth : '') . ' in',
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

    public function editArtwork()
    {
        $id = $_GET['id'] ?? null;
        if (!isset($id)) {
            $_SESSION['error'] = 'Invalid request';
            header('Location: /artist/artworks');
            exit;
        }

        $artwork = Artwork::getArtworkById($id);
        if (!$artwork) {
            $_SESSION['error'] = 'Artwork not found';
            header('Location: /artist/artworks');
            exit;
        }

        $artist = Artist::getCurrentArtist();
        if (!$artist) {
            $_SESSION['error'] = 'You must be logged in as an artist to perform this action';
            header('Location: /index');
            exit;
        }
        if ($artist->getUserID() != $artwork->getArtistID()) {
            $_SESSION['error'] = 'You do not have permission to edit this artwork';
            header('Location: /artist/artworks');
            exit;
        }
        $categories = Artwork::getCategories();

        require_once VIEWS . 'pages\Artist\edit-artwork.php';
    }

    public function updatePayment()
    {
        if (!isset($_POST['expYear']) || !isset($_POST['expMonth']) || !isset($_POST['cardNumber']) || !isset($_POST['cvvNumber'])) {
            $_SESSION['error'] = 'Invalid request';
            header('Location: /artist/profile');
            exit;
        }
        $expYear = $_POST['expYear'];
        $expMonth = $_POST['expMonth'];
        $cardNumber = str_replace("-", "", $_POST['cardNumber']);
        $cvvNumber = $_POST['cvvNumber'];

        if (empty($expYear) || empty($expMonth) || empty($cardNumber) || empty($cvvNumber)) {
            $_SESSION['error'] = 'All fields are required';
            header('Location: /artist/profile');
            exit;
        }
        if (!is_numeric($cardNumber) || strlen($cardNumber) != 16) {
            $_SESSION['error'] = 'Card number must be a 16-digit number';
            header('Location: /artist/profile');
            exit;
        }

        if (!is_numeric($cvvNumber) || strlen($cvvNumber) != 3) {
            $_SESSION['error'] = 'CVV number must be a 3-digit number';
            header('Location: /artist/profile');
            exit;
        }
        if (!is_numeric($expYear) || strlen($expYear) != 4) {
            $_SESSION['error'] = 'Expiration year must be a 4-digit number';
            header('Location: /artist/profile');
            exit;
        }
        if (!is_numeric($expMonth) || strlen($expMonth) > 2) {
            $_SESSION['error'] = 'Expiration month must be a 2-digit number';
            header('Location: /artist/profile');
            exit;
        }
        if ($expMonth < 1 || $expMonth > 12) {
            $_SESSION['error'] = 'Expiration month must be between 01 and 12';
            header('Location: /artist/profile');
            exit;
        }
        if ($expYear < date('Y')) {
            $_SESSION['error'] = 'Expiration year must be greater than or equal to the current year';
            header('Location: /artist/profile');
            exit;
        }
        if ($expYear == date('Y') && $expMonth < date('m')) {
            $_SESSION['error'] = 'Expiration month must be greater than or equal to the current month';
            header('Location: /artist/profile');
            exit;
        }
        $artist = Artist::getCurrentArtist();

        if (!$artist) {
            $_SESSION['error'] = 'You must be logged in as an artist to perform this action';
            header('Location: /index');
            exit;
        }

        $success = $artist->updatePayement([
            'expYear' => $expYear,
            'expMonth' => $expMonth,
            'cardNumber' => $cardNumber,
            'cvv' => $cvvNumber,
        ]);

        if ($success) {
            $_SESSION['success'] = "Payment information has been updated successfully";
        } else {
            $_SESSION['error'] = "Failed to update payment information: " . $_SESSION['error'];
        }
        header('Location: /artist/profile');
    }

    public function updateArtwork()
    {
        if (!isset($_POST['id']) || !isset($_POST['title']) || !isset($_POST['description']) || !isset($_POST['price']) || !isset($_POST['category'])) {
            $_SESSION['error'] = 'Invalid request';
            header('Location: /artist/artworks');
            exit;
        }

        $id = $_POST['id'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $category = $_POST['category'];
        $medium = $_POST['medium'];
        $height = $_POST['height'];
        $width = $_POST['width'];
        $depth = $_POST['depth'] ?? "";
        $image = $_FILES['image']['name'] ?? null;
        $newFileName = null;

        if (empty($id) || empty($title) || empty($description) || empty($price) || empty($category) || empty($medium) || empty($height) || empty($width)) {
            $_SESSION['error'] = 'All fields are required';
            header('Location: /artist/artworks');
            exit;
        }
        $oldArtwork = Artwork::getArtworkById($id);
        if (!$oldArtwork) {
            $_SESSION['error'] = 'Artwork not found';
            header('Location: /artist/artworks');
            exit;
        }
        $artist = Artist::getCurrentArtist();
        if (!$artist) {
            $_SESSION['error'] = 'You must be logged in as an artist to perform this action';
            header('Location: /index');
            exit;
        }

        if ($artist->getUserID() != $oldArtwork->getArtistID()) {
            $_SESSION['error'] = 'You do not have permission to edit this artwork';
            header('Location: /artist/artworks');
            exit;
        }

        if (!is_numeric($price) || $price <= 0) {
            $_SESSION['error'] = 'Price must be a positive number';
            header('Location: /artist/artworks');
            exit;
        }

        if (!is_numeric($height) || $height <= 0 || !is_numeric($width) || $width <= 0) {
            $_SESSION['error'] = 'Dimensions must be positive numbers';
            header('Location: /artist/artworks');
            exit;
        }

        if ($image && $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Invalid image upload ' . $_FILES['image']['error'];
            header('Location: /artist/artworks');
            exit;
        } else {
            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg', 'image/webp'];
            $fileType = strtolower($_FILES['image']['type']);
            if ($image && !in_array($fileType, $allowedTypes)) {
                $_SESSION['error'] = 'Only JPG, JPEG, PNG, Webp and GIF files are allowed';
                header('Location: /artist/artworks');
                exit;
            }
            // Generate unique filename
            $newFileName = uniqid('artwork_') . '.' . pathinfo($image, PATHINFO_EXTENSION);
            $uploadPath = UPLOADS . 'artworks' . DS . $newFileName;

            // Check if directory exists and is writable
            $directory = UPLOADS . 'artworks/';
            if ($image && !is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            if ($image && !is_writable($directory)) {
                $_SESSION['error'] = "Upload directory is not writable";
                header('Location: /artist/artworks');
                exit;
            }

            $fileTmpName = $_FILES['image']['tmp_name'];
            if ($image && !move_uploaded_file($fileTmpName, $uploadPath)) {
                $_SESSION['error'] = "Failed to upload artwork image. Check file permissions.";
                header('Location: /artist/artworks');
                exit;
            }
        }
        $oldPath = UPLOADS . 'artworks' . DS . $oldArtwork->getImages();
        $success = $oldArtwork->updateArtwork([
            'title'       => $title,
            'description' => $description,
            'price'       => $price,
            'images'      => $image ? $newFileName : $oldArtwork->getImages(),
            'medium'      => $medium,
            'dimensions'  => $height . 'x' . $width . (!empty($depth) ? 'x' . $depth : '') . ' in',
            'category'    => $category,
            'ArtworkID'   => $id,
        ]);
        if ($success) {
            // Delete old image if a new one was uploaded
            if ($image) {
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
        } else {
            $_SESSION['error'] = "Failed to update artwork: " . $_SESSION['error'];
        }
        header('Location: /artist/artworks');
        exit;
    }

    public function collections()
    {
        $artist = Artist::getCurrentArtist();
        if (!$artist) {
            $_SESSION['error'] = 'You must be logged in as an artist to perform this action';
            header('Location: /index');
            exit;
        }
        $collections = $artist->getCollections();
        $artworks = $artist->getArtworks();
        $categories = Artwork::getCategories();
        $usedArtworks = count(array_reduce($collections, function ($carry, $collection) {
            return array_merge($carry, $collection->getCollectionArtworks());
        }, []));
        require_once VIEWS . 'pages\Artist\collections.php';
    }

    public function createCollection()
    {
        if (!isset($_POST['selectedArtworks']) || !isset($_POST['collectionTitle']) || !isset($_POST['collectionDescription']) || !isset($_FILES['coverImage'])) {
            $_SESSION['error'] = 'Invalid request';
            header('Location: /artist/collections');
            exit;
        }
        $selectedArtworks = $_POST['selectedArtworks'];
        $collectionName = $_POST['collectionTitle'];
        $collectionDescription = $_POST['collectionDescription'];
        $coverImage = $_FILES['coverImage'];

        if (empty($selectedArtworks) || empty($collectionName) || empty($collectionDescription) || empty($coverImage)) {
            $_SESSION['error'] = 'All fields are required';
            header('Location: /artist/collections');
            exit;
        }
        if ($coverImage['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Invalid image upload ' . $coverImage['error'];
            header('Location: /artist/collections');
            exit;
        }

        $upload = App::handleUploadImage($coverImage, 'collections');
        if (!$upload) {
            $_SESSION['error'] = "Failed to upload cover image " . $_SESSION['error'];
            header('Location: /artist/collections');
            exit;
        }

        $artist = Artist::getCurrentArtist();
        if (!$artist) {
            $_SESSION['error'] = 'You must be logged in as an artist to perform this action';
            header('Location: /index');
            exit;
        }

        $collection = $artist->createCollection([
            'name' => $collectionName,
            'description' => $collectionDescription,
            'coverImage' => $upload,
            'artworks' => $selectedArtworks
        ]);

        if ($collection) {
            $_SESSION['success'] = "Collection has been created successfully";
        } else {
            $_SESSION['error'] = "Failed to create collection: " . $_SESSION['error'];
        }
        header('Location: /artist/collections');
    }

    public function deleteCollection()
    {
        if (!isset($_POST['id'])) {
            $_SESSION['error'] = 'Invalid request';
            header('Location: /artist/collections');
            exit;
        }

        $collectionId = $_POST['id'];
        $artist = Artist::getCurrentArtist();
        if (!$artist) {
            $_SESSION['error'] = 'You must be logged in as an artist to perform this action';
            header('Location: /index');
            exit;
        }

        $success = $artist->deleteCollectionByID($collectionId);

        if ($success) {
            $_SESSION['success'] = "Collection has been deleted successfully";
        } else {
            $_SESSION['error'] = "Failed to delete collection";
        }

        header('Location: /artist/collections');
        exit;
    }

    private function getSalesChartData($period = 'week')
    {
        $db = Database::getInstance()->getConnection();
        $labels = [];
        $data = [];

        // Check if order table has any records
        $checkOrdersSql = "SELECT COUNT(*) as count FROM `order`";
        $checkOrdersStmt = $db->prepare($checkOrdersSql);
        $checkOrdersStmt->execute();
        $orderCount = $checkOrdersStmt->fetch(\PDO::FETCH_ASSOC)['count'] ?? 0;

        // If order table is empty, return default empty data
        if ($orderCount == 0) {
            return [
                'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                'data' => [0, 0, 0, 0, 0, 0, 0]
            ];
        }

        if ($period === 'week') {
            // Get sales for each day of the current week
            $startOfWeek = date('Y-m-d', strtotime('monday this week'));

            for ($i = 0; $i < 7; $i++) {
                $day = date('Y-m-d', strtotime($startOfWeek . " +$i days"));
                $dayLabel = date('D', strtotime($day));

                $sql = "SELECT COALESCE(SUM(totalPrice), 0) as total FROM `order` WHERE DATE(orderDate) = :day";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':day', $day, \PDO::PARAM_STR);
                $stmt->execute();

                $result = $stmt->fetch(\PDO::FETCH_ASSOC);
                $total = $result['total'] ? floatval($result['total']) : 0;

                $labels[] = $dayLabel;
                $data[] = $total;
            }
        }
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    public function withdraw()
    {
        $artist = Artist::getCurrentArtist();
        if (!$artist) {
            $_SESSION['error'] = 'You must be logged in as an artist to perform this action';
            header('Location: /index');
            exit;
        }

        $trnasactions = $artist->getAllTransactions();
        $pendingWithdrawals = count(array_filter($trnasactions, function ($transaction) {
            return $transaction['status'] === 'pending';
        }));
        $acceptedWithdraw = count(array_filter($trnasactions, function ($transaction) {
            return $transaction['status'] === 'accepted';
        }));
        $payment = $artist->getPaymentMethod();
        require_once VIEWS . 'pages\Artist\withdraw.php';
    }


    public function followers()
    {
        $artist = Artist::getCurrentArtist();
        if (!$artist) {
            $_SESSION['error'] = 'You must be logged in as an artist to perform this action';
            header('Location: /index');
            exit;
        }

        $followers = $artist->getFollowers();
        $totalFollowers = count($followers);
        require_once VIEWS . 'pages\Artist\followers.php';
    }

    public function notifications()
    {
        $artist = Artist::getCurrentArtist();
        if (!$artist) {
            $_SESSION['error'] = 'You must be logged in as an artist to perform this action';
            header('Location: /index');
            exit;
        }
        $notifications = $artist->getNotifications();
        $totalNotifications = count($notifications);
        require_once VIEWS . 'pages\Artist\notifications.php';
    }

    public function sellingHistory()
    {
        $artist = Artist::getCurrentArtist();
        if (!$artist) {
            $_SESSION['error'] = 'You must be logged in as an artist to perform this action';
            header('Location: /index');
            exit;
        }
        $orders = $artist->getSoldArtworks();
        $totalGainedMoney = array_reduce($orders, function ($carry, $order) {
            return $carry + $order['price'];
        }, 0);
        require_once VIEWS . 'pages\Artist\selling-history.php';
    }

    public function withdrawRequst()
    {
        if (!isset($_POST['amount']) || !isset($_POST['withdrawalType'])) {
            $_SESSION['error'] = 'Invalid request';
            header('Location: /artist/withdraw');
            exit;
        }
        $amount = $_POST['amount'];
        $withdrawalType = $_POST['withdrawalType'];
        $artist = Artist::getCurrentArtist();
        if (!$artist) {
            $_SESSION['error'] = 'You must be logged in as an artist to perform this action';
            header('Location: /index');
            exit;
        }

        if ($amount <= 0) {
            $_SESSION['error'] = 'Amount must be greater than 0';
            header('Location: /artist/withdraw');
            exit;
        }

        if ($amount > $artist->getBalance()) {
            $_SESSION['error'] = 'Amount must be less than or equal to your balance';
            header('Location: /artist/withdraw');
            exit;
        }

        if ($amount < 30) {
            $_SESSION['error'] = 'Minimum withdrawal amount is 30$';
            header('Location: /artist/withdraw');
            exit;
        }

        if ($withdrawalType != 'urgent' && $withdrawalType != 'normal') {
            $_SESSION['error'] = 'Invalid withdrawal type';
            header('Location: /artist/withdraw');
            exit;
        }

        $success = $artist->withdrawRequest($amount, $withdrawalType);
        if ($success) {
            $_SESSION['success'] = 'Withdrawal request has been sent successfully';
        } else {
            $_SESSION['error'] = 'Failed to send withdrawal request ' . $_SESSION['error'];
        }
        header('Location: /artist/withdraw');
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


    public function fairs()
    {
        $artist = Artist::getCurrentArtist();
        if (!$artist) {
            $_SESSION['error'] = 'You must be logged in as an artist to perform this action';
            header('Location: /index');
            exit;
        }

        $governments = ArtFair::getGoverments();
        $fairs = $artist->getArtFairs();
        $totalFairs = count($fairs);
        $pendingFairs = count(array_filter($fairs, function ($fair) {
            return $fair->getStatus() === 'pending';
        }));
        $acceptedFairs = count(array_filter($fairs, function ($fair) {
            return $fair->getStatus() === 'accepted';
        }));
        require_once VIEWS . 'pages\Artist\fairs.php';
    }

    public function registerFair()
    {
        if (!isset($_POST['fairName']) || !isset($_POST['fairGovernment']) || !isset($_POST['fairLocationDetails']) || !isset($_POST['fairDescription']) || !isset($_FILES['fairImage']) || !isset($_POST['fairDate'])) {
            $_SESSION['error'] = 'Invalid request';
            header('Location: /artist/fairs');
            exit;
        }

        $fairName = $_POST['fairName'];
        $fairGovernment = $_POST['fairGovernment'];
        $fairLocationDetails = $_POST['fairLocationDetails'];
        $fairDescription = $_POST['fairDescription'];
        $fairImage = $_FILES['fairImage'];
        $fairDate = $_POST['fairDate'];
        if (empty($fairName) || empty($fairGovernment) || empty($fairLocationDetails) || empty($fairDescription) || $fairImage['error'] !== UPLOAD_ERR_OK || empty($fairDate)) {
            $_SESSION['error'] = 'All fields are required';
            header('Location: /artist/fairs');
            exit;
        }


        $artist = Artist::getCurrentArtist();
        if (!$artist) {
            $_SESSION['error'] = 'You must be logged in as an artist to perform this action';
            header('Location: /index');
            exit;
        }

        $uploaded = App::handleUploadImage($fairImage, 'localfairs');

        if (!$uploaded) {
            $_SESSION['error'] = 'Failed to upload fair image';
            header('Location: /artist/fairs');
            exit;
        }

        $artfair = new ArtFair([
            'name' => $fairName,
            'location' => $fairGovernment . "," . $fairLocationDetails,
            'description' => $fairDescription,
            'image' => $uploaded,
            'artistID' => $artist->getUserID(),
            'startDate' => $fairDate,
        ]);

        $success = $artfair->createArtFair();

        if ($success) {
            $_SESSION['success'] = 'Art Fair registration has been sent successfully';
        } else {
            $_SESSION['error'] = 'Failed to create fair ' . $_SESSION['error'];
        }
        header('Location: /artist/fairs');
        exit;
    }

    public function deleteFair()
    {
        if (!isset($_POST['id'])) {
            $_SESSION['error'] = 'Invalid request';
            header('Location: /artist/fairs');
            exit;
        }

        $fairId = $_POST['id'];
        $artist = Artist::getCurrentArtist();
        if (!$artist) {
            $_SESSION['error'] = 'You must be logged in as an artist to perform this action';
            header('Location: /index');
            exit;
        }

        $success = $artist->deleteArtFair($fairId);

        if ($success) {
            $_SESSION['success'] = "Art Fair has been deleted successfully";
        } else {
            $_SESSION['error'] = "Failed to delete Art Fair " . $_SESSION['error'];
        }

        header('Location: /artist/fairs');
        exit;
    }
}
