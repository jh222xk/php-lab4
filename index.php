<?php

require_once("src/view/HTMLPage.php");
require_once("src/controller/User.php");

session_start();

$controller = new \controller\User();
$htmlBody = $controller->showPage();

$view = new \view\HTMLPage();

echo $view->echoHTML("Logga in", $htmlBody);

