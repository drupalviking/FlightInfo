<?php
/**
 * Created by PhpStorm.
 * User: drupalviking
 * Date: 07/05/15
 * Time: 13:45
 */
namespace FlightInfo\Service;

use PDOException;
use FlightInfo\Lib\DataSourceAwareInterface;
use FlightInfo\Service\DatabaseService;

class Flightnumber implements DataSourceAwareInterface {

  use DatabaseService;

  const NAME = 'flight';

  /**
   * @var \PDO
   */
  private $pdo;

  public function getAirlineFromFlightNumber($flightnumber){
    $cc = $this->_getCarrierCodeFromFlightNumber($flightnumber);
    return $this->_findByCarrierCode($cc);
  }

  protected function _findByCarrierCode($carrier_code){

    try{
      $statement = $this->pdo->prepare("
            SELECT * FROM Airline
            WHERE carrier_code = :cc
        ");
      $statement->execute(array(
        'cc' => $carrier_code
      ));
      $airline = $statement->fetchObject();

      if (!$airline) {
        $statement = $this->pdo->prepare("
          SELECT * FROM Airline
          WHERE id = 1
      ");
        $statement->execute();
        $airline = $statement->fetchObject();
      }

      return $airline;
    } catch (PDOException $e) {
      throw new Exception("Can't get airline item. airline:[{$id}]", 0, $e);
    }
  }

  protected function _getCarrierCodeFromFlightNumber( $flight_number ){
    return preg_replace("/[^A-Z]+/", "", $flight_number);
  }

  public function setDataSource(\PDO $pdo){
    $this->pdo = $pdo;
  }
}
