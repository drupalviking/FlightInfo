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

class Airport extends Form{

  public function __construct($name = null)
  {
    parent::__construct( strtolower( str_replace('\\','-',get_class($this) ) ));

    $this->setAttribute('method', 'post');

    $this->add(array(
      'name' => 'name',
      'type' => 'Zend\Form\Element\Text',
      'attributes' => array(
        'placeholder' => 'Nafn flugvallar',
        'required' => 'required',
        'tabindex' => 1
      ),
      'options' => array(
        'label' => 'Nafn',
      ),
    ));

    $this->add(array(
      'name' => 'airport_code',
      'type' => 'Zend\Form\Element\Text',
      'attributes' => array(
        'placeholder' => 'Flugvallarkóði',
        'required' => 'required',
        'tabindex' => 2
      ),
      'options' => array(
        'label' => 'Flugvallarkóði',
      ),
    ));

    $this->add(array(
      'name' => 'latitude',
      'type' => 'Zend\Form\Element\Text',
      'attributes' => array(
        'placeholder' => 'Breiddargráða (Norðlægrar breiddar)',
        'required' => 'required',
        'tabindex' => 3
      ),
      'options' => array(
        'label' => 'Breiddargráða',
      ),
    ));

    $this->add(array(
      'name' => 'longitude',
      'type' => 'Zend\Form\Element\Text',
      'attributes' => array(
        'placeholder' => 'Lengdargráða (Vestlægrar lengdar (neikvæð tala))',
        'required' => 'required',
        'tabindex' => 4
      ),
      'options' => array(
        'label' => 'Lengdargráða',
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
