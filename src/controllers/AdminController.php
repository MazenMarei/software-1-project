<?php

namespace App\controllers;

use App\models\Admin;
use App\models\User;
use App\models\Artwork;
use App\core\Database;

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

            // Fetch pending approvals
            $pendingApprovals = $this->getPendingApprovals();

            // Fetch recent orders
            $recentOrders = $this->getRecentOrders();

            // Fetch sales data for chart
            $salesData = $this->getSalesChartData();

            // Pass data to the view
            $viewData = [
                'admin' => $admin,
                'stats' => $stats,
                'pendingApprovals' => $pendingApprovals,
                'recentOrders' => $recentOrders,
                'salesData' => $salesData
            ];

            // Include the dashboard view
            require_once VIEWS . 'pages/Admin/index.php';
        } catch (\Exception $e) {
            // Log the detailed error
            error_log('AdminController::dashboard - Error: ' . $e->getMessage());
            error_log('Error details: ' . $e->getTraceAsString());

            // Show a more detailed error message in development
            if (true) { // Change this to a development environment check in production
                $_SESSION['error'] = 'Dashboard Error: ' . $e->getMessage();
            } else {
                $_SESSION['error'] = 'An error occurred while loading some dashboard components. Showing limited dashboard.';
            }

            $admin = Admin::getCurrentAdmin();
            $stats = ['totalArtworks' => 0, 'activeUsers' => 0, 'ordersThisMonth' => 0, 'monthlyRevenue' => 0];
            $pendingApprovals = [];
            $recentOrders = [];
            $salesData = ['labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'], 'data' => [0, 0, 0, 0, 0, 0, 0]];

            require_once VIEWS . 'pages/Admin/index.php';
        }
    }

    /**
     * Get dashboard statistics
     * 
     * @return array Statistics for the dashboard
     */
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

    /**
     * Get pending approvals for the dashboard
     * 
     * @return array Pending artworks, artists, and fairs
     */
    private function getPendingApprovals()
    {
        $db = Database::getInstance()->getConnection();

        // Get pending artworks
        $artworksSql = "SELECT a.*, u.Fname, u.Lname FROM artwork a 
                        JOIN user u ON a.artistID = u.userID 
                        WHERE a.status = 'Pending' 
                        ORDER BY a.createDate DESC 
                        LIMIT 5";
        $artworksStmt = $db->prepare($artworksSql);
        $artworksStmt->execute();
        $pendingArtworks = $artworksStmt->fetchAll(\PDO::FETCH_ASSOC);

        // Get pending artists
        $artistsSql = "SELECT u.* FROM user u 
                      WHERE u.role = 'artist' AND u.status = 'Pending' 
                      ORDER BY u.registerDate DESC 
                      LIMIT 5";
        $artistsStmt = $db->prepare($artistsSql);
        $artistsStmt->execute();
        $pendingArtists = $artistsStmt->fetchAll(\PDO::FETCH_ASSOC);

        // Get pending local fairs - modified to check table structure first
        $pendingFairs = [];
        try {
            // Check if localfair table exists and has required columns
            $tableCheck = "SHOW COLUMNS FROM localfair LIKE 'status'";
            $tableCheckStmt = $db->prepare($tableCheck);
            $tableCheckStmt->execute();

            if ($tableCheckStmt->rowCount() > 0) {
                // Status column exists, use it in query
                $fairsSql = "SELECT f.*, u.Fname, u.Lname FROM localfair f 
                            JOIN user u ON f.atristID = u.userID 
                            WHERE f.status = 'Pending' 
                            ORDER BY f.date DESC 
                            LIMIT 5";
            } else {
                // Status column doesn't exist, adjust query to work without filtering by status
                $fairsSql = "SELECT f.*, u.Fname, u.Lname FROM localfair f 
                            JOIN user u ON f.atristID = u.userID 
                            ORDER BY f.date DESC 
                            LIMIT 5";
            }

            $fairsStmt = $db->prepare($fairsSql);
            $fairsStmt->execute();
            $pendingFairs = $fairsStmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log('Error retrieving pending fairs: ' . $e->getMessage());
            // Continue without fairs data if there's an error
        }

        // Combine all pending approvals
        $allPending = [];

        foreach ($pendingArtworks as $artwork) {
            $allPending[] = [
                'id' => $artwork['artworkID'],
                'name' => $artwork['title'],
                'type' => 'Artwork',
                'submittedBy' => $artwork['Fname'] . ' ' . $artwork['Lname'],
                'date' => $artwork['createDate']
            ];
        }

        foreach ($pendingArtists as $artist) {
            $allPending[] = [
                'id' => $artist['userID'],
                'name' => $artist['Fname'] . ' ' . $artist['Lname'],
                'type' => 'Artist',
                'submittedBy' => $artist['Fname'] . ' ' . $artist['Lname'],
                'date' => $artist['registerDate']
            ];
        }

        foreach ($pendingFairs as $fair) {
            $allPending[] = [
                'id' => $fair['eventID'],
                'name' => $fair['name'],
                'type' => 'Fair',
                'submittedBy' => $fair['Fname'] . ' ' . $fair['Lname'],
                'date' => $fair['date']
            ];
        }

        // Sort by most recent date
        usort($allPending, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        // Return only the 5 most recent items
        return array_slice($allPending, 0, 5);
    }

    /**
     * Get recent orders for the dashboard
     * 
     * @return array Recent orders
     */
    private function getRecentOrders()
    {
        $db = Database::getInstance()->getConnection();

        // Check if order table has any records
        $checkOrdersSql = "SELECT COUNT(*) as count FROM `order`";
        $checkOrdersStmt = $db->prepare($checkOrdersSql);
        $checkOrdersStmt->execute();
        $orderCount = $checkOrdersStmt->fetch(\PDO::FETCH_ASSOC)['count'] ?? 0;

        // Return empty array if no orders exist
        if ($orderCount == 0) {
            return [];
        }

        $sql = "SELECT o.*, u.Fname, u.Lname FROM `order` o
                JOIN user u ON o.customerID = u.userID
                ORDER BY o.orderDate DESC
                LIMIT 5";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $formattedOrders = [];

        foreach ($orders as $order) {
            $formattedOrders[] = [
                'id' => $order['orderID'],
                'customer' => $order['Fname'] . ' ' . $order['Lname'],
                'amount' => $order['totalPrice'],
                'date' => $order['orderDate'],
                'status' => $order['orderStatus']
            ];
        }

        return $formattedOrders;
    }

    /**
     * Get sales data for the dashboard chart
     * 
     * @param string $period 'week', 'month', or 'year'
     * @return array Sales data for the chart
     */
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

    public function approveArtist()
    {
        if (!isset($_POST['id']) || !isset($_POST['type'])) {
            $_SESSION['error'] = 'Invalid request';
            header('Location: /admin/artists');
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
        $success = $admin->updateUserStatus($id, 'Accepted');

        if ($success) {
            $_SESSION['success'] = "$type has been approved successfully";
        } else {
            $_SESSION['error'] = "Failed to approve $type";
        }
        
        header('Location: /admin/artists');
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

            // Get the current page from the URL query parameters, default to 1
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

            // Make sure page is at least 1
            $page = max(1, $page);

            // Get artworks with pagination
            $result = $this->getAllArtworks($page, 25);
            $artworks = $result['items'];
            $pagination = $result['pagination'];

            // Get the list of unique categories for the filter
            $categories = $this->getUniqueArtworkCategories();

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
            $pagination = [
                'currentPage' => 1,
                'totalPages' => 1,
                'totalItems' => 0,
                'limit' => 25,
                'hasNextPage' => false,
                'hasPrevPage' => false,
                'nextPage' => 1,
                'prevPage' => 1
            ];

            require_once VIEWS . 'pages/Admin/artworks.php';
        }
    }

    /**
     * Get unique artwork categories
     * 
     * @return array Array of unique artwork categories
     */
    private function getUniqueArtworkCategories()
    {
        try {
            $db = Database::getInstance();
            $conn = $db->getConnection();

            $query = "SELECT DISTINCT category FROM Artwork ORDER BY category";
            $stmt = $conn->prepare($query);
            $stmt->execute();

            $categories = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $categories[] = $row['category'];
            }

            return $categories;
        } catch (\Exception $e) {
            error_log('AdminController::getUniqueArtworkCategories - Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all artworks from the database with artist information and pagination
     * 
     * @param int $page Current page number (default: 1)
     * @param int $limit Number of items per page (default: 25)
     * @param array $filters Optional associative array of filters (status, category, price, search)
     * @return array Artworks with pagination information
     */
    private function getAllArtworks($page = 1, $limit = 25, $filters = [])
    {
        try {
            $db = Database::getInstance()->getConnection();

            // Build WHERE clause based on filters
            $whereClause = "";
            $params = [];

            if (!empty($filters)) {
                $conditions = [];

                // Status filter
                if (!empty($filters['status']) && $filters['status'] !== 'all') {
                    $conditions[] = "a.status = :status";
                    $params[':status'] = $filters['status'];
                }

                // Category filter
                if (!empty($filters['category']) && $filters['category'] !== 'all') {
                    $conditions[] = "a.category = :category";
                    $params[':category'] = $filters['category'];
                }

                // Price range filter
                if (!empty($filters['price']) && $filters['price'] !== 'all') {
                    switch ($filters['price']) {
                        case '0-100':
                            $conditions[] = "a.price BETWEEN 0 AND 100";
                            break;
                        case '100-500':
                            $conditions[] = "a.price BETWEEN 100 AND 500";
                            break;
                        case '500-1000':
                            $conditions[] = "a.price BETWEEN 500 AND 1000";
                            break;
                        case '1000-5000':
                            $conditions[] = "a.price BETWEEN 1000 AND 5000";
                            break;
                        case '5000+':
                            $conditions[] = "a.price > 5000";
                            break;
                    }
                }

                // Search filter (title, artist name, description)
                if (!empty($filters['search'])) {
                    $searchTerm = "%" . $filters['search'] . "%";
                    $conditions[] = "(a.title LIKE :search OR u.Fname LIKE :search OR u.Lname LIKE :search OR a.description LIKE :search)";
                    $params[':search'] = $searchTerm;
                }

                // Combine all conditions
                if (!empty($conditions)) {
                    $whereClause = " WHERE " . implode(" AND ", $conditions);
                }
            }

            // Calculate the offset for pagination
            $offset = ($page - 1) * $limit;

            // Get total count of artworks for pagination
            $countQuery = "SELECT COUNT(*) as total FROM artwork a JOIN user u ON a.artistID = u.userID" . $whereClause;
            $countStmt = $db->prepare($countQuery);
            foreach ($params as $key => $value) {
                $countStmt->bindValue($key, $value);
            }
            $countStmt->execute();
            $totalItems = (int)$countStmt->fetch(\PDO::FETCH_ASSOC)['total'];

            // Calculate total pages
            $totalPages = ceil($totalItems / $limit);

            // Make sure page is within valid range
            $page = max(1, min($page, $totalPages));

            // Get artworks with pagination
            $query = "SELECT a.*, u.Fname, u.Lname 
                     FROM artwork a
                     JOIN user u ON a.artistID = u.userID" .
                $whereClause . "
                     ORDER BY a.createDate DESC
                     LIMIT :offset, :limit";

            $stmt = $db->prepare($query);
            $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);

            // Bind any filter parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->execute();

            $artworks = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Build pagination metadata
            $pagination = [
                'currentPage' => $page,
                'totalPages' => max(1, $totalPages),
                'totalItems' => $totalItems,
                'limit' => $limit,
                'hasNextPage' => ($page < $totalPages),
                'hasPrevPage' => ($page > 1),
                'nextPage' => min($page + 1, max(1, $totalPages)),
                'prevPage' => max($page - 1, 1)
            ];

            return [
                'items' => $artworks,
                'pagination' => $pagination
            ];
        } catch (\Exception $e) {
            error_log('AdminController::getAllArtworks - Error: ' . $e->getMessage());

            // Return empty result with basic pagination structure
            return [
                'items' => [],
                'pagination' => [
                    'currentPage' => $page,
                    'totalPages' => 1,
                    'totalItems' => 0,
                    'limit' => $limit,
                    'hasNextPage' => false,
                    'hasPrevPage' => false,
                    'nextPage' => 1,
                    'prevPage' => 1
                ]
            ];
        }
    }

    /**
     * Get all artwork categories for filtering
     * 
     * @return array Categories list
     */
    private function getArtworkCategories()
    {
        $db = Database::getInstance()->getConnection();

        $sql = "SELECT DISTINCT category FROM artwork";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        // Create an array of unique categories
        $categories = [];
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($results as $result) {
            if (!empty($result['category'])) {
                $categories[] = $result['category'];
            }
        }

        return $categories;
    }

    /**
     * Update artwork status (approve or reject)
     */
    public function updateArtworkStatus()
    {
        // Verify admin is logged in
        $admin = Admin::getCurrentAdmin();

        if (!$admin) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
            exit;
        }

        // Check if this is a POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        // Get POST data
        $artworkId = $_POST['artworkId'] ?? null;
        $status = $_POST['status'] ?? null;
        $reason = $_POST['reason'] ?? '';

        // Validate input
        if (!$artworkId || !$status || !in_array($status, ['Accepted', 'Rejected'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid input data']);
            exit;
        }

        try {
            $db = Database::getInstance()->getConnection();

            // Update artwork status
            $sql = "UPDATE artwork SET status = :status WHERE artworkID = :artworkId";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':status', $status, \PDO::PARAM_STR);
            $stmt->bindParam(':artworkId', $artworkId, \PDO::PARAM_INT);
            $result = $stmt->execute();

            // If status is Rejected and a reason is provided, save the reason
            if ($status === 'Rejected' && !empty($reason)) {
                $sql = "UPDATE artwork SET rejectReason = :reason WHERE artworkID = :artworkId";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':reason', $reason, \PDO::PARAM_STR);
                $stmt->bindParam(':artworkId', $artworkId, \PDO::PARAM_INT);
                $stmt->execute();
            }

            if ($result) {
                // Notify the artist (implementation would depend on your notification system)
                // ...

                $_SESSION['success'] = "Artwork has been " . strtolower($status) . " successfully";
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
                exit;
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Failed to update artwork status']);
                exit;
            }
        } catch (\Exception $e) {
            error_log('AdminController::updateArtworkStatus - Error: ' . $e->getMessage());

            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
            exit;
        }
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
            $artists = $admin->getAllArtists();
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
            $pendingArtists = [];
            $artists = [];

            require_once VIEWS . 'pages/Admin/artists.php';
        }
    }

    /**
     * Get all artists from the database
     * 
     * @return array List of all artists
     */
    private function getAllArtists()
    {
        $db = Database::getInstance()->getConnection();

        $sql = "SELECT u.*, a.Bio, a.Balance, a.specialties 
                FROM user u
                JOIN artist a ON u.userID = a.artistID
                WHERE u.role = 'artist'
                ORDER BY u.status ASC, u.created_at DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Update artist status (approve or reject)
     */
    public function updateArtistStatus()
    {
        // Verify admin is logged in
        $admin = Admin::getCurrentAdmin();

        if (!$admin) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
            exit;
        }

        // Check if this is a POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        // Get POST data
        $artistId = $_POST['artistId'] ?? null;
        $status = $_POST['status'] ?? null;
        $reason = $_POST['reason'] ?? '';

        // Validate input
        if (!$artistId || !$status || !in_array($status, ['Accepted', 'Rejected'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid input data']);
            exit;
        }

        try {
            $db = Database::getInstance()->getConnection();

            // Update artist status
            $sql = "UPDATE user SET status = :status WHERE userID = :artistId";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':status', $status, \PDO::PARAM_STR);
            $stmt->bindParam(':artistId', $artistId, \PDO::PARAM_INT);
            $result = $stmt->execute();

            // If status is Rejected and a reason is provided, save the reason
            if ($status === 'Rejected' && !empty($reason)) {
                $sql = "UPDATE artist SET rejectReason = :reason WHERE artistID = :artistId";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':reason', $reason, \PDO::PARAM_STR);
                $stmt->bindParam(':artistId', $artistId, \PDO::PARAM_INT);
                $stmt->execute();
            }

            if ($result) {
                // Notify the artist (implementation would depend on your notification system)
                // ...

                $_SESSION['success'] = "Artist has been " . strtolower($status) . " successfully";
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
                exit;
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Failed to update artist status']);
                exit;
            }
        } catch (\Exception $e) {
            error_log('AdminController::updateArtistStatus - Error: ' . $e->getMessage());

            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Get artist details for modal
     */
    public function getArtistDetails()
    {
        // Get current admin from session
        $admin = Admin::getCurrentAdmin();

        if (!$admin) {
            echo json_encode([
                'success' => false,
                'message' => 'Not authorized'
            ]);
            return;
        }

        // Get the artist ID from GET parameters
        $artistId = $_GET['artistID'] ?? null;

        if (!$artistId) {
            echo json_encode([
                'success' => false,
                'message' => 'Missing artist ID'
            ]);
            return;
        }

        try {
            $db = Database::getInstance()->getConnection();

            // Get artist details
            $sql = "SELECT u.*, a.Bio, a.Balance, a.specialties 
                    FROM user u
                    JOIN artist a ON u.userID = a.artistID
                    WHERE u.userID = :artistId";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':artistId', $artistId, \PDO::PARAM_INT);
            $stmt->execute();

            $artist = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$artist) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Artist not found'
                ]);
                return;
            }

            // Format the artist data
            $artist['fullName'] = $artist['Fname'] . ' ' . $artist['Lname'];
            $artist['joinDate'] = date('F j, Y', strtotime($artist['created_at']));

            // Get specialties as array
            $artist['specialties'] = explode(',', $artist['specialties']);

            // Get total artworks
            $sqlArtworks = "SELECT COUNT(*) as total FROM artwork WHERE artistID = :artistId";
            $stmtArtworks = $db->prepare($sqlArtworks);
            $stmtArtworks->bindParam(':artistId', $artistId, \PDO::PARAM_INT);
            $stmtArtworks->execute();
            $artist['totalArtworks'] = $stmtArtworks->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;

            // Get recent artworks
            $sqlRecentArtworks = "SELECT * FROM artwork WHERE artistID = :artistId ORDER BY created_at DESC LIMIT 3";
            $stmtRecentArtworks = $db->prepare($sqlRecentArtworks);
            $stmtRecentArtworks->bindParam(':artistId', $artistId, \PDO::PARAM_INT);
            $stmtRecentArtworks->execute();
            $artist['recentArtworks'] = $stmtRecentArtworks->fetchAll(\PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'artist' => $artist
            ]);
        } catch (\Exception $e) {
            error_log('AdminController::getArtistDetails - Error: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'An error occurred while fetching artist details'
            ]);
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

            // Get the current page from the URL query parameters, default to 1
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

            // Make sure page is at least 1
            $page = max(1, $page);

            // Get customers with pagination
            $result = $this->getAllCustomers($page, 25);
            $customers = $result['items'];
            $pagination = $result['pagination'];

            // Load the view
            require_once VIEWS . 'pages/Admin/customers.php';
        } catch (\Exception $e) {
            // Log the error
            error_log('AdminController::customers - Error: ' . $e->getMessage());

            // Set error message
            $_SESSION['error'] = 'An error occurred while loading customers. Please try again later.';

            // Get the current admin for the header component
            $admin = Admin::getCurrentAdmin();

            // Load the view with empty data
            $customers = [];
            $pagination = [
                'currentPage' => 1,
                'totalPages' => 1,
                'totalItems' => 0,
                'limit' => 25,
                'hasNextPage' => false,
                'hasPrevPage' => false,
                'nextPage' => 1,
                'prevPage' => 1
            ];

            require_once VIEWS . 'pages/Admin/customers.php';
        }
    }

    /**
     * Get all customers from the database with pagination
     * 
     * @param int $page Current page number (default: 1)
     * @param int $limit Number of items per page (default: 25)
     * @param array $filters Optional associative array of filters (status, activity, search)
     * @return array Customers with pagination information
     */
    private function getAllCustomers($page = 1, $limit = 25, $filters = [])
    {
        try {
            $db = Database::getInstance()->getConnection();

            // Build WHERE clause based on filters
            $whereClause = "WHERE u.role = 'customer'";
            $params = [];

            if (!empty($filters)) {
                // Status filter
                if (!empty($filters['status']) && $filters['status'] !== 'all') {
                    if ($filters['status'] === 'active') {
                        $whereClause .= " AND u.status = 'Accepted'";
                    } else if ($filters['status'] === 'inactive') {
                        $whereClause .= " AND u.status = 'Rejected'";
                    }
                }

                // Activity level filter
                if (!empty($filters['activity']) && $filters['activity'] !== 'all') {
                    if ($filters['activity'] === 'high') {
                        // Customers with more than 5 orders
                        $whereClause .= " AND (SELECT COUNT(*) FROM `order` o WHERE o.customerID = u.userID) > 5";
                    } else if ($filters['activity'] === 'medium') {
                        // Customers with 2-5 orders
                        $whereClause .= " AND (SELECT COUNT(*) FROM `order` o WHERE o.customerID = u.userID) BETWEEN 2 AND 5";
                    } else if ($filters['activity'] === 'low') {
                        // Customers with 0-1 orders
                        $whereClause .= " AND (SELECT COUNT(*) FROM `order` o WHERE o.customerID = u.userID) <= 1";
                    }
                }

                // Search filter (name, email, ID)
                if (!empty($filters['search'])) {
                    $searchTerm = "%" . $filters['search'] . "%";
                    $whereClause .= " AND (u.Fname LIKE :search OR u.Lname LIKE :search OR u.email LIKE :search OR u.userID LIKE :search)";
                    $params[':search'] = $searchTerm;
                }
            }

            // Calculate the offset for pagination
            $offset = ($page - 1) * $limit;

            // Get total count of customers for pagination
            $countQuery = "SELECT COUNT(*) as total FROM user u $whereClause";
            $countStmt = $db->prepare($countQuery);
            foreach ($params as $key => $value) {
                $countStmt->bindValue($key, $value);
            }
            $countStmt->execute();
            $totalItems = (int)$countStmt->fetch(\PDO::FETCH_ASSOC)['total'];

            // Calculate total pages
            $totalPages = ceil($totalItems / $limit);

            // Make sure page is within valid range
            $page = max(1, min($page, $totalPages));

            // Get customers with pagination
            $query = "SELECT u.*, 
                        (SELECT COUNT(*) FROM `order` o WHERE o.customerID = u.userID) as order_count,
                        (SELECT COALESCE(SUM(totalPrice), 0) FROM `order` o WHERE o.customerID = u.userID) as total_spending,
                        c.Bio, c.phone
                     FROM user u
                     LEFT JOIN customer c ON u.userID = c.customerID
                     $whereClause
                     ORDER BY u.registerDate DESC
                     LIMIT :offset, :limit";

            $stmt = $db->prepare($query);
            $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);

            // Bind any filter parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->execute();

            $customers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Build pagination metadata
            $pagination = [
                'currentPage' => $page,
                'totalPages' => max(1, $totalPages),
                'totalItems' => $totalItems,
                'limit' => $limit,
                'hasNextPage' => ($page < $totalPages),
                'hasPrevPage' => ($page > 1),
                'nextPage' => min($page + 1, max(1, $totalPages)),
                'prevPage' => max($page - 1, 1)
            ];

            return [
                'items' => $customers,
                'pagination' => $pagination
            ];
        } catch (\Exception $e) {
            error_log('AdminController::getAllCustomers - Error: ' . $e->getMessage());

            // Return empty result with basic pagination structure
            return [
                'items' => [],
                'pagination' => [
                    'currentPage' => $page,
                    'totalPages' => 1,
                    'totalItems' => 0,
                    'limit' => $limit,
                    'hasNextPage' => false,
                    'hasPrevPage' => false,
                    'nextPage' => 1,
                    'prevPage' => 1
                ]
            ];
        }
    }

    /**
     * Handle customer status updates (activate or deactivate)
     */
    public function updateCustomerStatus()
    {
        // Verify admin is logged in
        $admin = Admin::getCurrentAdmin();

        if (!$admin) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
            exit;
        }

        // Check if this is a POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        // Get POST data
        $customerId = $_POST['customerId'] ?? null;
        $status = $_POST['status'] ?? null;
        $reason = $_POST['reason'] ?? '';

        // Validate input
        if (!$customerId || !$status || !in_array($status, ['Accepted', 'Rejected'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid input data']);
            exit;
        }

        try {
            $db = Database::getInstance()->getConnection();

            // Update customer status
            $sql = "UPDATE user SET status = :status WHERE userID = :customerId AND role = 'customer'";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':status', $status, \PDO::PARAM_STR);
            $stmt->bindParam(':customerId', $customerId, \PDO::PARAM_INT);
            $result = $stmt->execute();

            if ($result) {
                $_SESSION['success'] = "Customer status has been updated successfully";
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
                exit;
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Failed to update customer status']);
                exit;
            }
        } catch (\Exception $e) {
            error_log('AdminController::updateCustomerStatus - Error: ' . $e->getMessage());

            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Handle customer update from admin panel
     */
    public function updateCustomer()
    {
        // Check if the request is a POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        // Check if admin is logged in
        $admin = Admin::getCurrentAdmin();
        if (!$admin) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        // Get input data
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['customerId'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Customer ID is required']);
            exit;
        }

        try {
            $db = Database::getInstance()->getConnection();
            $customerId = $data['customerId'];

            // Build update query for user table
            $updateFields = [];
            $params = [':id' => $customerId];

            if (isset($data['name'])) {
                // Split name into first and last name
                $nameParts = explode(' ', $data['name'], 2);
                if (count($nameParts) > 0) {
                    $updateFields[] = 'Fname = :fname';
                    $params[':fname'] = $nameParts[0];

                    if (count($nameParts) > 1) {
                        $updateFields[] = 'Lname = :lname';
                        $params[':lname'] = $nameParts[1];
                    }
                }
            }

            if (isset($data['email'])) {
                $updateFields[] = 'email = :email';
                $params[':email'] = $data['email'];
            }

            if (isset($data['status'])) {
                $updateFields[] = 'status = :status';
                $params[':status'] = $data['status'] ? 'Accepted' : 'Inactive';
            }

            // Update user table if there are fields to update
            if (!empty($updateFields)) {
                $userQuery = "UPDATE user SET " . implode(', ', $updateFields) . " WHERE userID = :id";
                $userStmt = $db->prepare($userQuery);
                foreach ($params as $key => $value) {
                    $userStmt->bindValue($key, $value);
                }
                $userStmt->execute();
            }

            // Update customer table
            $customerFields = [];
            $customerParams = [':id' => $customerId];

            if (isset($data['phone'])) {
                $customerFields[] = 'phone = :phone';
                $customerParams[':phone'] = $data['phone'];
            }

            if (isset($data['address'])) {
                $customerFields[] = 'address = :address';
                $customerParams[':address'] = $data['address'];
            }

            // Update customer table if there are fields to update
            if (!empty($customerFields)) {
                $customerQuery = "UPDATE customer SET " . implode(', ', $customerFields) . " WHERE customerID = :id";
                $customerStmt = $db->prepare($customerQuery);
                foreach ($customerParams as $key => $value) {
                    $customerStmt->bindValue($key, $value);
                }
                $customerStmt->execute();
            }

            // Return success response
            echo json_encode([
                'success' => true,
                'message' => 'Customer updated successfully'
            ]);
            exit;
        } catch (\Exception $e) {
            error_log('Error updating customer: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'An error occurred while updating the customer'
            ]);
            exit;
        }
    }

    /**
     * Get customer details by ID
     */
    public function getCustomerDetails()
    {
        // Check if admin is logged in
        $admin = Admin::getCurrentAdmin();
        if (!$admin) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        // Get customer ID from request
        $customerId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($customerId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid customer ID']);
            exit;
        }

        try {
            $db = Database::getInstance()->getConnection();

            // Get customer details
            $query = "SELECT u.*, c.phone, c.date as joinDate, c.address, c.currency, c.balance, 
                     (SELECT COUNT(o.orderID) FROM `order` o WHERE o.customerID = u.userID) as orderCount,
                     (SELECT COALESCE(SUM(o.totalPrice), 0) FROM `order` o WHERE o.customerID = u.userID) as totalSpending
                     FROM user u
                     LEFT JOIN customer c ON u.userID = c.customerID
                     WHERE u.userID = :id AND u.role = 'customer'";

            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $customerId, \PDO::PARAM_INT);
            $stmt->execute();

            $customer = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$customer) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Customer not found']);
                exit;
            }

            // Get recent orders
            $orders = [];
            try {
                $orderQuery = "SELECT o.*, a.title, a.image 
                              FROM `order` o 
                              LEFT JOIN artwork a ON o.artworkID = a.artworkID
                              WHERE o.customerID = :id 
                              ORDER BY o.orderDate DESC 
                              LIMIT 5";
                $orderStmt = $db->prepare($orderQuery);
                $orderStmt->bindParam(':id', $customerId, \PDO::PARAM_INT);
                $orderStmt->execute();
                $orders = $orderStmt->fetchAll(\PDO::FETCH_ASSOC);
            } catch (\Exception $e) {
                error_log('Error getting customer orders: ' . $e->getMessage());
            }

            // Return customer data with orders
            echo json_encode([
                'success' => true,
                'customer' => $customer,
                'recentOrders' => $orders
            ]);
            exit;
        } catch (\Exception $e) {
            error_log('Error getting customer details: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'An error occurred while retrieving customer details'
            ]);
            exit;
        }
    }

    /**
     * Add a new customer
     */
    public function addCustomer()
    {
        // Check if the request is a POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        // Check if admin is logged in
        $admin = Admin::getCurrentAdmin();
        if (!$admin) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        // Get input data
        $data = json_decode(file_get_contents('php://input'), true);

        // Validate required fields
        if (!isset($data['name']) || !isset($data['email'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Name and email are required']);
            exit;
        }

        try {
            $db = Database::getInstance()->getConnection();

            // Split name into first and last name
            $nameParts = explode(' ', $data['name'], 2);
            $firstName = $nameParts[0];
            $lastName = count($nameParts) > 1 ? $nameParts[1] : '';

            // Generate a username based on email
            $username = explode('@', $data['email'])[0];

            // Generate a random password
            $password = bin2hex(random_bytes(8));
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Create user record
            $userQuery = "INSERT INTO user (Fname, Lname, email, username, password, role, profilepic, status) 
                         VALUES (:fname, :lname, :email, :username, :password, 'customer', 'default.jpg', :status)";

            $userStmt = $db->prepare($userQuery);
            $userStmt->bindParam(':fname', $firstName, \PDO::PARAM_STR);
            $userStmt->bindParam(':lname', $lastName, \PDO::PARAM_STR);
            $userStmt->bindParam(':email', $data['email'], \PDO::PARAM_STR);
            $userStmt->bindParam(':username', $username, \PDO::PARAM_STR);
            $userStmt->bindParam(':password', $hashedPassword, \PDO::PARAM_STR);
            $userStmt->bindParam(':status', $data['status'] ? 'Accepted' : 'Inactive', \PDO::PARAM_STR);
            $userStmt->execute();

            // Get the inserted user ID
            $userId = $db->lastInsertId();

            // Create customer record
            $customerQuery = "INSERT INTO customer (customerID, phone, date, address, currency, balance) 
                            VALUES (:id, :phone, CURDATE(), :address, 'USD', 0.00)";

            $customerStmt = $db->prepare($customerQuery);
            $customerStmt->bindParam(':id', $userId, \PDO::PARAM_INT);
            $customerStmt->bindParam(':phone', $data['phone'] ?? '', \PDO::PARAM_STR);
            $customerStmt->bindParam(':address', $data['address'] ?? '', \PDO::PARAM_STR);
            $customerStmt->execute();

            // Return success response
            echo json_encode([
                'success' => true,
                'message' => 'Customer created successfully',
                'customerId' => $userId,
                'tempPassword' => $password
            ]);
            exit;
        } catch (\Exception $e) {
            error_log('Error creating customer: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'An error occurred while creating the customer'
            ]);
            exit;
        }
    }
}
