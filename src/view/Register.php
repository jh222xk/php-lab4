<?php

namespace view;

require_once("src/view/CookieJar.php");

class Register {
  /**
   * @var Usermodel
   */
  private $model;

  private $message;

  private $username;

  private $password;

  private $errors = array();

  private $usernameFormCookie = "User::Form::Username";
  
  function __construct() {
    // $this->model = $model;
    $this->message = new \view\CookieJar();
  }

  public function getUsernameFormCookie() {
    if (isset($_COOKIE[$this->usernameFormCookie])) {
      return $_COOKIE[$this->usernameFormCookie];
    }
    else {
      return "";
    }
  }

  public function setRegCookie() {
    setcookie($this->usernameFormCookie, $_POST["username"], time()+60);
  }

  public function showRegister() {
    if ($this->triesToRegister()) {
      $this->setRegCookie();
    }
    else {
      setcookie($this->usernameFormCookie, "", time() -1);
    }

    $username = $this->getUsernameFormCookie();
    $ret = "
      <a href='" . $_SERVER['PHP_SELF'] . "'>Tillbaka</a>
      <h2>Ej inloggad, Registrerar användare</h2>
      <form action='?register' method='post'>
        <fieldset>
          <legend>Registrera ny användare - Skriv in användarnamn och lösenord</legend>
          <label>Användarnamn: </label>
          <div>
            <input type='text' size='20' name='username' value='" . $this->sanitize($username) . "' />
          </div>
          <label>Lösenord: </label>
          <div>
            <input type='password' size='20' name='password' value='' />
          </div>
          <label>Repetera Lösenord: </label>
          <div>
            <input type='password' size='20' name='password_confirm' value='' />
          </div>
          <input type='submit' value='Registrera' name='register' />
        </fieldset>
      </form>
    ";

    if ($this->triesToRegister()) {
      // $errors = $this->errors;
      // foreach ($errors as $error) {
      //   $ret .= "<p>" . $error . "</p>";
      // }
      header('Location: ' . $_SERVER['REQUEST_URI']);
    }
    else {
      // $ret .= $this->message->loadErr();
      $ret .= $this->message->load();
      $errors = $this->message->loadErr();
      foreach ($errors as $error) {
        $ret .= "<p>" . $error . "</p>";
      }
    }

    return $ret;
  }

  public function triesToRegister() {
    return isset($_POST["register"]);
  }

  public function getInput() {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $password_confirm = $_POST["password_confirm"];

    // if (strlen($username) < 3) {
    //   $this->errors[] = "Användarnamnet har för få tecken. Minst 3 tecken.";
    //   // $this->message->save("Användarnamnet har för få tecken. Minst 3 tecken");
    // }
    // if (strlen($password) < 6) {
    //   $this->errors[] = "Lösenordet har för få tecken. Minst 6 tecken.";
    //   // $this->message->save("Lösenordet har för få tecken. Minst 6 tecken");
    // }
    // if ($password !== $password_confirm) {
    //   $this->errors[] = "Lösenorden matchar inte.";
    // }

    if ($username != $this->sanitize($username)) {
      return null;
    }


    return array(
      "username" => $username,
      "password" => $password,
      "password_confirmation" => $password_confirm
    );
  }

  private function sanitize($input) {
    $temp = trim($input);
    return filter_var($temp, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
  }
}











