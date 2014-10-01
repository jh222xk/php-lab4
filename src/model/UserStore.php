<?php

namespace model;

require_once("src/model/UserDAL.php");

class UserStore {

  private $users = array();

  private $userDAL;

  public function __construct() {
    $pdo = new \PDO("mysql:host=127.0.0.1;dbname=lab4", "appUser", "password");
    $this->userDAL = new \model\UserDAL($pdo);
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
  }

  public function addUser($user, $password) {
    if ($this->getUser($user)) {
      throw new \Exception("Användarnamnet [$user] är redan upptaget!");
    }

    return $this->userDAL->insertUser($user, $password);
  }

  public function getUser($username) {
    return $this->userDAL->getUser($username);
  }
}