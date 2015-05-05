<?php
/**
 * Created by PhpStorm.
 * User: drupalviking
 * Date: 05/05/15
 * Time: 16:11
 */
namespace FlightInfo\Form;

use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Form;

class User extends Form{
  public function __construct(){

    parent::__construct( strtolower( str_replace('\\','-',get_class($this) ) ));

    $this->setAttribute('method', 'post');

    $this->add(array(
      'name' => 'name',
      'type' => 'Zend\Form\Element\Text',
      'attributes' => array(
        'placeholder' => 'Nafn...',
        'required' => 'required',
      ),
      'options' => array(
        'label' => 'Nafn',
      ),
    ));

    $this->add(array(
      'name' => 'email',
      'type' => 'Zend\Form\Element\Email',
      'attributes' => array(
        'placeholder' => 'Netfang...',
        'required' => 'required',
      ),
      'options' => array(
        'label' => 'Netfang',
      ),
    ));

    $this->add(array(
      'name' => 'password',
      'type' => 'Zend\Form\Element\Password',
      'attributes' => array(
        'placeholder' => 'Lykilorð...',
        'required' => 'required',
      ),
      'options' => array(
        'label' => 'Lykilorð',
      ),
    ));

    $this->add(array(
      'name' => 'password-again',
      'type' => 'Zend\Form\Element\Password',
      'attributes' => array(
        'placeholder' => 'Lykilorð aftur...',
        'required' => 'required',
      ),
      'options' => array(
        'label' => 'Lykilorð aftur',
      ),
    ));

    $this->add(array(
      'name' => 'submit',
      'type' => 'Zend\Form\Element\Submit',
      'attributes' => array(
        'value' => 'Submit',
      ),
      'options' => array(
        'label' => 'Submit',
      ),
    ));
  }
}