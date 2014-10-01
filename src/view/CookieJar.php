<?php

namespace view;

class CookieJar {
  private $cookieName = "CookieJar";
  private $cookieNameErrUser = "CookieJar::Errors::Username";
  private $cookieNameErrPass = "CookieJar::Errors::Password";
  private $cookieNameErrPassConfirm = "CookieJar::Errors::PasswordConfirm";

  /**
   * Sets a cookie
   * @param String $string
   */
  public function save($string) {
    setcookie($this->cookieName, $string, time()+60);
  }

  public function saveErr(array $errors) {
    // var_dump($errors);
    // die();
    $time = 0;
    setcookie($this->cookieNameErrUser, json_encode($errors["username"]), $time);
    setcookie($this->cookieNameErrPass, json_encode($errors["password"]), $time);
    setcookie($this->cookieNameErrPassConfirm, json_encode($errors["password_confirmation"]), $time);
  }

  /**
   * Loads a cookie
   * @return
   */ 
  public function load() {
    $ret = isset($_COOKIE[$this->cookieName]) ? $_COOKIE[$this->cookieName] : "";

    setcookie($this->cookieName, "", time() -1);

    return $ret;
  }

  public function loadErr() {
    if (isset($_COOKIE[$this->cookieNameErrUser]) == false) {
      return array();
    }
    if (isset($_COOKIE[$this->cookieNameErrPassConfirm])) {
      $passConfirmCookie = json_decode($_COOKIE[$this->cookieNameErrPassConfirm]);
      setcookie($this->cookieNameErrPassConfirm, "", time() -1);
    }
    else {
      $passConfirmCookie = null;
    }

    $userCookie = json_decode($_COOKIE[$this->cookieNameErrUser]);
    $passCookie = json_decode($_COOKIE[$this->cookieNameErrPass]);

    setcookie($this->cookieNameErrUser, "", time() -1);
    setcookie($this->cookieNameErrPass, "", time() -1);

    return array(
      'username' => $userCookie,
      'password' => $passCookie,
      'password_confirmation' => $passConfirmCookie
    );
  }
}





