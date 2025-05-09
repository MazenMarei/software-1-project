<?php

namespace App\models;

use App\core\Database;

class Transaction
{
    private $transactionID;
    private $amount;
    private $date;
    private $type;
    private $status;

    public function __construct($amount, $type, $status = 'pending', $date = null)
    {
        $this->amount = $amount;
        $this->type = $type;
        $this->status = $status;
        $this->date = $date ?? date('Y-m-d');
    }
    public function getTransactionID()
    {
        return $this->transactionID;
    }
    public function getAmount()
    {
        return $this->amount;
    }
    public function getDate()
    {
        return $this->date;
    }
    public function getType()
    {
        return $this->type;
    }
    public function getStatus()
    {
        return $this->status;
    }

    public function createTransaction()
    {
        try {
            $sql = "INSERT INTO transaction ( amount, type, date, status) VALUES (:amount, :type, :date, :status)";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':amount', $this->amount);
            $stmt->bindParam(':type', $this->type);
            $stmt->bindParam(':date', $this->date);
            $stmt->bindParam(':status', $this->status);
            $stmt->execute();
            $this->transactionID = Database::getInstance()->getConnection()->lastInsertId();
            return $this->transactionID;
        } catch (\PDOException $e) {
            $_SESSION['error'] =  $e->getMessage();
            return false;
        }
    }

    public static function getTransactionById($transactionID)
    {
        try {
            $sql = "SELECT * FROM transaction WHERE transactionID = :transactionID";
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->bindParam(':transactionID', $transactionID);
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function getArtistTransactions($artistId)
    {
        try {
            $db = Database::getInstance()->getConnection();

            $sql = "SELECT 
                        t.transactionID,
                        t.amount,
                        DATE_FORMAT(t.datee, '%Y-%m-%d') AS formatted_date,
                        t.type,
                        t.status
                    FROM transaction t
                    JOIN artisttransaction at ON t.transactionID = at.transactionID
                    WHERE at.artistID = :artistId
                    ORDER BY t.datee DESC";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':artistId', $artistId, \PDO::PARAM_INT);
            $stmt->execute();

            $transactions = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if (!$transactions) {
                $_SESSION['error'] = 'No transactions found for this artist.';
                return [];
            }
            return $transactions;
        } catch (\PDOException $e) {
            $_SESSION['error'] = 'Error fetching transactions: ' . $e->getMessage();
            return [];
        }
    }
}
