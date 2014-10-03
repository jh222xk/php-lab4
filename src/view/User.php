<?php

namespace view;

require_once("src/view/CookieJar.php");

class User {
  /**
   * @var Usermodel
   */
  private $model;

  private $message;

  private $rememberMeUsernameCookie = "User::Username";

  private $rememberMePasswordCookie = "User::Password";

  private $usernameFormCookie = "User::Form::Username";

  private $username;

  private $password;

  /**
   * Secret signing key, keep secret. Maybe as an env var instead.
   * @var String
   */
  private $key = '2vtH6v#tbv$JOy4PxO!ISmWdBBtL2tjBNh0GoIwJa6ePtfhu9X5OD!NIY&*0';
  
  function __construct(\model\User $model) {
    $this->model = $model;
    $this->message = new \view\CookieJar();
  }

  public function getData() {
    $username = $_POST["username"];
    $password = $_POST["password"];

    return array(
      "username" => $username,
      "password" => $password
    );
  }

  /**
   * Checks if the user wants to register.
   * @return Boolean
   */
  public function userWantsToRegister() {
    return isset($_GET["register"]);
  }

  /**
   * Checks if the user has posted login.
   * @return Boolean
   */
  public function userWantsToLogin() {
    return isset($_POST["login"]);
  }

  /**
   * Checks if the user has posted remember me.
   * @return Boolean
   */
  public function userWantsToBeRemembered() {
    return isset($_POST["remember"]);
  }

  /**
   * Checks if the user wants to logout.
   * @return Boolean
   */
  public function userWantsToLogout() {
    return isset($_GET["logout"]);
  }

  public function getUsernameFormCookie() {
    if (isset($_COOKIE[$this->usernameFormCookie])) {
      return $_COOKIE[$this->usernameFormCookie];
    }
    else {
      return "";
    }
  }

  /**
   * Just a check to see if the username and password is equal
   * to the models data.
   * @return Boolean
   */
  public function userCredentialsIsValid() {
    $this->username = $_POST['username'];
    $this->password = $_POST['password'];

    if (empty($this->username)) {
      $this->message->save("Användarnamn saknas!");
      return false;
    }
    elseif(empty($this->password)) {
      $this->message->save("Lösenord saknas!");
      return false;
    }

    return true;
  }

  /**
   * Woho, a show login page for the users… I guess.
   * @return String
   */
  public function showLogin($back = false, $usernameFromReg = null, $message = null) {
    if ($this->userWantsToLogin()) {
      setcookie($this->usernameFormCookie, $_POST["username"], 0);
    }
    else {
      setcookie($this->usernameFormCookie, "", time() -1);
    }

    $username = $this->getUsernameFormCookie();

    if ($back) {
      $ret = "<a href='?register'>Tillbaka</a>";
      $username = $usernameFromReg;
      $storedMsg = $message;
    }
    else {
      $storedMsg = "";
      $ret = "<a href='?register'>Registrera ny användare</a>";
    }

    if ($this->userWantsToLogin()) {
      header('Location: ' . $_SERVER['PHP_SELF']);
    }
    elseif ($this->loginThroughCookies()) {
      $this->message->save("Inloggning genom kakor!");
      header('Location: ' . $_SERVER['PHP_SELF']);
    }
    elseif($this->cookieExist() && $this->loginThroughCookies() == false) {
      $this->message->save("Felaktig information i kakan!");
      header('Location: ' . $_SERVER['PHP_SELF']);
    }
    else {
      $storedMsg .= $this->message->load();
    }

    $ret .= "
      <h2>Ej inloggad</h2>
      <p>" . $storedMsg . "</p>
      <form action='.' method='post'>
        <fieldset>
          <legend>Login - Skriv in användarnamn och lösenord</legend>
          <label>Användarnamn: </label>
          <input type='text' size='20' name='username' value='$username' />
          <label>Lösenord: </label>
          <input type='password' size='20' name='password' value='' />
          <label>Håll mig inloggad: </label>
          <input type='checkbox' name='remember' />
          <input type='submit' value='Logga in' name='login' />
        </fieldset>
      </form>
    ";

    return $ret;
  }

