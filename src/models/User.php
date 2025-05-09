<?php

namespace App\models;

use App\core\Database;
use App\Middlewares\Authentication;

class User
{
    protected $userID;
    protected $firstName;
    protected $lastName;
    protected $email;
    protected $role;
    protected $username;
    protected $password;
    protected $profilePic;
    protected $emailNotification;
    protected $status;
    protected $registerDate; /// add to artist

    public function __construct($firstName = null, $lastName = null, $email = null, $role = null, $username = null, $profilePic = null)
    {

        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->role = $role;
        $this->username = $username;
        $this->profilePic = $profilePic;
        $this->emailNotification = true;
        $this->registerDate = date('Y-m-d');
    }

    /**
     * Handle user login process
     * 
     * @param string $email User email
     * @param string $password User password
     * @return array Response with status, message, and token if successful
     */
    public function login(string $email, string $password)
    {
        // Validate the input data
        if (empty($email) || empty($password)) {
            return [
                'status' => false,
                'message' => 'Email and password are required.'
            ];
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'status' => false,
                'message' => 'Invalid email format.'
            ];
        }

        try {
            // Get user from database
            $sql = "SELECT * FROM user WHERE Email = :email";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':email', $email, \PDO::PARAM_STR);
            $stmt->execute();

            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            // Check if user exists
            if (!$user) {
                return [
                    'status' => false,
                    'message' => 'Invalid email or password.'
                ];
            }

            // Verify password
            if (!password_verify($password, $user['password'])) {
                return [
                    'status' => false,
                    'message' => 'Invalid email or password.'
                ];
            }

            // Check if user's account is active/approved
            if ($user['status'] !== 'Accepted') {
                return [
                    'status' => false,
                    'message' => 'Your account is pending approval. Please wait for admin verification.'
                ];
            }

            // Create JWT token
            $token = Authentication::generateToken($user['userID'], $user['Email'], $user['role']);

