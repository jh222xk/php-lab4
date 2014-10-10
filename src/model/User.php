<?php

namespace model;

class User {

  /**
   * The username session var
   * @var String
   */
  private $usernameSession = "User::username";

  /**
   * The password session var
   * @var String
   */
  private $passwordSession = "User::password";

  /**
   * The uniqueID session var
   * @var String
   */
  private $uniqueIDSession = "User::uniqueID";

  /**
   * The clientIdentifier session var, possible an ip-adress
   * @var String
   */
  private $clientIdentifier = "User::clientIdentifier";

  /**
   * The hashed password.
   * @var String Example $1$HszYM0BY$UzPU/c2H41K9IEv1cMvQx1
   */
  private $crypted;

  /**
   * The users unique ID
   * @var Integer Example 123
   */ 
  private $userID;

  /**
   * Username of the user
   * @var String Example Edith
   */
  private $username;

  /**
   * Password for the user
   * @var String Example 3ew0gX8V7hlS
   */
  private $password;

  private $store;

  /**
   * @param Integer $userID
   * @param String $username
   * @param String $password
   */
  // public function __construct($username, $password) {
  public function __construct(\model\UserStore $store) {
    // $this->userID = $userID;
    // $this->username = $username;
    // $this->password = $password;
    $this->store = $store;
  }

  /**
   * Gets the users id
   * @return Integer
   */
  public function getUserID() {
    return $this->userID;
  }

  /**
   * Gets the users name
   * @return String
   */
  public function getUsername() {
    return $this->username;
  }

  public function setUsername($username) {
    if (strlen($username) < 3) {
      throw new \InvalidArgumentException("The username needs to be 3 characters of length.");
    }
    $this->username = $username;
  }

  /**
   * Gets the users password
   * @return String
   */
  public function getPassword() {
    return $this->password;
  }

  public function setPassword($password) {
    if (strlen($password) < 6) {
      throw new \InvalidArgumentException("The password needs to be 6 characters of length");
    }
    $this->password = $password;
  }

  /**
   * Hashes the password.
   */ 
  public function hashPassword($password) {
    $this->crypted = crypt($password);
  }

  /**
   * Returns the hashed password.
   */
  public function getHashedPassword() {
    return $this->crypted;
  }

  /**
   * Check if hashed password matches.
   * @param String $password
   * @param String $hashedPassword
   * @return Boolean
   */
  function checkHashedPassword($password, $hashedPassword) {
    return crypt($password, $hashedPassword) === $hashedPassword;
  }

  /**
   * Checks if the user is logged in according to the session.
   * @return Boolean
   */ 
  public function userIsLoggedIn($clientIdentifier) {
    if(isset($_SESSION[$this->usernameSession])
      && $_SESSION[$this->clientIdentifier] === base64_encode($clientIdentifier)) {
        return true;
    }
    return false;
  }

  /**
   * Signs the user in, i.e. sets the session vars.
   */
  public function login($clientIdentifier, $username, $pass, $crypt = true) {
    $storedUser = $this->store->getUser($username);

    // var_dump($crypt);
    // die;

    if ($storedUser !== null) {
      if ($crypt == true) {
        $this->hashPassword($pass);

        $password = $this->crypted;
      }

      if ($this->checkHashedPassword($pass, $storedUser["password"])) {
        $this->setUsername($storedUser["username"]);
        $this->setPassword($pass);
        $this->setSessions($clientIdentifier);
        return true;
      }
    }
  }

  public function setSessions($clientIdentifier) {
    $_SESSION[$this->usernameSession] = $this->username;
    $_SESSION[$this->passwordSession] = $this->password;
    $_SESSION[$this->clientIdentifier] = base64_encode($clientIdentifier);
  }

  public function getUserSession() {
    return array(
      "user" => $_SESSION[$this->usernameSession],
      "pass" => $_SESSION[$this->passwordSession]
    );
  }

  /**
   * Signs out the user, i.e. kills the session.
   */
  public function logout() {
    unset($_SESSION[$this->usernameSession]);
    unset($_SESSION[$this->passwordSession]);
  }
}