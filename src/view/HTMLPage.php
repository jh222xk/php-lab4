<?php

namespace view;

require_once("src/view/CurrentDateTime.php");

class HTMLPage {
  /**
   * @param String $title
   * @param String $body
   * @return String
   */
  public function echoHTML($title, $body) {
    $currentTime = new \view\CurrentDateTime();
    $ret = "
      <!DOCTYPE html>
      <html>
      <head>
        <meta charset='utf-8'>
        <title>$title</title>
      </head>
      <body>
        <h1>Laborationskod jh222xk</h1>
        $body
        <p>". $currentTime->getCurrentDateTime() . "</p>
      </body>
      </html>
    ";
    return $ret;
  }
}