            return [
                'status' => true,
                'message' => 'Login successful.',
                'token' => $token,
                'role' => $user['role'],
                'userId' => $user['userID']
            ];
        } catch (\PDOException $e) {
            return [
                'status' => false,
                'message' => 'Login failed. Please try again later.'
            ];
        }
    }

    public function getUserById($id)
    {
        try {
            $sql = "SELECT * FROM user WHERE userID = :id";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();

            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($user) {
                $this->userID = $user['userID'];
                $this->firstName = $user['Fname'];
                $this->lastName = $user['Lname'];
                $this->email = $user['Email'];
                $this->role = $user['role'];
                $this->username = $user['username'];
                $this->profilePic = $user['profilePic'];
                $this->emailNotification = $user['emailNotification'];
                $this->status = $user['status'];
                $this->registerDate = $user['registerDate'];
                return $this;
            } else {
                return false;
            }
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function updateProfile($data)
    {
        try {
            $sql = "UPDATE user SET ";
            if (isset($data['email'])) {
                if ((new Gust())->userExistsByEmail($data['email']) && $data['email'] !== $this->email) {
                    $_SESSION['error'] = 'Email already exists';
                    return false;
                }
                $sql .= "Email = :email, ";
            }
            if (isset($data['firstName'])) {
                $sql .= "Fname = :firstName, ";
            }
            if (isset($data['lastName'])) {
                $sql .= "Lname = :lastName, ";
            }

            if (isset($data['emailNotification'])) {
                $sql .= "emailNotification = :emailNotification, ";
            }
            if (isset($data['profilePic'])) {
                $sql .= "profilePic = :profilePic, ";
            }

            $sql = rtrim($sql, ', ') . " WHERE userID = :id";

            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            if (isset($data['profilePic'])) {
                $stmt->bindParam(':profilePic', $data['profilePic'], \PDO::PARAM_STR);
            }
            if (isset($data['firstName'])) {
                $stmt->bindParam(':firstName', $data['firstName'], \PDO::PARAM_STR);
            }
            if (isset($data['lastName'])) {
                $stmt->bindParam(':lastName', $data['lastName'], \PDO::PARAM_STR);
            }
            if (isset($data['email'])) {
                $stmt->bindParam(':email', $data['email'], \PDO::PARAM_STR);
            }
            if (isset($data['emailNotification'])) {
                $stmt->bindParam(':emailNotification', $data['emailNotification'], \PDO::PARAM_BOOL);
            }
            $stmt->bindParam(':id', $this->userID, \PDO::PARAM_INT);

            if ($stmt->execute()) {
                if (isset($data['firstName'])) {
                    $this->firstName = $data['firstName'];
                }
                if (isset($data['lastName'])) {
                    $this->lastName = $data['lastName'];
                }
                if (isset($data['email'])) {
                    $this->email = $data['email'];
                }

                if (isset($data['emailNotification'])) {
                    $this->emailNotification = $data['emailNotification'];
                }
                if (isset($data['profilePic'])) {
                    $this->profilePic = $data['profilePic'];
                }
                return true;
            }
            $_SESSION['error'] = "Failed to update profile4";
            return false;
        } catch (\Throwable $th) {
            $_SESSION['error'] = "Failed to update profile5" . $th->getMessage();
            return false;
        }
    }
    public function checkPassword($password)
    {
        try {
            $sql = "SELECT password FROM user WHERE userID = :id";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':id', $this->userID, \PDO::PARAM_INT);
            $stmt->execute();

            $userData = $stmt->fetch(\PDO::FETCH_ASSOC);
            return password_verify($password, $userData['password']);
        } catch (\Throwable $th) {
            return false;
        }
    }
    public function changePassword($currentPassword, $newPassword)
    {
        try {
            // First verify current password
            $sql = "SELECT password FROM user WHERE userID = :id";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':id', $this->userID, \PDO::PARAM_INT);
            $stmt->execute();

            $userData = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!password_verify($currentPassword, $userData['password'])) {
                $_SESSION['error'] = 'Current password is incorrect.';
                return false;
            }

            // Update with new password
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $sql = "UPDATE user SET password = :password WHERE userID = :id";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':password', $hashedPassword, \PDO::PARAM_STR);
            $stmt->bindParam(':id', $this->userID, \PDO::PARAM_INT);

            return $stmt->execute();
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Get notifications for this user
     * 
     * @param int $limit Number of notifications to retrieve
     * @return array Notifications
     */
    public function getNotifications()
    {
        try {
            $sql = "SELECT * FROM notification WHERE userid = :userID ORDER BY datesent DESC";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':userID', $this->userID, \PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            return [];
        }
    }

    /**
     * Create a new user in the database
     * 
     * @return bool|int False on failure, user ID on success
     */
    public function create($password)
    {
        try {
            try {
            } catch (\Exception $th) {
            }

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $sql = "INSERT INTO user (Fname, Lname, Email, role, username, password, profilePic, emailNotification, status, registerDate) 
                    VALUES (:firstName, :lastName, :email, :role, :username, :password, :profilePic, :emailNotification, :status, :registerDate)";

            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':firstName', $this->firstName, \PDO::PARAM_STR);
            $stmt->bindParam(':lastName', $this->lastName, \PDO::PARAM_STR);
            $stmt->bindParam(':email', $this->email, \PDO::PARAM_STR);
            $stmt->bindParam(':role', $this->role, \PDO::PARAM_STR);
            $stmt->bindParam(':username', $this->username, \PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashedPassword, \PDO::PARAM_STR);
            $stmt->bindParam(':profilePic', $this->profilePic, \PDO::PARAM_STR);
            $stmt->bindParam(':emailNotification', $this->emailNotification, \PDO::PARAM_BOOL);
            $stmt->bindParam(':status', $this->status, \PDO::PARAM_STR);
            $stmt->bindParam(':registerDate', $this->registerDate, \PDO::PARAM_STR);

            if ($stmt->execute()) {
                $this->userID = Database::getInstance()->getConnection()->lastInsertId();
                return $this->userID;
            }
            return false;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Update user status
     * 
     * @param string $status New status
     * @return bool Success
     */
    public function updateStatus($status)
    {
        try {
            $sql = "UPDATE user SET status = :status WHERE userID = :id";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':status', $status, \PDO::PARAM_STR);
            $stmt->bindParam(':id', $this->userID, \PDO::PARAM_INT);

            if ($stmt->execute()) {
                $this->status = $status;
                return true;
            }
            return false;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Get the currently logged-in user from session
     * 
     * @return User|false The current user object or false if not logged in
     */
    public static function getCurrentUser()
    {
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
            return false;
        }

        $user = new User();
        return $user->getUserById($_SESSION['user']['id']);
    }

    // Getters
    public function getUserID()
    {
        return $this->userID;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getProfilePic()
    {
        return $this->profilePic;
    }

    public function getEmailNotification()
    {
        return $this->emailNotification;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getRegisterDate()
    {
        return $this->registerDate;
    }

    public function getFullName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    // Setters
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setRole($role)
    {
        $this->role = $role;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setProfilePic($profilePic)
    {
        $this->profilePic = $profilePic;
    }

    public function setEmailNotification($emailNotification)
    {
        $this->emailNotification = $emailNotification;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }
}
