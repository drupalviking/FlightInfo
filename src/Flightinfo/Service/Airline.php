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

class Airline implements DataSourceAwareInterface {

  use DatabaseService;

  const NAME = 'airline';

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
  public function get($id) {
    try {
      $statement = $this->pdo->prepare("
          SELECT * FROM Airline
          WHERE id = :id
      ");
      $statement->execute(array(
        'id' => $id
      ));
      $flight = $statement->fetchObject();

      if (!$flight) {
        return FALSE;
      }

      return $flight;
    } catch (PDOException $e) {
      throw new Exception("Can't get airline item. airline:[{$id}]", 0, $e);
    }
  }

  public function getAirlineNames(){
    try{
      $return_array = null;
      $statement = $this->pdo->prepare("
        SELECT id, name_icelandic FROM Airline
        ORDER BY name_icelandic
      ");
      $statement->execute();
      $airlines = $statement->fetchAll();

      if (!$airlines) {
        return FALSE;
      }
      foreach( $airlines as $airline ){
        $return_array[$airline->id] = $airline->name_icelandic;
      }

      return $return_array;
    } catch (PDOException $e) {
      throw new Exception("Can't get airline item.", 0, $e);
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
  public function fetchAll() {
    try {
      $statement = $this->pdo->prepare("
					SELECT * FROM `Airline` A
					ORDER BY A.name_icelandic ASC
				");
      $statement->execute();

      return array_map(function ($i) use ($statement) {
        //$i->created_date = $i->created_date;
        //$i->modified_date = new DateTime($i->modified_date);

        return $i;
      }, $statement->fetchAll());
    } catch (PDOException $e) {
      throw new Exception("Can't get next airline item.", 0, $e);
    }
  }

  /**
   * Create airline entry.
   *
   * @param array $data
   * @return int ID
   * @throws Exception
   */
  public function create( array $data ){
    try{
      $insertString = $this->insertString('Airline',$data);
      $statement = $this->pdo->prepare($insertString);
      $statement->execute($data);
      $id = (int)$this->pdo->lastInsertId();
      $data['id'] = $id;
      return $id;
    }catch (PDOException $e){
      throw new Exception("Can't create airline entry",0,$e);
    }

  }

  public function update( $id, array $data ){
    try{
      $updateString = $this->updateString('Airline',$data, "id={$id}");
      $statement = $this->pdo->prepare($updateString);
      $statement->execute($data);
      $data['id'] = $id;
      return $statement->rowCount();
    }catch (PDOException $e){
      throw new Exception("Can't update airline entry",0,$e);
    }
  }

  public function setDataSource(\PDO $pdo){
    $this->pdo = $pdo;
  }
}