  /**
   * Yep, it's true. It will return a logout page.
   * @return String
   */
  public function showLogout() {
    $user = $this->model->getUsersession();
    $user = $user["user"];
    $ret = "
      <h2>$user är inloggad</h2>

      <p><a href='?logout'>Logga ut</a></p>
    ";
    if ($this->userWantsToLogout()) {
      $this->message->save("Du har nu loggat ut!");
      header('Location: ' . $_SERVER['PHP_SELF']);
    }
    else {
      $ret .= $this->message->load();
    }

    setcookie($this->usernameFormCookie, "", time() -1);

    return $ret;
  }

  /**
   * Get the clients user agent and ip.
   * The user agent is'nt enough for session hijacking
   * since spoofing a user agent is easy enough.
   * @return String
   */
  public function getClientIdentifier() {
    return $_SERVER["REMOTE_ADDR"] . $_SERVER["HTTP_USER_AGENT"];
  }


  /**
   * Check if a remember me cookie exist.
   * @return Boolean
   */
  public function cookieExist() {
    return isset($_COOKIE[$this->rememberMeUsernameCookie])
      && isset($_COOKIE[$this->rememberMePasswordCookie]);
  }

  /**
   * Set the remember me cookies.
   */ 
  public function setCookies() {
    $user = $this->model->getUsersession();

    $time = time()+60; // 1 minute.

    $userData = array('username' => $user["user"], 'time' => $time);
    $passData = array('password' => $user["pass"], 'time' => $time);

    // Use a hash_hmac with encoded json and sha256 as hashing algorithm.
    $userHmac = hash_hmac('sha256', json_encode($userData), $this->key);
    $passHmac = hash_hmac('sha256', json_encode($passData), $this->key);

    $userData['hmac'] = $userHmac;
    $passData['hmac'] = $passHmac;

    setcookie($this->rememberMeUsernameCookie, base64_encode(json_encode($userData)), $time);
    setcookie($this->rememberMePasswordCookie, base64_encode(json_encode($passData)), $time);
  }

  /**
   * Kill the remember me cookies.
   */ 
  public function killCookies() {
    setcookie($this->rememberMeUsernameCookie, "", time() -1);
    setcookie($this->rememberMePasswordCookie, "", time() -1);
  }

  /**
   * Get the remember me cookies.
   * @return Array
   */
  public function getCookies() {
    $time = time()+60; // 1 minute.

    // Decode the cookie.
    $userCookie = json_decode(base64_decode($_COOKIE[$this->rememberMeUsernameCookie]));
    $passCookie = json_decode(base64_decode($_COOKIE[$this->rememberMePasswordCookie]));

    // Store the hmac for comparison.
    $userCookieHmac = $userCookie->hmac;
    $passCookieHmac = $passCookie->hmac;

    // Remove the hmac from the cookie data.
    unset($userCookie->hmac);
    unset($passCookie->hmac);

    // Calculate hmac for data, should be the same as the stored one.
    $userCalculatedHmac = hash_hmac('sha256', json_encode($userCookie), $this->key);
    $passCalculatedHmac = hash_hmac('sha256', json_encode($passCookie), $this->key);

    // Check if the hmac's is fine.
    if ($userCookieHmac === $userCalculatedHmac && $passCookieHmac === $passCalculatedHmac) {
      return array('user' => $userCookie, 'pass' => $passCookie);
    }
    else {
      return false;
    }
  }

  /**
   * Checks if the remember me cookies has expired.
   * @return Boolean
   */
  public function cookieExpired() {
    $cookies = $this->getCookies();

    if ($cookies['user']->time < time() && $cookies['pass']->time < time()) {
      return true;
    }
    else {
      return false;
    }
  }

  /**
   * Login through the remember me cookies.
   * @return Boolean
   */
  public function loginThroughCookies() {

    if (isset($_COOKIE[$this->rememberMeUsernameCookie])
      && isset($_COOKIE[$this->rememberMePasswordCookie])
      && $this->cookieExpired() == false) {

      // Get the cookies.
      $cookies = $this->getCookies();

      // Login the user.
      if ($this->model->login($this->getClientIdentifier(), $cookies["user"]->username,
          $cookies["pass"]->password, false)) {
        return true;
      }
    }
    $this->killCookies();
    return false;
  }
}