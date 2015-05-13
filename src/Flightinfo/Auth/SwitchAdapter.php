<?php
/**
 * Created by PhpStorm.
 * User: drupalviking
 * Date: 05/05/15
 * Time: 15:40
 */
namespace FlightInfo\Auth;

use \PDO;
use FlightInfo\Lib\DataSourceAwareInterface;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;

class SwitchAdapter implements AdapterInterface, DataSourceAwareInterface
{
  /**
   * @var \PDO
   */
  private $pdo;

  /**
   * @var int
   */
  private $id;

  /**
   * Performs an authentication attempt
   *
   * @return \Zend\Authentication\Result
   * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface If authentication cannot be performed
   */
  public function authenticate()
  {

      $statement = $this->pdo
        ->prepare("
				SELECT * FROM `User`
				WHERE id = :id");
      $statement->execute(array(
        'id' => $this->id,
      ));

    $result = $statement->fetchAll();
    if (count($result) == 0) {
      return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, null);
    } else if (count($result) == 1) {
      $data = $result[0];
      unset($data->passwd);
      return new Result(Result::SUCCESS, $result[0]);
    } else {
      return new Result(Result::FAILURE_IDENTITY_AMBIGUOUS, null);
    }
  }

  /**
   * You can authenticate based on username/password
   * or by using the user's ID. If this value is set
   * then the user's ID will be used to identify.
   *
   * So use this method or self::setCredentials, but
   * not both.
   *
   * @param $id
   */
  public function setIdentifier($id)
  {
    $this->id = $id;
  }

  /**
   * Set username and password
   *
   * You can authenticate based on username/password
   * or by using the user's ID. If this value is set
   * then the user's username/password will be used to
   * identify.
   *
   * So use this method or self::setIdentifier, but
   * not both.
   *
   * @param $username
   * @param $password
   */
  public function setCredentials($username, $password)
  {
        throw new \InvalidArgumentException("Can't set username and password for this " . get_class($this));
  }

  /**
   * Set a configured PDO object.
   *
   * @param \PDO $pdo
   * @return mixed
   */
  public function setDataSource(\PDO $pdo)
  {
    $this->pdo = $pdo;
  }
}
