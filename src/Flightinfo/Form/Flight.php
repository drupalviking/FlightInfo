<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 2/13/14
 * Time: 11:06 PM
 */

namespace FlightInfo\Form;

use Zend\Form\Element;
use Zend\Form\Form;

use FlightInfo\Service\Airport;

class Flight extends Form {
  private $airports;

  private $id = NULL;

  public function __construct(Airport $airports) {
    $this->airports = $airports;

    parent::__construct(strtolower(str_replace('\\', '-', get_class($this))));

    $this->setAttribute('method', 'post');

    $this->add(array(
      'name' => 'flightnumber',
      'type' => 'Zend\Form\Element\Text',
      'attributes' => array(
        'placeholder' => 'Flugnúmer',
        'required' => 'required',
        'tabindex' => 1
      ),
      'options' => array(
        'label' => 'Flugnúmer',
      ),
    ));

    $this->add(array(
      'name' => 'date',
      'type' => 'Zend\Form\Element\Date',
      'attributes' => array(
        'placeholder' => 'Dagsetning flugs',
        'required' => 'required',
        'tabindex' => 2
      ),
      'options' => array(
        'label' => 'Dagsetning',
      ),
    ));

    $this->add([
      'name' => 'from',
      'type' => 'Zend\Form\Element\Select',
      'attributes' => [
        'required' => 'required',
        'tabindex' => 3
      ],
      'options' => [
        'label' => 'Brottfararflugvöllur',
        'value_options' => $airports->getAirportNames(),
      ],
    ]);

    $this->add([
      'name' => 'to',
      'type' => 'Zend\Form\Element\Select',
      'attributes' => [
        'required' => 'required',
        'tabindex' => 4
      ],
      'options' => [
        'label' => 'Komuflugvöllur',
        'value_options' => $airports->getAirportNames(),
      ],
    ]);

    $this->add(array(
      'name' => 'scheduled_departure',
      'type' => 'Zend\Form\Element\Text',
      'attributes' => array(
        'placeholder' => '00:00',
        'required' => 'required',
        'tabindex' => 5
      ),
      'options' => array(
        'label' => 'Áætluð brottför',
      ),
    ));

    $this->add(array(
      'name' => 'estimated_departure',
      'type' => 'Zend\Form\Element\Text',
      'attributes' => array(
        'placeholder' => '00:00',
        'tabindex' => 6
      ),
      'options' => array(
        'label' => 'Staðfest brottför',
      ),
    ));

    $this->add(array(
      'name' => 'actual_departure',
      'type' => 'Zend\Form\Element\Text',
      'attributes' => array(
        'placeholder' => '00:00',
        'tabindex' => 7
      ),
      'options' => array(
        'label' => 'Rauntími brottfarar',
      ),
    ));

    $this->add(array(
      'name' => 'scheduled_arrival',
      'type' => 'Zend\Form\Element\Text',
      'attributes' => array(
        'placeholder' => '00:00',
        'required' => 'required',
        'tabindex' => 8
      ),
      'options' => array(
        'label' => 'Áætluð koma',
      ),
    ));

    $this->add(array(
      'name' => 'estimated_arrival',
      'type' => 'Zend\Form\Element\Text',
      'attributes' => array(
        'placeholder' => '00:00',
        'tabindex' => 9
      ),
      'options' => array(
        'label' => 'Staðfestur komutími',
      ),
    ));

    $this->add(array(
      'name' => 'actual_arrival',
      'type' => 'Zend\Form\Element\Text',
      'attributes' => array(
        'placeholder' => '00:00',
        'tabindex' => 10
      ),
      'options' => array(
        'label' => 'Raun komutími',
      ),
    ));

    $this->add(array(
      'name' => 'status_departure',
      'type' => 'Zend\Form\Element\Text',
      'attributes' => array(
        'placeholder' => 'Staða brottfarar (frjáls texti)',
        'tabindex' => 11
      ),
      'options' => array(
        'label' => 'Staða brottfarar',
      ),
    ));

    $this->add(array(
      'name' => 'status_arrival',
      'type' => 'Zend\Form\Element\Text',
      'attributes' => array(
        'placeholder' => 'Staða komu (frjáls texti)',
        'tabindex' => 11
      ),
      'options' => array(
        'label' => 'Staða komu',
      ),
    ));

    $this->add(array(
      'name' => 'submit',
      'type' => 'Zend\Form\Element\Submit',
      'attributes' => array(//'value' => 'Submit',
      ),
      'options' => array(
        'label' => 'Submit',
      ),
    ));

  }
}