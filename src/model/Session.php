<?php

namespace model;

class Session {

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
   * The clientIdentifier session var, possible an ip-adress
   * @var String
   */
  private $clientIdentifier = "User::clientIdentifier";

  /**
   * Checks if the user is logged in according to the session.
   * @return Boolean
   */ 
  public function userIsLoggedIn($clientIdentifier) {
    return isset($_SESSION[$this->usernameSession]);
  }

  /**
   * Signs the user in, i.e. sets the session vars.
   */
  public function login($clientIdentifier, $username, $cryptedPassword) {
    $user = $this->store->getUser($username);
    
    if ($user !== null) {
      $this->hashPassword($cryptedPassword);
      $password = $this->crypted;

      if ($this->checkHashedPassword($cryptedPassword, $user["password"])) {
        $this->setUsername($username);
        $this->setPassword($password);
        $this->setSessions($clientIdentifier);
        return true;
      }
    }
  }

  public function setSessions($clientIdentifier) {
    $_SESSION[$this->usernameSession] = $this->username;
    $_SESSION[$this->passwordSession] = $this->password;
    $_SESSION[$this->clientIdentifier] = $clientIdentifier;
  }

  /**
   * Signs out the user, i.e. kills the session.
   */
  public function logout() {
    unset($_SESSION[$this->usernameSession]);
    unset($_SESSION[$this->passwordSession]);
    unset($_SESSION[$this->clientIdentifier]);
  }
}