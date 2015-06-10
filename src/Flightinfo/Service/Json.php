<?php
/**
 * Created by PhpStorm.
 * User: drupalviking
 * Date: 06/05/15
 * Time: 10:40
 */
namespace FlightInfo\Service;

use PDOException;
use FlightInfo\Lib\DataSourceAwareInterface;
use FlightInfo\Service\DatabaseService;

class Json implements DataSourceAwareInterface {

  use DatabaseService;

  const NAME = 'json';

  /**
   * @var \PDO
   */
  private $pdo;

  /**
   * Get flights based on
   *
   * @param int $id event ID
   * @return \stdClass
   * @throws Exception
   */
  public function fetchByParameters($arrivals = false, $date = null, $airports = null, $carriers = null) {
    try {
      $departureSQL = "SELECT f.flightnumber, al.name_icelandic, f.date, f.from as airport_from_id, f.to as airport_to_id, a.name as airport_from, a.airport_code as airportcode_from, a2.name as airport_to, a2.airport_code as airportcode_to, f.last_modified, u.name,
          f.scheduled_departure, f.estimated_departure, f.actual_departure, f.status_departure
          FROM flight_info.Flight f
          INNER JOIN flight_info.Airport a
          ON f.from = a.id
          INNER JOIN flight_info.Airport a2
          ON f.to = a2.id
          INNER JOIN flight_info.User u
          on f.last_modified_by = u.id
          INNER JOIN flight_info.Airline al
          ON f.airline = al.id
          WHERE f.date = :dt";

      $arrivalSQL = "SELECT f.flightnumber, al.name_icelandic, f.date, f.from as airport_from_id, f.to as airport_to_id, a.name as airport_from, a.airport_code as airportcode_from, a2.name as airport_to, a2.airport_code as airportcode_to, f.last_modified, u.name,
          f.scheduled_arrival, f.estimated_arrival, f.actual_arrival, f.status_arrival
          FROM flight_info.Flight f
          INNER JOIN flight_info.Airport a
          ON f.from = a.id
          INNER JOIN flight_info.Airport a2
          ON f.to = a2.id
          INNER JOIN flight_info.User u
          on f.last_modified_by = u.id
          INNER JOIN flight_info.Airline al
          ON f.airline = al.id
          WHERE f.date = :dt";

      $date = (!$date) ? strtotime(strftime("%Y-%m-%d")) : strtotime(strftime($date));

      $airportWhereString = (!$arrivals) ? "f.from IN (" : "f.to IN (";

      if(!$arrivals){
        if($airports && $carriers){
          $airportIds = $this->getIdsFromAirportCodes($airports);
          $carrierIds = $this->getIdsFromCarrierCodes($carriers);
          $statement = $this->pdo->prepare(
            $departureSQL .
            " AND " . $airportWhereString . implode(',',array_map(function($i){ return "'{$i}'"; },$airportIds) ) . ")
            " . " AND f.airline IN (" . implode(',',array_map(function($i){ return "'{$i}'"; },$carrierIds) ) . ")"
          );
        }
        else if($airports && !$carriers){
          $airportIds = $this->getIdsFromAirportCodes($airports);
          $statement = $this->pdo->prepare(
            $departureSQL .
            " AND " . $airportWhereString . implode(',',array_map(function($i){ return "'{$i}'"; },$airportIds) ) . ")"
          );
        }
        else if(!$airports && $carriers){
          $carrierIds = $this->getIdsFromCarrierCodes($carriers);
          $statement = $this->pdo->prepare(
            $departureSQL .

            " AND f.airline IN (" . implode(',',array_map(function($i){ return "'{$i}'"; },$carrierIds) ) . ")"
          );
        }
        else{
          $statement = $this->pdo->prepare(
            $departureSQL
          );
        }
      }
      else{
        if($airports && $carriers){
          $airportIds = $this->getIdsFromAirportCodes($airports);
          $carrierIds = $this->getIdsFromCarrierCodes($carriers);
          $statement = $this->pdo->prepare(
            $arrivalSQL .
            " AND " . $airportWhereString . implode(',',array_map(function($i){ return "'{$i}'"; },$airportIds) ) . ")
            " . " AND f.airline IN (" . implode(',',array_map(function($i){ return "'{$i}'"; },$carrierIds) ) . ")"
          );
        }
        else if($airports && !$carriers){
          $airportIds = $this->getIdsFromAirportCodes($airports);
          $statement = $this->pdo->prepare(
            $arrivalSQL .
            " AND " . $airportWhereString . implode(',',array_map(function($i){ return "'{$i}'"; },$airportIds) ) . ")"
          );
        }
        else if(!$airports && $carriers){
          $carrierIds = $this->getIdsFromCarrierCodes($carriers);
          $statement = $this->pdo->prepare(
            $arrivalSQL .

            " AND f.airline IN (" . implode(',',array_map(function($i){ return "'{$i}'"; },$carrierIds) ) . ")"
          );
        }
        else{
          $statement = $this->pdo->prepare(
            $arrivalSQL
          );
        }
      }

      $statement->execute(array(
        'dt' => $date
      ));

      $flight = $statement->fetchAll();

      if (!$flight) {
        return array("results" => array());
      }

      return array("results" => $flight );
    } catch (PDOException $e) {
      throw new Exception("Can't get flight items.", 0, $e);
    }
  }

  /**
   * Fetches all Airports in the database
   * @return array
   * @throws \FlightInfo\Service\Exception
   */
  public function getAirports(){
    try{
      $statement = $this->pdo->prepare("
        SELECT * FROM Airport
        ORDER BY name;
      ");

      $statement->execute();
      return $statement->fetchAll();
    }catch (PDOException $e) {
      throw new Exception("Can't get Airports", 0, $e);
    }
  }

  /**
   * Fetches all Airlines in the database
   *
   * @return array
   * @throws \FlightInfo\Service\Exception
   */
  public function getAirlines(){
    try{
      $statement = $this->pdo->prepare("
        SELECT * FROM Airline
        ORDER BY name_icelandic;
      ");

      $statement->execute();
      return $statement->fetchAll();
    }catch (PDOException $e) {
      throw new Exception("Can't get Airlines", 0, $e);
    }
  }

  public function getIdsFromAirportCodes($airports){
    try{
      $returnArr = null;
      $statement = $this->pdo->prepare("
        SELECT id FROM flight_info.Airport
        WHERE airport_code IN (".
          implode(',',array_map(function($i){ return "'{$i}'"; },$airports) ).
        ");
      ");

      $statement->execute();

      $airportIds = $statement->fetchAll();

      if (!$airportIds) {
        return null;
      }

      foreach($airportIds as $id){
        $returnArr[] = (int)$id->id;
      }

      return $returnArr;
    }catch (PDOException $e) {
      throw new Exception("Can't get flight items.", 0, $e);
    }
  }

  public function getIdsFromCarrierCodes($carrier){
    try{
      $returnArr = null;
      $statement = $this->pdo->prepare("
        SELECT id FROM flight_info.Airline
        WHERE carrier_code IN (".
        implode(',',array_map(function($i){ return "'{$i}'"; },$carrier) ).
        ");
      ");

      $statement->execute();

      $airportIds = $statement->fetchAll();

      if (!$airportIds) {
        return null;
      }

      foreach($airportIds as $id){
        $returnArr[] = (int)$id->id;
      }

      return $returnArr;
    }catch (PDOException $e) {
      throw new Exception("Can't get flight items.", 0, $e);
    }
  }

  public function setDataSource(\PDO $pdo) {
    $this->pdo = $pdo;
  }
}