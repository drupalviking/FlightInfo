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

class Flight extends AbstractService implements DataSourceAwareInterface {

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
          SELECT f.id, f.flightnumber, f.date, a.name as airport_from, a.airport_code as airportcode_from, a2.name as airport_to, a2.airport_code as airportcode_to, f.last_modified, u.name,
          f.scheduled_departure, f.estimated_departure, f.actual_departure, f.scheduled_arrival, f.estimated_arrival, f.actual_arrival, f.status_departure, f.status_arrival
          FROM flight_info.Flight f
          INNER JOIN flight_info.Airport a
          ON f.from = a.id
          INNER JOIN flight_info.Airport a2
          ON f.to = a2.id
          INNER JOIN flight_info.User u
          on f.last_modified_by = u.id
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
   * Get all flight entries.
   *
   * @param int $page page number
   * @param int $count number of items pr. page
   * @return \stdClass
   * @throws Exception
   */
  public function fetchAll($page=null, $count=10){
    try{
      if($page !== null){
        $statement = $this->pdo->prepare("
					SELECT * FROM `Flight` F
					ORDER BY F.date DESC
					LIMIT {$page},{$count}
				");
        $statement->execute();
      }else{
        $statement = $this->pdo->prepare("
					SELECT * FROM `Flight` F
					ORDER BY F.date DESC
				");
        $statement->execute();
      }

      return array_map(function($i) use ($statement){
        //$i->created_date = $i->created_date;
        //$i->modified_date = new DateTime($i->modified_date);

        return $i;
      },$statement->fetchAll());
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
    $date_at_midnight = strtotime($data['date']);

    try{
      $data['date'] = $date_at_midnight;
      $data['scheduled_departure'] = (int)$this->_getSecondsFromTime($data['scheduled_departure']) + $date_at_midnight;
      $data['estimated_departure'] = (is_string($data['estimated_departure']))
        ? (int)$this->_getSecondsFromTime($data['estimated_departure']) + $date_at_midnight
        : null;
      $data['actual_departure'] = (is_string($data['actual_departure']))
        ? (int)$this->_getSecondsFromTime($data['actual_departure']) + $date_at_midnight
        : null;
      $data['scheduled_arrival'] = (int)$this->_getSecondsFromTime($data['scheduled_arrival']) + $date_at_midnight;
      $data['estimated_arrival'] = (is_string($data['estimated_arrival']))
        ?(int)$this->_getSecondsFromTime($data['estimated_arrival']) + $date_at_midnight
        : null;
      $data['actual_arrival'] = (is_string($data['actual_arrival']))
        ? (int)$this->_getSecondsFromTime($data['actual_arrival']) + $date_at_midnight
        : null;

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
   * Update one entry.
   *
   * @param $id news ID
   * @param array $data
   * @return int row count
   * @throws Exception
   * @todo created_date
   */
  public function update( $id, array $data ){
    $date_at_midnight = strtotime($data['date']);

    try{
      $data['date'] = $date_at_midnight;
      $data['scheduled_departure'] = (int)$this->_getSecondsFromTime($data['scheduled_departure']) + $date_at_midnight;
      $data['estimated_departure'] = (is_string($data['estimated_departure']))
        ? (int)$this->_getSecondsFromTime($data['estimated_departure']) + $date_at_midnight
        : null;
      $data['actual_departure'] = (is_string($data['actual_departure']))
        ? (int)$this->_getSecondsFromTime($data['actual_departure']) + $date_at_midnight
        : null;
      $data['scheduled_arrival'] = (int)$this->_getSecondsFromTime($data['scheduled_arrival']) + $date_at_midnight;
      $data['estimated_arrival'] = (is_string($data['estimated_arrival']))
        ?(int)$this->_getSecondsFromTime($data['estimated_arrival']) + $date_at_midnight
        : null;
      $data['actual_arrival'] = (is_string($data['actual_arrival']))
        ? (int)$this->_getSecondsFromTime($data['actual_arrival']) + $date_at_midnight
        : null;

      $data['last_modified'] = time();
      $data['last_modified_by'] = 1;

      $updateString = $this->updateString('Flight',$data, "id={$id}");
      $statement = $this->pdo->prepare($updateString);
      $statement->execute($data);
      $data['id'] = $id;
      return $statement->rowCount();
    }catch (PDOException $e){
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
                DELETE FROM `Airport`
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

  public function setDataSource(\PDO $pdo){
    $this->pdo = $pdo;
  }

  public function _getSecondsFromTime( $time ){
    $hours = substr( $time, 0, 2 );
    $mins = substr( $time, 3, 2 );
    return (int)($hours * 60 * 60) + (int)($mins * 60);
  }

  public function _getCarrierCodeFromFlightNumber( $flight_number ){
    return preg_replace("/[^A-Z]+/", "", $flight_number);
  }
}