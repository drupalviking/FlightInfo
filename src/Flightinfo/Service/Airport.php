<?php
/**
 * Created by PhpStorm.
 * User: drupalviking
 * Date: 06/05/15
 * Time: 10:40
 */
namespace FlightInfo\Service;

use \DateTime;
use \PDOException;
use FlightInfo\Lib\DataSourceAwareInterface;
use \FlightInfo\Service\DatabaseService;

class Airport implements DataSourceAwareInterface {

  use DatabaseService;

  const NAME = 'airport';

  /**
   * @var \PDO
   */
  private $pdo;

  /**
   * Get one airport entry.
   *
   * @param int $id event ID
   * @return \stdClass
   * @throws Exception
   */
  public function get( $id ){
    try{
      $statement = $this->pdo->prepare("
                SELECT * FROM Airport WHERE id = :id
            ");
      $statement->execute(array(
        'id' => $id
      ));
      $airport = $statement->fetchObject();

      if( !$airport ){ return false; }

      return $airport;
    }catch (PDOException $e){
      throw new Exception("Can't get airport item. airport:[{$id}]",0,$e);
    }

  }

  /**
   * Get all airport entries.
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
					SELECT * FROM `Airport` A
					ORDER BY A.name ASC
					LIMIT {$page},{$count}
				");
        $statement->execute();
      }else{
        $statement = $this->pdo->prepare("
					SELECT * FROM `Airport` A
					ORDER BY A.name ASC
				");
        $statement->execute();
      }

      return array_map(function($i) use ($statement){
        //$i->created_date = $i->created_date;
        //$i->modified_date = new DateTime($i->modified_date);

        return $i;
      },$statement->fetchAll());
    }catch (PDOException $e){
      throw new Exception("Can't get next airport item.",0,$e);
    }
  }

  /**
   * Create airport entry.
   *
   * @param array $data
   * @return int ID
   * @throws Exception
   */
  public function create( array $data ){
    //print_r( $data ); die();
    try{
      $data['airport_code'] = strtoupper($data['airport_code']);
      $data['created_date'] = time();
      $data['last_modified'] = time();
      $insertString = $this->insertString('Airport',$data);
      $statement = $this->pdo->prepare($insertString);
      $statement->execute($data);
      $id = (int)$this->pdo->lastInsertId();
      $data['id'] = $id;
      return $id;
    }catch (PDOException $e){
      throw new Exception("Can't create airport entry",0,$e);
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
      $data['last_modified'] = time();
      $data['airport_code'] = strtoupper($data['airport_code']);
      $updateString = $this->updateString('Airport',$data, "id={$id}");
      $statement = $this->pdo->prepare($updateString);
      $statement->execute($data);
      $data['id'] = $id;
      return $statement->rowCount();
    }catch (PDOException $e){
      throw new Exception("Can't update airport entry",0,$e);
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
}