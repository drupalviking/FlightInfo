<?php
/**
 * Created by PhpStorm.
 * User: drupalviking
 * Date: 06/05/15
 * Time: 13:27
 */
namespace FlightInfo\Form;

use Zend\Form\Element;
use Zend\Form\Form;

class Airline extends Form{

  public function __construct($name = null)
  {
    parent::__construct( strtolower( str_replace('\\','-',get_class($this) ) ));

    $this->setAttribute('method', 'post');

    $this->add(array(
      'name' => 'name_icelandic',
      'type' => 'Zend\Form\Element\Text',
      'attributes' => array(
        'placeholder' => 'Nafn flugfélags (á íslensku)',
        'required' => 'required',
        'tabindex' => 1
      ),
      'options' => array(
        'label' => 'Nafn (íslenskt)',
      ),
    ));

    $this->add(array(
      'name' => 'name_english',
      'type' => 'Zend\Form\Element\Text',
      'attributes' => array(
        'placeholder' => 'Nafn flugfélags (á ensku)',
        'required' => 'required',
        'tabindex' => 2
      ),
      'options' => array(
        'label' => 'Nafn (enskt)',
      ),
    ));

    $this->add(array(
      'name' => 'carrier_code',
      'type' => 'Zend\Form\Element\Text',
      'attributes' => array(
        'placeholder' => 'Carrier kóði (max 5 stafir)',
        'required' => 'required',
        'tabindex' => 3
      ),
      'options' => array(
        'label' => 'Carrier kóði',
      ),
    ));

    $this->add(array(
      'name' => 'submit',
      'type' => 'Zend\Form\Element\Submit',
      'attributes' => array(
        //'value' => 'Submit',
      ),
      'options' => array(
        'label' => 'Submit',
      ),
    ));

  }
}
