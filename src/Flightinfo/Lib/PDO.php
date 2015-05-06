<?php
/**
 * Created by PhpStorm.
 * User: drupalviking
 * Date: 05/05/15
 * Time: 16:24
 */
namespace FlightInfo\Lib;

use \PDO as OriginalPDO;

/**
 * Class PDO
 * @package Stjornvisi\Lib
 * @deprecated
 */
class PDO extends OriginalPDO {

  protected $dsn;
  protected $username;
  protected $password;
  protected $options;

  public function __construct( $dsn,  $username = "",  $password = "", array $options = array() ){
    $this->dsn = $dsn;
    $this->username = $username;
    $this->password = $password;
    $this->options = $options;
    parent::__construct( $dsn,  $username,  $password,  $options );
  }

  public function getDsn(){
    return $this->dsn;
  }

  public function getUsername(){
    return $this->username;
  }

  public function getPassword(){
    return $this->password;
  }

  public function getOptions(){
    return $this->options;
  }
}