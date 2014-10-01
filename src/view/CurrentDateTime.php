<?php

namespace view;

class CurrentDateTime {

  /**
   * An array of months in Swedish names.
   * @var Array Example Januari
   */
  private $month = array('Januari', 'Februari', 'Mars', 'April',
    'Maj', 'Juni', 'Juli', 'Augusti', 'September', 'Oktober',
    'November', 'December');

  /**
   * An array of days in Swedish names.
   * @var Array Example Söndag
   */
  private $day = array('Måndag', 'Tisdag', 'Onsdag',
    'Torsdag', 'Fredag', 'Lördag', 'Söndag');

  /**
   * Returns a strigng of a datetime with Swedish names for
   * days and months.
   * @return String
   */
  public function getCurrentDateTime() {
    $date = new \DateTime();

    $date->setTimezone(new \DateTimeZone('Europe/Stockholm'));

    // Awesome concatenating…
    $ret = $this->day[$date->format('N')-1] . ", den " .
      $date->format('j') . " " . $this->month[$date->format('n')-1] .
      " år " . $date->format('Y') . ". Klockan är " . "[" .
      $date->format('H:i:s') . "].";

    return $ret;
  }
}