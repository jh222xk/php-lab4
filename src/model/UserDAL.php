<?php

namespace model;

class UserDAL {
  private static $tableName = "users";

  private static $username = "username";

  private static $password = "password";

  private static $primaryKey = "pk";

  private $pdo;

  public function __construct(\PDO $pdo) {
    $this->pdo = $pdo;
    //$this->createTable();
  }

  public function createTable() {
    $sql = "CREATE TABLE `" . self::$tableName . "`
            (
              `" . self::$primaryKey . "` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `" . self::$username ."` VARCHAR(255),
              `" . self::$password ."` VARCHAR(255)
            )";

    if ($this->pdo->exec($sql) === false) {
      throw new \Exception("'$sql' failed " . $this->pdo->error);
    }
  }

  public function insertUser($username, $password) {

    $sql = "INSERT INTO " . self::$tableName . "
          (
            " . self::$username .",
            " . self::$password ."
          )
          VALUES(?, ?)";
    
    $params = array($username, $password);

    $query = $this->pdo->prepare($sql);
    $query->execute($params);
  }

  public function getUser($username) {
    // $sql = "SELECT " . self::$username . ", " . self::$password . " FROM " . self::$tableName . "
    // WHERE " . self::$username .  " = ? AND " . self::$password .  " = ?;";

    $sql = "SELECT " . self::$username . ", " . self::$password . " FROM " . self::$tableName . "
    WHERE " . self::$username . " = ?;";

    $params = array($username);

    $query = $this->pdo->prepare($sql);
    $query->execute($params);

    $result = $query->fetch();

    // var_dump($result);

    if ($result) {
      return array(
        "username" => $result[self::$username],
        "password" => $result[self::$password]
      );
    }

    return null;

  }

  public function getAllUsers() {
    $sql = "SELECT " . self::$username . ", " . self::$password . " FROM " . self::$tableName . ";";

    $query = $this->pdo->prepare($sql);
    $query->execute();

    $result = $query->fetchAll(\PDO::FETCH_ASSOC);

    if ($result) {
      return new \model\User($result[self::$username], $result[self::$password]);
    }

    return null;

  }
}