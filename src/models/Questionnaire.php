<?php


namespace App\models;

use App\core\Database;


class Questionnaire
{

    private $questionnaireID;
    private $customerID;
    private $artStyle;
    private $budget;
    private $artType;
    private $submitDate;
    private $status;
    private $response;

    public function __construct($customerID, $artStyle, $budget, $artType, $questionnaireID = null, $response = null, $status = null, $submitDate = null)
    {
        $this->questionnaireID = $questionnaireID;
        $this->customerID = $customerID;
        $this->artStyle = $artStyle;
        $this->budget = $budget;
        $this->artType = $artType;
        $this->submitDate = $submitDate;
        $this->status = $status;
        $this->response = $response;
    }

    public function create()
    {
        $sql = "INSERT INTO questionnaire (customerID, artStyle, budget, artType) VALUES ( :customerID, :artStyle, :budget, :artType)";
        $stmt = Database::getInstance()->getConnection()->prepare($sql);
        $stmt->bindParam(':customerID', $this->customerID);
        $stmt->bindParam(':artStyle', $this->artStyle);
        $stmt->bindParam(':budget', $this->budget);
        $stmt->bindParam(':artType', $this->artType);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }


    public static function getQuestionnaireById($id)
    {
        $sql = "SELECT * FROM questionnaire WHERE questionnaireID = :questionnaireID";
        $stmt = Database::getInstance()->getConnection()->prepare($sql);
        $stmt->bindParam(':questionnaireID', $id);
        $stmt->execute();
        $questionnaire = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($questionnaire) {

            return new self(
                $questionnaire['customerID'],
                $questionnaire['artStyle'],
                $questionnaire['budget'],
                $questionnaire['artType'],
                $questionnaire['questionnaireID'],
                $questionnaire['response'],
                $questionnaire['status'],
                $questionnaire['submitDate']
            );
        } else {
            return false;
        }
    }
    public function response($response)
    {
        $this->response = $response;
        $sql = "UPDATE questionnaire SET response = :response, status = 'Accepted' WHERE questionnaireID = :questionnaireID";
        $stmt = Database::getInstance()->getConnection()->prepare($sql);
        $stmt->bindParam(':response', $this->response);
        $stmt->bindParam(':questionnaireID', $this->questionnaireID);

        if ($stmt->execute()) {
            $this->status = 'Accepted';
            return true;
        } else {
            return false;
        }
    }

    public static function getAllQuestionnaires()
    {
        $sql = "SELECT * FROM questionnaire";
        $stmt = Database::getInstance()->getConnection()->prepare($sql);
        $stmt->execute();
        $questionnaires = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if ($questionnaires) {
            return array_map(function ($questionnaire) {
                return new self(
                    $questionnaire['customerID'],
                    $questionnaire['artStyle'],
                    $questionnaire['budget'],
                    $questionnaire['artType'],
                    $questionnaire['questionnaireID'],
                    $questionnaire['response'],
                    $questionnaire['status'],
                    $questionnaire['submitDate']
                );
            }, $questionnaires);
        } else {
            return false;
        }
    }

    public function getQuestionnaireID()
    {
        return $this->questionnaireID;
    }
    public function getCustomerID()
    {
        return $this->customerID;
    }
    public function getArtStyle()
    {
        return $this->artStyle;
    }
    public function getBudget()
    {
        return $this->budget;
    }
    public function getArtType()
    {
        return $this->artType;
    }
    public function getSubmitDate()
    {
        return $this->submitDate;
    }
    public function getStatus()
    {
        return $this->status;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function updateStatus($status)
    {
        $this->status = $status;
        $sql = "UPDATE questionnaire SET status = :status WHERE questionnaireID = :questionnaireID";
        $stmt = Database::getInstance()->getConnection()->prepare($sql);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':questionnaireID', $this->questionnaireID);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
