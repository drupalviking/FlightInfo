<?php
/**
 * Created by PhpStorm.
 * User: drupalviking
 * Date: 06/05/15
 * Time: 10:40
 */
namespace FlightInfo\Service;

date_default_timezone_set('UTC');
setlocale(LC_ALL, 'is_IS');

use PDOException;
use FlightInfo\Lib\DataSourceAwareInterface;
use FlightInfo\Service\DatabaseService;
use FlightInfo\Service\Flightnumber;
use FlightInfo\Service\Airport;

class Flight implements DataSourceAwareInterface {

  use DatabaseService;

  const NAME = 'flight';

  /**
   * @var \PDO
   */
  private $pdo;

  /**
   * Get one flight entry.
   *
   * @param int $id event ID
   * @return \stdClass
   * @throws Exception
   */
  public function get( $id ){
    try{
      $statement = $this->pdo->prepare("
          SELECT f.id, f.flightnumber, al.name_icelandic, f.date, f.from as airport_from_id, f.to as airport_to_id, a.name as airport_from, a.airport_code as airportcode_from, a2.name as airport_to, a2.airport_code as airportcode_to, f.last_modified, u.name,
          f.scheduled_departure, f.estimated_departure, f.actual_departure, f.scheduled_arrival, f.estimated_arrival, f.actual_arrival, f.status_departure, f.status_arrival
          FROM flight_info.Flight f
          INNER JOIN flight_info.Airport a
          ON f.from = a.id
          INNER JOIN flight_info.Airport a2
          ON f.to = a2.id
          INNER JOIN flight_info.User u
          on f.last_modified_by = u.id
          INNER JOIN flight_info.Airline al
          ON f.airline = al.id
          WHERE f.id = :id
      ");
      $statement->execute(array(
        'id' => $id
      ));
      $flight = $statement->fetchObject();

      if( !$flight ){ return false; }

      return $flight;
    }catch (PDOException $e){
      throw new Exception("Can't get flight item. flight:[{$id}]",0,$e);
    }
  }

  /**
   * Get one flight entry.
   *
   * @param int $id event ID
   * @return \stdClass
   * @throws Exception
   */
  public function getByFlightnumberAndDate( $flightnumber, $date ){
    try{
      $statement = $this->pdo->prepare("
          SELECT f.id, f.flightnumber, al.name_icelandic, f.date, f.from as airport_from_id, f.to as airport_to_id, a.name as airport_from, a.airport_code as airportcode_from, a2.name as airport_to, a2.airport_code as airportcode_to, f.last_modified, u.name,
          f.scheduled_departure, f.estimated_departure, f.actual_departure, f.scheduled_arrival, f.estimated_arrival, f.actual_arrival, f.status_departure, f.status_arrival
          FROM flight_info.Flight f
          INNER JOIN flight_info.Airport a
          ON f.from = a.id
          INNER JOIN flight_info.Airport a2
          ON f.to = a2.id
          INNER JOIN flight_info.User u
          on f.last_modified_by = u.id
          INNER JOIN flight_info.Airline al
          ON f.airline = al.id
          WHERE f.flightnumber = :fn AND f.date = :dt
      ");
      $statement->execute(array(
        'fn' => $flightnumber,
        'dt' => $date,
      ));
      $flight = $statement->fetchObject();

      if( !$flight ){ return false; }

      return $flight;
    }catch (PDOException $e){
      throw new Exception("Can't get flight item. flight:[{$id}]",0,$e);
    }
  }

  /**
   * Get all flight entries.
   *
   * @param int $page page number
   * @param int $count number of items pr. page
   * @return \stdClass
   * @throws Exception
   */
  public function fetchAll($date){
    try{
      $statement = $this->pdo->prepare("
					SELECT f.id, f.flightnumber, al.name_icelandic, f.date, f.from as airport_from_id, f.to as airport_to_id, a.name as airport_from, a.airport_code as airportcode_from, a2.name as airport_to, a2.airport_code as airportcode_to, f.last_modified, u.name,
          f.scheduled_departure, f.estimated_departure, f.actual_departure, f.scheduled_arrival, f.estimated_arrival, f.actual_arrival, f.status_departure, f.status_arrival
          FROM flight_info.Flight f
          INNER JOIN flight_info.Airport a
          ON f.from = a.id
          INNER JOIN flight_info.Airport a2
          ON f.to = a2.id
          INNER JOIN flight_info.User u
          on f.last_modified_by = u.id
          INNER JOIN flight_info.Airline al
          ON f.airline = al.id
					WHERE f.`date` = :dt
					ORDER BY f.scheduled_departure
				");
      $statement->execute(array(
        'dt' => $date
      ));

      return $statement->fetchAll();
    }catch (PDOException $e){
      throw new Exception("Can't get next flight item.",0,$e);
    }
  }

  /**
   * Create flight entry.
   *
   * @param array $data
   * @return int ID
   * @throws Exception
   */
  public function create( array $data ){
    try{
      $data = $this->_sanitiseTimeData($data);
      $data['last_modified'] = time();
      $data['last_modified_by'] = 1;

      $insertString = $this->insertString('Flight',$data);
      $statement = $this->pdo->prepare($insertString);
      $statement->execute($data);
      $id = (int)$this->pdo->lastInsertId();
      $data['id'] = $id;
      return $id;
    }catch (PDOException $e){
      throw new Exception("Can't create flight entry",0,$e);
    }

  }

  /**
   * Create flight entry from stream
   *
   * @param array $data
   * @return int ID
   * @throws Exception
   */
  public function createFromStream( array $data ){
    try{
      $insertString = $this->insertString('Flight',$data);
      $statement = $this->pdo->prepare($insertString);
      $statement->execute($data);
      $id = (int)$this->pdo->lastInsertId();
      $data['id'] = $id;
      return $id;
    }catch (PDOException $e){
      throw new Exception("Can't create flight entry",0,$e);
    }

  }

  /**
   * Update one entry.
   *
   * @param $id news ID
   * @param array $data
   * @return int row count
   * @throws Exception
   * @todo created_date
   */
  public function update( $id, array $data ){
    try{
      $data = $this->_sanitiseTimeData($data);

      $data['last_modified'] = time();
      $data['last_modified_by'] = 1;

      $updateString = $this->updateString('Flight',$data, "id={$id}");
      $statement = $this->pdo->prepare($updateString);
      $statement->execute($data);
      $data['id'] = $id;
      return $id;
    }catch (PDOException $e){
      throw new Exception("Can't update flight entry",0,$e);
    }
  }

  /**
   * Update one entry from data stream
   *
   * @param $id news ID
   * @param array $data
   * @return int row count
   * @throws Exception
   * @todo created_date
   */
  public function updateFromStream( $id, array $data ){
    try{
      $data['last_modified'] = time();
      $data['last_modified_by'] = 1;

      $updateString = $this->updateString('Flight',$data, "id={$id}");
      $statement = $this->pdo->prepare($updateString);
      $statement->execute($data);
      $data['id'] = $id;
      return $id;
    }catch (PDOException $e){
      echo "<pre>"; print_r($e->getMessage());
      throw new Exception("Can't update flight entry",0,$e);
    }
  }

  /**
   * Delete one entry.
   *
   * @param $id news ID
   * @return int
   * @throws Exception
   */
  public function delete($id){
    if( ( $news = $this->get( $id ) ) != false ){
      try{
        $statement = $this->pdo->prepare('
                DELETE FROM `Flight`
                WHERE id = :id');
        $statement->execute(array(
          'id' => $id
        ));
        return $statement->rowCount();
      }catch (PDOException $e){

        throw new Exception("can't delete airport entry",0,$e);
      }
    }else{
      return 0;
    }
  }

  public function processStream(array $obj){
    foreach($obj[0]->destination as $flight_stream){
      if( isset($flight_stream->flights->flight)){
        $this->_processFlights($flight_stream->flights->flight, false);
      }
    }
    foreach($obj[1]->destination as $flight_stream){
      if( isset($flight_stream->flights->flight)){
        $this->_processFlights($flight_stream->flights->flight, true);
      }
    }
  }

  public function setDataSource(\PDO $pdo){
    $this->pdo = $pdo;
  }

  protected function _processFlights($flights, $arrivals = false){
    //The stream returns either an array of flights or just one object
    if(is_array($flights)){
      foreach($flights as $flight){
        $this->_processOneFlight($flight, $arrivals);
      }
    }
    else if(is_object($flights)){
      $this->_processOneFlight($flights, $arrivals);
    }
  }

  protected function _getSecondsFromTime( $time ) {
    $hours = substr($time, 0, 2);
    $mins = substr($time, 3, 2);
    return (int) ($hours * 60 * 60) + (int) ($mins * 60);
  }

  protected function _sanitiseTimeData($data){
    $date_at_midnight = strtotime($data['date']);

    $data['date'] = $date_at_midnight;
    $data['scheduled_departure'] = (int)$this->_getSecondsFromTime($data['scheduled_departure']) + $date_at_midnight;
    $data['estimated_departure'] = (strlen($data['estimated_departure'])>0)
      ? (int)$this->_getSecondsFromTime($data['estimated_departure']) + $date_at_midnight
      : null;
    $data['actual_departure'] = (strlen($data['actual_departure'])>0)
      ? (int)$this->_getSecondsFromTime($data['actual_departure']) + $date_at_midnight
      : null;
    $data['scheduled_arrival'] = (int)$this->_getSecondsFromTime($data['scheduled_arrival']) + $date_at_midnight;
    $data['estimated_arrival'] = (strlen($data['estimated_arrival'])>0)
      ?(int)$this->_getSecondsFromTime($data['estimated_arrival']) + $date_at_midnight
      : null;
    $data['actual_arrival'] = (strlen($data['actual_arrival'])>0)
      ? (int)$this->_getSecondsFromTime($data['actual_arrival']) + $date_at_midnight
      : null;

    return $data;
  }

  protected function _processOneFlight( $flight, $arrivals ){
    $flightNumberService = new Flightnumber();
    $flightNumberService->setDataSource($this->pdo);
    $airportService = new Airport();
    $airportService->setDataSource($this->pdo);

    $date = strtotime(substr($flight->departure->scheduled, 0, 10));
    $flight_in_database = $this->getByFlightnumberAndDate($flight->flight_number, $date);
    if($flight_in_database){
      $id = $flight_in_database->id;
      $data = array();
      /**
       * The datastream is a little bit "whack" from Air Iceland.  When they have actual time of a flight
       * (departure or arrival) they change the estimated time to N/A again.  This we want to prevent by
       * checking if we have actual data there.  If we do and the data we're getting now is N/A, then we do
       * nothing.  If the data in the database is null and we have time we want to make changes, and also
       * if we have a timestamp and the new timestamp is different.
       *
       * We have to do this for both departure and arrival
       */
      if(!$arrivals) {
        $data['status_departure'] = $flight->status;

        if( strlen($flight->departure->scheduled) == 5 ){
          //We have time, lets check if it's the same or not
          $estimated_departure = $date + (int)$this->_getSecondsFromTime($flight->departure->scheduled);
          if( $estimated_departure != $flight_in_database->estimated_departure ){
            $data['estimated_departure'] = $estimated_departure;
          }
        }

        if( strlen($flight->departure->estimate) == 5 ){
          //We have time, lets check if it's the same or not
          $estimated_departure = $date + (int)$this->_getSecondsFromTime($flight->departure->estimate);
          if( $estimated_departure != $flight_in_database->estimated_departure ){
            $data['estimated_departure'] = $estimated_departure;
          }
        }

        if( strlen($flight->departure->actual) == 5 ){
          //We have time, lets check if it's the same or not
          $actual_departure = $date + (int)$this->_getSecondsFromTime($flight->departure->actual);
          if( $actual_departure != $flight_in_database->actual_departure ){
            $data['actual_departure'] = $actual_departure;
          }
        }
      }
      else{
        $data['status_arrival'] = $flight->status;

        if( strlen($flight->arrival->scheduled) == 5 ){
          //We have time, lets check if it's the same or not
          $estimated_arrival = $date + (int)$this->_getSecondsFromTime($flight->arrival->scheduled);
          if( $estimated_arrival != $flight_in_database->estimated_arrival ){
            $data['estimated_arrival'] = $estimated_arrival;
          }
        }

        if( strlen($flight->arrival->estimate) == 5 ){
          //We have time, lets check if it's the same or not
          $estimated_arrival = $date + (int)$this->_getSecondsFromTime($flight->arrival->estimate);
          if( $estimated_arrival != $flight_in_database->estimated_arrival ){
            $data['estimated_arrival'] = $estimated_arrival;
          }
        }

        if( strlen($flight->arrival->actual) == 5 ){
          //We have time, lets check if it's the same or not
          $actual_arrival = $date + (int)$this->_getSecondsFromTime($flight->arrival->actual);
          if( $actual_arrival != $flight_in_database->actual_arrival ){
            $data['actual_arrival'] = $actual_arrival;
          }
        }
      }

      $this->updateFromStream($id, $data);
    }
    else{
      $data = array();
      $data['last_modified'] = time();
      $data['last_modified_by'] = 1;
      $data['flightnumber'] = $flight->flight_number;
      $data['date'] = $date;
      $data['scheduled_departure'] = $date + (int)$this->_getSecondsFromTime(substr($flight->departure->scheduled, 11, 5));
      $data['scheduled_arrival'] = $date + (int)$this->_getSecondsFromTime(substr($flight->arrival->scheduled, 11, 5));
      $data['airline'] = $flightNumberService->getAirlineFromFlightNumber($flight->flight_number)->id;
      $data['from'] = $airportService->getByCode($flight->departure->airport)->id;
      $data['to'] = $airportService->getByCode($flight->arrival->airport)->id;

      if(!$arrivals){
        $data['status_departure'] = $flight->status;
        $data['estimated_departure'] = ($flight->departure->estimate != "N/A")
          ? $date + (int)$this->_getSecondsFromTime($flight->departure->estimate)
          : null;
        $data['actual_departure'] = ($flight->departure->actual != "N/A")
          ? $date + (int)$this->_getSecondsFromTime($flight->departure->actual)
          : null;
      }
      else{
        $data['status_arrival'] = $flight->status;
        $data['estimated_arrival'] = ($flight->arrival->estimate != "N/A")
          ? $date + (int)$this->_getSecondsFromTime($flight->arrival->estimate)
          : null;
        $data['actual_arrival'] = ($flight->arrival->estimate != "N/A")
          ? $date + (int)$this->_getSecondsFromTime($flight->arrival->actual)
          : null;
      }

      $this->createFromStream($data);
    }
  }
}