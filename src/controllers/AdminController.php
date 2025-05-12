<?php

namespace App\controllers;

use App\models\Admin;
use App\models\User;
use App\models\Artwork;
use App\core\Database;
use App\models\ArtFair;
use App\models\Order;
use App\models\SpecialCollection;
use App\models\Offer;

class AdminController
{
    /**
     * Display admin dashboard
     */
    public function dashboard()
    {
        // Get current admin from session
        $admin = Admin::getCurrentAdmin();

        if (!$admin) {
            $_SESSION['error'] = 'You must be logged in as an admin to access this page';
            header('Location: /index');
            exit;
        }

        try {
            // Fetch dashboard statistics
            $stats = $this->getDashboardStats();


            // Fetch sales data for chart
            $salesData = $this->getSalesChartData();

            // Pass data to the view
            $viewData = [
                'admin' => $admin,
                'stats' => $stats,
                'salesData' => $salesData
            ];

            $offer = Offer::getInstance();

            // Include the dashboard view
            require_once VIEWS . 'pages/Admin/index.php';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Dashboard Error: ' . $e->getMessage();


            $admin = Admin::getCurrentAdmin();
            $stats = ['totalArtworks' => 0, 'activeUsers' => 0, 'ordersThisMonth' => 0, 'monthlyRevenue' => 0];
            $salesData = ['labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'], 'data' => [0, 0, 0, 0, 0, 0, 0]];

            require_once VIEWS . 'pages/Admin/index.php';
        }
    }

    private function getDashboardStats()
    {
        $db = Database::getInstance()->getConnection();

        // Get total artworks
        $artworksSql = "SELECT COUNT(*) as total FROM artwork";
        $artworksStmt = $db->prepare($artworksSql);
        $artworksStmt->execute();
        $totalArtworks = $artworksStmt->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;

        // Get active users (customers and artists)
        $usersSql = "SELECT COUNT(*) as total FROM user WHERE status = 'Accepted'";
        $usersStmt = $db->prepare($usersSql);
        $usersStmt->execute();
        $activeUsers = $usersStmt->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;

        // Check if order table has any records
        $checkOrdersSql = "SELECT COUNT(*) as count FROM `order`";
        $checkOrdersStmt = $db->prepare($checkOrdersSql);
        $checkOrdersStmt->execute();
        $orderCount = $checkOrdersStmt->fetch(\PDO::FETCH_ASSOC)['count'] ?? 0;

        // Default values in case the order table is empty
        $ordersThisMonth = 0;
        $monthlyRevenue = 0;

        // Only run these queries if the order table has data
        if ($orderCount > 0) {
            // Get orders this month
            $currentMonth = date('Y-m-01');
            $ordersSql = "SELECT COUNT(*) as total FROM `order` WHERE orderDate >= :currentMonth";
            $ordersStmt = $db->prepare($ordersSql);
            $ordersStmt->bindParam(':currentMonth', $currentMonth, \PDO::PARAM_STR);
            $ordersStmt->execute();
            $ordersThisMonth = $ordersStmt->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;

            // Get monthly revenue
            $revenueSql = "SELECT COALESCE(SUM(totalPrice), 0) as total FROM `order` WHERE orderDate >= :currentMonth";
            $revenueStmt = $db->prepare($revenueSql);
            $revenueStmt->bindParam(':currentMonth', $currentMonth, \PDO::PARAM_STR);
            $revenueStmt->execute();
            $monthlyRevenue = $revenueStmt->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;
        }

        return [
            'totalArtworks' => $totalArtworks,
            'activeUsers' => $activeUsers,
            'ordersThisMonth' => $ordersThisMonth,
            'monthlyRevenue' => $monthlyRevenue
        ];
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
        } elseif ($period === 'month') {
            // Implementation for monthly chart would go here
            // This would show sales for each week of the current month
        } else {
            // Implementation for yearly chart would go here
            // This would show sales for each month of the current year
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Handle approval of a pending item (artwork, artist, or fair)
     */
    public function approveItem()
    {
        if (!isset($_POST['id']) || !isset($_POST['type'])) {
            $_SESSION['error'] = 'Invalid request';
            header('Location: /admin/dashboard');
            exit;
        }

        $id = $_POST['id'];
        $type = $_POST['type'];

        $admin = Admin::getCurrentAdmin();

        if (!$admin) {
            $_SESSION['error'] = 'You must be logged in as an admin to perform this action';
            header('Location: /index');
            exit;
        }

        $success = false;

        switch ($type) {
            case 'Artwork':
                $success = $admin->updateArtworkStatus($id, 'Approved');
                break;
            case 'Artist':
                $success = $admin->updateUserStatus($id, 'Accepted');
                break;
            case 'Fair':
                // Implementation for fair approval with error handling
                try {
                    $db = Database::getInstance()->getConnection();

                    // Check if localfair table has a status column
                    $tableCheck = "SHOW COLUMNS FROM localfair LIKE 'status'";
                    $tableCheckStmt = $db->prepare($tableCheck);
                    $tableCheckStmt->execute();

                    if ($tableCheckStmt->rowCount() > 0) {
                        // Status column exists, update it
                        $sql = "UPDATE localfair SET status = 'Approved' WHERE eventID = :id";
                        $stmt = $db->prepare($sql);
                        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
                        $success = $stmt->execute();
                    } else {
                        $_SESSION['error'] = "Fair table does not have a status column. Please update the database schema.";
                        header('Location: /admin/dashboard');
                        exit;
                    }
                } catch (\Exception $e) {
                    error_log('Error approving fair: ' . $e->getMessage());
                    $success = false;
                }
                break;
        }

        if ($success) {
            $_SESSION['success'] = "$type has been approved successfully";
        } else {
            $_SESSION['error'] = "Failed to approve $type";
        }

        header('Location: /admin/dashboard');
        exit;
    }

    public function withdrawals()
    {
        $admin = Admin::getCurrentAdmin();
        if (!$admin) {
            $_SESSION['error'] = 'You must be logged in as an admin to access this page';
            header('Location: /index');
            exit;
        }

        // Get all withdrawals from the database
        $trnasactions = $admin->getWithdrawRequests() ?? [];
        $totalWithdrawals = count($trnasactions);
        $accepted = count(array_filter($trnasactions, function ($withdrawal) {
            return $withdrawal['status'] === 'accepted';
        }));
        $pending = count(array_filter($trnasactions, function ($withdrawal) {
            return $withdrawal['status'] === 'pending';
        }));

        require_once VIEWS . 'pages/Admin/withdrawals.php';
    }

    public function approveArtist()
    {
        if (!isset($_POST['id'])) {
            $_SESSION['error'] = 'Invalid request';
            header('Location: /admin/artists');
            exit;
        }

        $id = $_POST['id'];
        $admin = Admin::getCurrentAdmin();
        if (!$admin) {
            $_SESSION['error'] = 'You must be logged in as an admin to perform this action';
            header('Location: /index');
            exit;
        }

        $success = false;
        $success = $admin->updateUserStatus($id, 'Accepted');

        if ($success) {
            $_SESSION['success'] = "Artists Registration has been approved successfully";
        } else {
            $_SESSION['error'] = "Artists Registration Failed to approve";
        }

        header('Location: /admin/artists');
        exit;
    }

    public function rejectArtist()
    {
        if (!isset($_POST['id'])) {
            $_SESSION['error'] = 'Invalid request';
            header('Location: /admin/artists');
            exit;
        }

        $id = $_POST['id'];
        $reason = $_POST['reason'] ?? 'No reason provided';
        $admin = Admin::getCurrentAdmin();
        if (!$admin) {
            $_SESSION['error'] = 'You must be logged in as an admin to perform this action';
            header('Location: /index');
            exit;
        }

        $success = false;
        $success = $admin->updateUserStatus($id, 'Rejected', $reason);

        if ($success) {
            $_SESSION['success'] = "Artists Registration has been rejected successfully";
        } else {
            $_SESSION['error'] = "Artists Registration Failed to reject";
        }

        header('Location: /admin/artists');
        exit;
    }

    public function updateArtistStatus()
    {
        if (!isset($_POST['id']) || !isset($_POST['status'])) {
            $_SESSION['error'] = 'Invalid request';
            header('Location: /admin/artists');
            exit;
        }

        $id = $_POST['id'];
        $status = $_POST['status'];
        $reason = $_POST['reason'] ?? 'No reason provided';
        $admin = Admin::getCurrentAdmin();
        if (!$admin) {
            $_SESSION['error'] = 'You must be logged in as an admin to perform this action';
            header('Location: /index');
            exit;
        }

        $success = false;
        $success = $admin->updateUserStatus($id, $status, $reason);

        if ($success) {
            $_SESSION['success'] = "Artists Status has been updated successfully";
        } else {
            $_SESSION['error'] = "Artists Status Failed to update";
        }

        header('Location: /admin/artists');
        exit;
    }

    public function updateCustomerStatus()
    {
        if (!isset($_POST['id']) || !isset($_POST['status'])) {
            $_SESSION['error'] = 'Invalid request';
            header('Location: /admin/artists');
            exit;
        }

        $id = $_POST['id'];
        $status = $_POST['status'];
        $reason = $_POST['reason'] ?? 'No reason provided';
        $admin = Admin::getCurrentAdmin();
        if (!$admin) {
            $_SESSION['error'] = 'You must be logged in as an admin to perform this action';
            header('Location: /index');
            exit;
        }

        $success = false;
        $success = $admin->updateUserStatus($id, $status, $reason);

        if ($success) {
            $_SESSION['success'] = "Customer Status has been updated successfully";
        } else {
            $_SESSION['error'] = "Customer Status Failed to update";
        }

        header('Location: /admin/customers');
        exit;
    }

    /**
     * Handle rejection of a pending item (artwork, artist, or fair)
     */
    public function rejectItem()
    {
        if (!isset($_POST['id']) || !isset($_POST['type']) || !isset($_POST['reason'])) {
            $_SESSION['error'] = 'Invalid request';
            header('Location: /admin/dashboard');
            exit;
        }

        $id = $_POST['id'];
        $type = $_POST['type'];
        $reason = $_POST['reason'];

        $admin = Admin::getCurrentAdmin();

        if (!$admin) {
            $_SESSION['error'] = 'You must be logged in as an admin to perform this action';
            header('Location: /index');
            exit;
        }

        $success = false;

        switch ($type) {
            case 'Artwork':
                $success = $admin->updateArtworkStatus($id, 'Rejected', $reason);
                break;
            case 'Artist':
                $success = $admin->updateUserStatus($id, 'Rejected', $reason);
                break;
            case 'Fair':
                // Implementation for fair rejection would go here
                break;
        }

        if ($success) {
            $_SESSION['success'] = "$type has been rejected";
        } else {
            $_SESSION['error'] = "Failed to reject $type";
        }

        header('Location: /admin/dashboard');
        exit;
    }

    /**
     * Display admin artworks management page
     */
    public function artworks()
    {
        try {
            // Get the current admin from session
            $admin = Admin::getCurrentAdmin();

            if (!$admin) {
                $_SESSION['error'] = 'You must be logged in as an admin to access this page';
                header('Location: /index');
                exit;
            }


            // Get artworks with pagination
            $artworks = Artwork::getAllArtworks();


            // Get the list of unique categories for the filter
            $categories = Artwork::getCategories();
            // Load the view
            require_once VIEWS . 'pages/Admin/artworks.php';
        } catch (\Exception $e) {
            // Log the error
            error_log('AdminController::artworks - Error: ' . $e->getMessage());

            // Set error message
            $_SESSION['error'] = 'An error occurred while loading artworks. Please try again later.';

            // Get the current admin for the header component
            $admin = Admin::getCurrentAdmin();

            // Load the view with empty data
            $artworks = [];
            $categories = [];

            require_once VIEWS . 'pages/Admin/artworks.php';
        }
    }

    public function orders()
    {
        $admin = Admin::getCurrentAdmin();
        if (!$admin) {
            $_SESSION['error'] = 'You must be logged in as an admin to access this page';
            header('Location: /index');
            exit;
        }

        // Get all orders from the database
        $orders = Order::getAllOrders() ?? [];
        $totalOrders = count($orders);
        $totalItems = array_reduce($orders, function ($carry, $order) {
            return $carry + $order['totalItems'];
        }, 0);
        $totalPrice = array_reduce($orders, function ($carry, $order) {
            return $carry + $order['totalPrice'];
        }, 0);


        require_once VIEWS . 'pages/Admin/orders.php';
    }


    /**
     * Update artwork status (approve or reject)
     */
    public function updateArtworkStatus()
    {
        if (!isset($_POST['id']) || !isset($_POST['status'])) {
            $_SESSION['error'] = 'Invalid request';
            header('Location: /admin/artists');
            exit;
        }

        $id = $_POST['id'];
        $status = $_POST['status'];
        $reason = $_POST['reason'] ?? 'No reason provided';
        $admin = Admin::getCurrentAdmin();
        if (!$admin) {
            $_SESSION['error'] = 'You must be logged in as an admin to perform this action';
            header('Location: /index');
            exit;
        }

        $success = false;
        $success = $admin->updateArtworkStatus($id, $status, $reason);

        if ($success) {
            $_SESSION['success'] = "Artists Status has been updated successfully";
        } else {
            $_SESSION['error'] = "Artists Status Failed to update";
        }

        header('Location: /admin/artworks');
        exit;
    }

    public function profile()
    {
        // Get the current admin from session
        $admin = Admin::getCurrentAdmin();

        if (!$admin) {
            $_SESSION['error'] = 'You must be logged in as an admin to access this page';
            header('Location: /index');
            exit;
        }

        // Load the view
        require_once VIEWS . 'pages/Admin/profile.php';
    }

    /**
     * Display admin artists management page
     */
    public function artists()
    {
        try {
            // Get the current admin from session
            $admin = Admin::getCurrentAdmin();

            if (!$admin) {
                $_SESSION['error'] = 'You must be logged in as an admin to access this page';
                header('Location: /index');
                exit;
            }

            // Get all artists and pending artists
            $artists = $admin->getAllArtists() ?? [];
            // Load the view
            require_once VIEWS . 'pages/Admin/artists.php';
        } catch (\Exception $e) {
            // Log the error
            error_log('AdminController::artists - Error: ' . $e->getMessage());

            // Set error message
            $_SESSION['error'] = 'An error occurred while loading artists data. Please try again later.';

            // Get the current admin for the header component
            $admin = Admin::getCurrentAdmin();

            // Load the view with empty data
            $artists = [];

            require_once VIEWS . 'pages/Admin/artists.php';
        }
    }




    /**
     * Display admin customers management page
     */
    public function customers()
    {
        try {
            // Get the current admin from session
            $admin = Admin::getCurrentAdmin();

            if (!$admin) {
                $_SESSION['error'] = 'You must be logged in as an admin to access this page';
                header('Location: /index');
                exit;
            }

            // Get all customers
            $customers = $admin->getAllCustomers() ?? [];

            // Load the view
            require_once VIEWS . 'pages/Admin/customers.php';
        } catch (\Exception $e) {

            // Set error message
            $_SESSION['error'] = 'An error occurred while loading customers. Please try again later.';

            $admin = Admin::getCurrentAdmin();

            // Load the view with empty data
            $customers = [];

            require_once VIEWS . 'pages/Admin/customers.php';
        }
    }


    public function profilePicUpdate()
    {
        if (!isset($_FILES['profilePic']) || $_FILES['profilePic']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Invalid request';
            header('Location: /admin/profile');
            exit;
        }

        $admin = Admin::getCurrentAdmin();
        if (!$admin) {
            $_SESSION['error'] = 'You must be logged in as an admin to perform this action';
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
            header('Location: /admin/profile');
            exit;
        }

        // Generate unique filename
        $newFileName = uniqid('admin_') . '.' . pathinfo($fileName, PATHINFO_EXTENSION);
        $uploadPath = UPLOADS . 'profiles/' . $newFileName;

        // Check if directory exists and is writable
        $directory = UPLOADS . 'profiles/';
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        if (!is_writable($directory)) {
            $_SESSION['error'] = "Upload directory is not writable";
            header('Location: /admin/profile');
            exit;
        }

        // Move uploaded file
        $fileTmpName = $profilePic['tmp_name'];
        if (move_uploaded_file($fileTmpName, $uploadPath)) {
            // Delete old profile pic if not default
            $oldPic = $admin->getProfilePic();
            if ($oldPic !== 'default.jpg') {
                $oldPath = UPLOADS . 'profiles/' . $oldPic;
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $success = $admin->updateProfile([
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

            header('Location: /admin/profile');
            exit;
        } else {
            $_SESSION['error'] = "Failed to upload profile picture. Check file permissions.";
            header('Location: /admin/profile');
            exit;
        }
    }

    public function profileUpdate()
    {
        if (!isset($_POST['firstName']) || !isset($_POST['lastName']) || !isset($_POST['email'])) {
            $_SESSION['error'] = 'Invalid request';
            header('Location: /admin/profile');
            exit;
        }

        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $email = $_POST['email'];

        $admin = Admin::getCurrentAdmin();
        if (!$admin) {
            $_SESSION['error'] = 'You must be logged in as an admin to perform this action';
            header('Location: /index');
            exit;
        }

        $success = $admin->updateProfile([
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $email
        ]);

        if ($success) {
            $_SESSION['success'] = "Profile has been updated successfully";
        } else {
            $_SESSION['error'] = "Failed to update profile : " . $_SESSION['error'];
        }

        header('Location: /admin/profile');
        exit;
    }

    public function changePassword()
    {
        if (!isset($_POST['currentPassword']) || !isset($_POST['newPassword'])) {
            $_SESSION['error'] = 'Invalid request';
            header('Location: /admin/profile');
            exit;
        }

        $oldPassword = $_POST['currentPassword'];
        $newPassword = $_POST['newPassword'];

        $admin = Admin::getCurrentAdmin();
        if (!$admin) {
            $_SESSION['error'] = 'You must be logged in as an admin to perform this action';
            header('Location: /index');
            exit;
        }

        if ($newPassword == $oldPassword) {
            $_SESSION['error'] = 'New password must be different from old password';
            header('Location: /admin/profile');
            exit;
        }

        if (strlen($newPassword) < 8) {
            $_SESSION['error'] = 'New password must be at least 6 characters long';
            header('Location: /admin/profile');
            exit;
        }

        // Check if the old password is correct
        if (!$admin->checkPassword($oldPassword)) {
            $_SESSION['error'] = 'Old password is incorrect';
            header('Location: /admin/profile');
            exit;
        }



        // Update the password
        $success = $admin->changePassword($oldPassword, $newPassword);

        if ($success) {
            $_SESSION['success'] = "Password has been changed successfully";
        } else {
            $_SESSION['error'] = "Failed to change password : " . $_SESSION['error'];
        }

        header('Location: /admin/profile');
        exit;
    }

    public function fairs()
    {
        $admin = Admin::getCurrentAdmin();
        if (!$admin) {
            $_SESSION['error'] = 'You must be logged in as an admin to access this page';
            header('Location: /index');
            exit;
        }

        // Get all fairs from the database
        $fairs = $admin->getAllArtFairs();
        $totalFairs = count($fairs);
        $acceptedFairs  = count(array_filter($fairs, function ($fair) {
            return $fair->getStatus() === 'accepted';
        }));
        $pendingFairs  = count(array_filter($fairs, function ($fair) {
            return $fair->getStatus() === 'pending';
        }));
        $governments     = ArtFair::getGoverments();


        require_once VIEWS . 'pages/Admin/fairs.php';
    }


    public function updateFairStatus()
    {
        if (!isset($_POST['id']) || !isset($_POST['status'])) {
            $_SESSION['error'] = 'Invalid request';
            header('Location: /admin/fairs');
            exit;
        }

        $id = $_POST['id'];
        $status = $_POST['status'];
        $reason = $_POST['reason'] ?? 'No reason provided';
        $admin = Admin::getCurrentAdmin();
        if (!$admin) {
            $_SESSION['error'] = 'You must be logged in as an admin to perform this action';
            header('Location: /index');
            exit;
        }
        $statusValid = ['accepted', 'rejected', 'pending'];
        if (!in_array($status, $statusValid)) {
            $_SESSION['error'] = 'Invalid status ';
            header('Location: /admin/fairs');
            exit;
        }
        $success = false;
        $success = $admin->updateArtFairStatus($id, $status, $reason);

        if ($success) {
            $_SESSION['success'] = "Fair Status has been updated successfully";
        } else {
            $_SESSION['error'] = "Fair Status Failed to update";
        }

        header('Location: /admin/fairs');
        exit;
    }

    public function collections()
    {

        $admin = Admin::getCurrentAdmin();
        $artworks =  Artwork::getAllAcceptedArtworks();
  
        $categories = Artwork::getCategories();
        $collection = SpecialCollection::getInstance();
        if ($collection) {
            $selectedArtworksDate = $collection->getArtworks();
            $selectedArtworks = [];
            foreach ($selectedArtworksDate as $artwork) {
                array_push($selectedArtworks, $artwork->getArtworkID());
            }
        }

        require_once VIEWS . 'pages/Admin/collections.php';
    }

    public function updateSpecialCollections()
    {

        if (!isset($_POST['collectionName']) || !isset($_POST['selectedArtworks'])) {
            $_SESSION['error'] = 'Invalid request';
            header('Location: /admin/collections');
            exit;
        }

        $collectionName = $_POST['collectionName'];
        $selectedArtworks = explode(',', $_POST['selectedArtworks']);

        $admin = Admin::getCurrentAdmin();
        if (!$admin) {
            $_SESSION['error'] = 'You must be logged in as an admin to perform this action';
            header('Location: /index');
            exit;
        }

        $success = false;
        $success = $admin->updateSpecialCollections($collectionName, $selectedArtworks);

        if ($success) {
            $_SESSION['success'] = "Special Collection has been updated successfully";
        } else {
            $_SESSION['error'] = "Failed to update Special Collection. " . $_SESSION['error'];
        }

        header('Location: /admin/collections');
        exit;
    }

    public function updateOffer()
    {
        if (!isset($_POST['discount'])) {
            $_SESSION['error'] = 'Invalid request';
            header('Location: /admin/dashboard');
            exit;
        }
        $enableOffer = isset($_POST['enableOffer']) ? 1 : 0;
        $discount = $_POST['discount'];

        $admin = Admin::getCurrentAdmin();

        if (!$admin) {
            $_SESSION['error'] = 'You must be logged in as an admin to perform this action';
            header('Location: /index');
            exit;
        }

        $offer = Offer::getInstance();
        $success = $offer->updateOffer([
            'discount' => $discount,
            'enabled' => $enableOffer
        ]);
        if ($success) {
            $_SESSION['success'] = "Offer has been updated successfully";
        } else {
            $_SESSION['error'] = "Failed to update Offer. " . $_SESSION['error'];
        }
        header('Location: /admin/dashboard');
        exit;
    }
}
