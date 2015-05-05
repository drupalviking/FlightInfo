<?php
/**
 * Created by PhpStorm.
 * User: drupalviking
 * Date: 05/05/15
 * Time: 16:08
 */
namespace FlightInfo\Form;

use Zend\Form\Form;
/**
 * Log in user
 *
 * @category Stjornvisi
 * @package Form
 * @author einar
 * @deprecated
 */
class Login extends Form{

  public function __construct($name = null){

    parent::__construct( strtolower( str_replace('\\','-',get_class($this) ) ));

    $this->setAttribute('method', 'post')->setAttribute('action','/innskra');

    $this->add(array(
      'name' => 'name',
      'attributes' => array(
        'type'  => 'text',
      ),
      'options' => array(
        'label' => 'Nafn',
      ),
    ));
    $this->add(array(
      'name' => 'email',
      'attributes' => array(
        'type'  => 'text',
      ),
      'options' => array(
        'label' => 'Netfang',
      ),
    ));
    $this->add(array(
      'name' => 'passwd',
      'attributes' => array(
        'type'  => 'password',
      ),
      'options' => array(
        'label' => 'Lykilorð',
      ),
    ));
    $this->add(array(
      'name' => 'submit',
      'attributes' => array(
        'type'  => 'submit',
        'value' => 'Innskrá',
        'id' => 'submitbutton',
      ),
    ));
  }


}