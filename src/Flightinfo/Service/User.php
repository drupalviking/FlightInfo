<?php
/**
 * Created by PhpStorm.
 * User: drupalviking
 * Date: 05/05/15
 * Time: 16:21
 */
namespace FlightInfo\Service;

use \DateTime;
use \PDOException;
use FlightInfo\Lib\DataSourceAwareInterface;

class User extends AbstractService implements DataSourceAwareInterface {
  const REGISTER = 'user.register';
  const NAME = 'user.create';

  /**
   * @var \PDO
   */
  private $pdo;

  /**
   * Get one user.
   *
   * @param int|string $id Numberic ID of user or email
   * @return \stdClass|bool mixed
   * @throws Exception
   */
  public function get($id) {
    try {
      if (filter_var($id, FILTER_VALIDATE_EMAIL)) {
        $statement = $this->pdo->prepare("
					SELECT U.*, MD5( CONCAT(U.id,U.email) ) AS hash FROM `User` U WHERE email = :id
				");
      }
      else {
        $statement = $this->pdo->prepare("
					SELECT U.*, MD5( CONCAT(U.id,U.email) ) AS hash FROM `User` U WHERE id = :id
				");
      }

      $statement->execute(array(
        'id' => $id
      ));
      $user = $statement->fetchObject();

      $this->getEventManager()->trigger('read', $this, array(__FUNCTION__));
      return $user;
    } catch (PDOException $e) {
      $this->getEventManager()->trigger('read', $this, array(
        'exception' => $e->getTraceAsString(),
        'sql' => array(
          isset($statement) ? $statement->queryString : NULL
        )
      ));
      throw new Exception("Can't get user. user:[{$id}]", 0, $e);
    }


  }

  public function createHash($id) {
    try {
      $statement = $this->pdo->prepare("
				SELECT MD5( CONCAT(U.id,U.email) ) AS hash
				FROM `User` U WHERE id = :id;
			");
      $statement->execute(['id' => $id]);
      return $statement->fetchColumn(0);

    } catch (PDOException $e) {
      $this->getEventManager()->trigger('error', $this, [
        'exception' => $e->getTraceAsString(),
        'sql' => [
          isset($statement) ? $statement->queryString : NULL,
        ]
      ]);
      throw new Exception("Can't get hash of a user. user:[{$id}]", 0, $e);
    }
  }

  public function getByHash($hash) {
    try {

      $statement = $this->pdo->prepare("
				SELECT * FROM `User` U WHERE MD5( CONCAT(U.id,U.email) ) = :hash;
			");


      $statement->execute(array(
        'hash' => $hash
      ));
      $user = $statement->fetchObject();

      $this->getEventManager()->trigger('read', $this, array(__FUNCTION__));
      return $user;
    } catch (PDOException $e) {
      $this->getEventManager()->trigger('read', $this, array(
        'exception' => $e->getTraceAsString(),
        'sql' => array(
          isset($statement) ? $statement->queryString : NULL
        )
      ));
      throw new Exception("Can't get user. user:[{$id}]", 0, $e);
    }
  }

  /**
   * Get type of user.
   * Basically what this does is to check if user
   * is <em>admin</em> or not.
   * @param $id
   *
   * @return object
   * @throws Exception
   */
  public function getType( $id ){
    try{
      $statement = $this->pdo->prepare("
				SELECT is_admin
				FROM `User` WHERE id = :id"
      );
      $statement->execute(array( 'id' => $id ));
      $value = $statement->fetchColumn(0);
      $this->getEventManager()->trigger('read', $this, array(__FUNCTION__));
      return (object)array(
        'is_admin' => (bool)$value,
        'type' => 0
      );
    }catch (PDOException $e){
      $this->getEventManager()->trigger('error', $this, array(
        'exception' => $e->getTraceAsString(),
        'sql' => array(
          isset($statement)?$statement->queryString:null
        )
      ));
      throw new Exception("Can't get type of user",0,$e);
    }
  }
  /**
   * Set new password on user.
   *
   * This action will encrypt the password
   * @param int $id
   * @param string $password
   * @return int
   * @throws Exception
   */
  public function setPassword( $id, $password ){
    try{
      $statement = $this->pdo->prepare("
				UPDATE `User` SET passwd = MD5(:password)
				WHERE id = :id");
      $statement->execute(array(
        'password' => $password,
        'id' => $id
      ));
      $this->getEventManager()->trigger('update', $this, array(__FUNCTION__));
      return $statement->rowCount();
    }catch (PDOException $e){
      $this->getEventManager()->trigger('error', $this, array(
        'exception' => $e->getTraceAsString(),
        'sql' => array(
          isset($statement)?$statement->queryString:null,
        )
      ));
      throw new Exception("Can't set user's password. user:[{$id}]",0,$e);
    }
  }

  /**
   * @param array $data
   * @return int
   * @throws \Exception
   */
  public function create( $data ){
    try{
      $data['passwd'] = md5($data['passwd']);
      $data['created_date'] = date('Y-m-d H:i:s');
      $data['modified_date'] = date('Y-m-d H:i:s');

      $createString = $this->insertString('User',$data);
      $createStatement = $this->pdo->prepare($createString);
      $createStatement->execute($data);

      $id = (int)$this->pdo->lastInsertId();

      $data['id'] = $id;
      $this->getEventManager()->trigger('create', $this, array(
        0 => __FUNCTION__,
        'data' => $data
      ));

      $this->getEventManager()->trigger('index', $this, array(
        0 => __NAMESPACE__ .':'.get_class($this).':'. __FUNCTION__,
        'id' => $id,
        'name' => User::NAME,
      ));
      return $id;
    }catch (PDOException $e){
      $this->getEventManager()->trigger('error', $this, array(
        'exception' => $e->getTraceAsString(),
        'sql' => array(
          isset($createStatement)?$createStatement->queryString:null
        )
      ));
      throw new Exception("Can't create user. " . $e->getMessage() ,0,$e);
    }
  }

  public function update( $id, $data ){
    try{
      $data['modified_date'] = date('Y-m-d H:i:s');

      $updateString = $this->updateString('User',$data,"id={$id}");
      $updateStatement = $this->pdo->prepare($updateString);
      $updateStatement->execute($data);

      $data['id'] = $id;
      $this->getEventManager()->trigger('update', $this, array(
        0 => __FUNCTION__,
        'data' => $data
      ));

      $this->getEventManager()->trigger('index', $this, array(
        0 => __NAMESPACE__ .':'.get_class($this).':'. __FUNCTION__,
        'id' => $id,
        'name' => User::NAME,
      ));
      return $id;
    }catch (PDOException $e){
      $this->getEventManager()->trigger('error', $this, array(
        'exception' => $e->getTraceAsString(),
        'sql' => array(
          isset($createStatement)?$createStatement->queryString:null
        )
      ));
      throw new Exception("Can't update user[$id]. " . $e->getMessage() ,0,$e);
    }
  }

  public function setDataSource(\PDO $pdo){
    $this->pdo = $pdo;
    return $this;
  }
}