<?php

namespace App\models;

use App\core\Database;
use App\Middlewares\Authentication;
use \Firebase\JWT\JWT;

class Gust
{

    public function register(string $fname, string $lname, string $username, string $email, string $password, string $role, string $profilePic = 'default.jpg')
    {
        // Validate the input data
        if (empty($fname) || empty($lname) || empty($email) || empty($password) || empty($username)) {
            return [
                'status' => false,
                'message' => 'All fields are required.'
            ];
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'status' => false,
                'message' => 'Invalid email format.'
            ];
        }

        // Validate password length and complexity
        if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
            return [
                'status' => false,
                'message' => 'Password must be at least 8 characters long and contain at least one uppercase letter and one number.'
            ];
        }

        // Validate username length
        if (strlen($username) < 4) {
            return [
                'status' => false,
                'message' => 'Username must be at least 4 characters long.'
            ];
        }

        // Validate names
        if ($fname === $lname) {
            return [
                'status' => false,
                'message' => 'First name and last name cannot be the same.'
            ];
        }

        // Validate account type
        if ($role !== 'customer' && $role !== 'artist') {
            return [
                'status' => false,
                'message' => 'Invalid account type.'
            ];
        }

        // Check if email is already registered
        $sql = "SELECT * FROM user WHERE Email = :email";
        $stmt = Database::getInstance()->getConnection()->prepare($sql);
        $stmt->bindParam(':email', $email, \PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return [
                'status' => false,
                'message' => 'Email already registered.'
            ];
        }

        // Check if username is already taken
        $sql = "SELECT * FROM user WHERE username = :username";
        $stmt = Database::getInstance()->getConnection()->prepare($sql);
        $stmt->bindParam(':username', $username, \PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return [
                'status' => false,
                'message' => 'Username already taken.'
            ];
        }

        // Hash the password
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $status = $role === 'customer' ? "Accepted" : "Pending";
        $registerDate = date('Y-m-d');

        try {
            // Start transaction
            Database::getInstance()->getConnection()->beginTransaction();

            // Insert into user table
            $sql = "INSERT INTO user (Fname, Lname, username, Email, password, role, status, registerDate, profilePic, emailNotification) 
                    VALUES (:fname, :lname, :username, :email, :password, :role, :status, :registerDate, :profilePic, :emailNotification)";

            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $emailNotification = 1; // Default to true

            $stmt->bindParam(':fname', $fname, \PDO::PARAM_STR);
            $stmt->bindParam(':lname', $lname, \PDO::PARAM_STR);
            $stmt->bindParam(':username', $username, \PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, \PDO::PARAM_STR);
            $stmt->bindParam(':password', $passwordHash, \PDO::PARAM_STR);
            $stmt->bindParam(':role', $role, \PDO::PARAM_STR);
            $stmt->bindParam(':status', $status, \PDO::PARAM_STR);
            $stmt->bindParam(':registerDate', $registerDate, \PDO::PARAM_STR);
            $stmt->bindParam(':profilePic', $profilePic, \PDO::PARAM_STR);
            $stmt->bindParam(':emailNotification', $emailNotification, \PDO::PARAM_BOOL);

            $stmt->execute();

            // Get the inserted user ID
            $userId = Database::getInstance()->getConnection()->lastInsertId();

            // If customer, create customer record
            if ($role === 'customer') {
                $sql = "INSERT INTO customer (customerID, currency, balance, date) VALUES (:userId, :currency, :balance, :date)";
                $stmt = Database::getInstance()->getConnection()->prepare($sql);

                $currency = 'USD';
                $balance = 0.00;

                $stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
                $stmt->bindParam(':currency', $currency, \PDO::PARAM_STR);
                $stmt->bindParam(':balance', $balance, \PDO::PARAM_STR);
                $stmt->bindParam(':date', $registerDate, \PDO::PARAM_STR);

                $stmt->execute();
            }
            // If artist, create artist record
            elseif ($role === 'artist') {
                $sql = "INSERT INTO artist (artistID, Balance) VALUES (:userId, :balance)";
                $stmt = Database::getInstance()->getConnection()->prepare($sql);

                $balance = 0.00;

                $stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
                $stmt->bindParam(':balance', $balance, \PDO::PARAM_STR);

                $stmt->execute();
            }

            // Commit transaction
            Database::getInstance()->getConnection()->commit();

            // If artist, return success message without token (pending approval)
            if ($role === 'artist') {
                return [
                    'status' => true,
                    'message' => 'Artist registration successful. Please wait for admin approval.'
                ];
            }

            // For customers, generate JWT token
            $token = Authentication::generateToken($userId, $email, $role);

            return [
                'status' => true,
                'message' => 'Registration successful.',
                'token' => $token,
                'role' => $role
            ];
        } catch (\PDOException $e) {
            // Rollback the transaction if something failed
            Database::getInstance()->getConnection()->rollBack();

            return [
                'status' => false,
                'message' => 'Registration failed. Please try again later.'
            ];
        }
    }



    public function userExistsByEmail(string $email)
    {
        $sql = "SELECT * FROM user WHERE Email = :email";
        $stmt = Database::getInstance()->getConnection()->prepare($sql);
        $stmt->bindParam(':email', $email, \PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }


    public function userExistsByUsername(string $username)
    {
        $sql = "SELECT * FROM user WHERE username = :username";
        $stmt = Database::getInstance()->getConnection()->prepare($sql);
        $stmt->bindParam(':username', $username, \PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
