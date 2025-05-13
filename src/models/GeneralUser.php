<?php

namespace App\models;

abstract class GeneralUser
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
    protected $registerDate;

    abstract public function login(string $email, string $password);
    abstract public function getUserById($id);
    abstract public function updateProfile($data);
}
