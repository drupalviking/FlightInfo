<?php
/**
 * Created by PhpStorm.
 * User: drupalviking
 * Date: 08/05/15
 * Time: 10:01
 */
namespace FlightInfo\Service;

date_default_timezone_set('UTC');
setlocale(LC_ALL, 'is_IS');

use PDOException;
use FlightInfo\Lib\DataSourceAwareInterface;
use FlightInfo\Service\DatabaseService;

define("SOURCE_URL", "http://flights.flugfelag.is/origo-portlets/rmdomestic/domesticservices/");

class XMLStream implements DataSourceAwareInterface {

  use DatabaseService;

  /**
   * @var \PDO
   */
  private $pdo;

  /**
   * @param bool $arrivals
   * @param string $airport_code
   * @param string $locale
   * @return stdClass $obj
   */
  public function getStream($arrivals=false, $airport_code, $locale = 'is_IS'){
    $concatstring = ($arrivals) ? "arrivals" : "departures";
    $concatstring2 = ($arrivals) ? "Arrival" : "Departure";
    $url = SOURCE_URL . $concatstring . ".xml?RequestType=" . $concatstring . "&" . $concatstring2 . "=" . strtoupper($airport_code) . "&GapBefore=2&GapAfter=14&locale=" . $locale;

    $xml = simplexml_load_file($url);
    $json = json_encode($xml);
    $obj = json_decode($json);
    return $obj;
  }

  public function setDataSource(\PDO $pdo) {
    $this->pdo = $pdo;
  }
}