<?php

namespace controller;

require_once("src/model/User.php");
require_once("src/view/User.php");
require_once("src/view/Register.php");
require_once("src/model/UserDAL.php");
require_once("src/model/UserStore.php");

class User {

  /**
   * @var Userview
   */
  private $view;

  /**
   * @var Usermodel
   */
  private $model;

  private $message;

  private $registerView;

  private $store;
  
  public function __construct() {
    $this->store = new \model\UserStore();
    $this->model = new \model\User($this->store);
    $this->view = new \view\User($this->model);

    $this->registerView = new \view\Register();
    $this->message = new \view\CookieJar();
  }


  /**
   * Desides which page to be shown.
   * @return String
   */
  public function showPage() {
    // User logged in, giv'em the logout!
    var_dump($this->model->userIsLoggedIn($this->view->getClientIdentifier()));
    if ($this->model->userIsLoggedIn($this->view->getClientIdentifier())) {
      return $this->doLogout();
    }
    elseif ($this->model->userIsLoggedIn($this->view->getClientIdentifier()) == false
      && $this->view->userWantsToRegister()) {
      return $this->doRegister();
    }
    else {
      return $this->doLogin();
    }
  }

  /**
   * 
   */ 
  public function doLogin() {
    // var_dump($_SESSION);
    // die();
    // Submitted the form?
    if ($this->view->userWantsToLogin()) {
      // Valid user credentials?
      if ($this->view->userCredentialsIsValid()) {
        $data = $this->view->getData();

        // var_dump($data["username"]);
        // var_dump($this->store->getUser($data["username"])["username"]);
        // die;
        $storedUser = $this->store->getUser($data["username"]);
        // Login user.
        if($this->model->login($this->view->getClientIdentifier(), $data["username"],
          $data["password"])) {
          $this->message->save("Inloggning lyckades");
        }
        else {
          $this->message->save("Felaktigt användarnamn och/eller lösenord");
        }

        // If the user want to be remembered set some cookies for that.
        if ($this->view->userWantsToBeRemembered()) {
          $this->view->setCookies();
          $this->message->save("Inloggning lyckades och vi kommer ihåg dig!");
        }
      }
    }
    // Render a view.
    return $this->view->showLogin();
  }

  public function doLogout() {
    // Pressed logout?
    if ($this->view->userWantsToLogout()) {
      // If the user has some cookies set, kill'em.
      if ($this->view->cookieExist()) {
        $this->view->killCookies();
      }
      // Logout the user.
      $this->model->logout();
    }

    // Render a view.
    return $this->view->showLogout();
  }

  public function doRegister() {
    if ($this->registerView->triesToRegister()) {
        $data = $this->registerView->getInput();
        if ($data !== null) {
          if ($this->containsErrors($data) == false) {
            $this->model->hashPassword($data["password"]);
            $pass = $this->model->getHashedPassword();
            try {
              $this->store->addUser($data["username"], $pass);
              return $this->view->showLogin(true, $data["username"], "Yay, grattis du är nu medlem!");
            }
            catch (\Exception $e) {
              $this->message->save($e->getMessage());
            }
          }
        }
        else {
          $this->message->save("Användarnamnet innehåller ogiltiga tecken!");
        }
    }
    return $this->registerView->showRegister();
  }

  public function containsErrors($data) {
    
    $errors = array();
    if ($data["password"] !== $data["password_confirmation"]) {
      $errors["password_confirmation"] = "Lösenorden matchar inte.";
    }
    try {
      $this->model->setUsername($data["username"]);
    }
    catch(\InvalidArgumentException $e) {
      $errors["username"] = $e->getMessage();
    }
    try {
      $this->model->setPassword($data["password"]);
    }
    catch (\InvalidArgumentException $e) {
      $errors["password"] = $e->getMessage();
    }

    if (!empty($errors)) {
      $this->message->saveErr($errors);
      return true;
    }
  }

}