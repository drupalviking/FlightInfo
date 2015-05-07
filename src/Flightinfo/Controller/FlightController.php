<?php
/**
 * Created by PhpStorm.
 * User: drupalviking
 * Date: 06/05/15
 * Time: 14:55
 */
namespace FlightInfo\Controller;

use ArrayObject;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FlightInfo\Form\Flight as FlightForm;

class FlightController extends AbstractActionController{
  public function indexAction(){
    $sm = $this->getServiceLocator();
    $flightService = $sm->get('FlightInfo\Service\Flight');
    $airportService = $sm->get('FlightInfo\Service\Airport');

    //FLIGHT FOUND
    //
    if (($flight = $flightService->get($this->params()->fromRoute('id', 0))) != false) {
      return new ViewModel(['flight' => $flight]);
    }
  }

  public function createAction(){
    $sm = $this->getServiceLocator();
    $flightService = $sm->get('FlightInfo\Service\Flight');
    $airportService = $sm->get('FlightInfo\Service\Airport');

    $form = new FlightForm($airportService);
    $form->setAttribute('action', $this->url()->fromRoute('flight/create'));

    if ($this->request->isPost()) {
      $form->setData($this->request->getPost());
      if ($form->isValid()) {
        $data = $form->getData();
        unset($data['submit']);
        $id = $flightService->create($data);

        return $this->redirect()->toRoute('flight/index', ['id'=>$id]);
      } else {
        $this->getResponse()->setStatusCode(400);
        return new ViewModel(['form' => $form]);
      }
      //QUERY
      //  http get request
    } else {
      return new ViewModel(['form' => $form]);
    }
  }

  public function updateAction(){
    $sm = $this->getServiceLocator();
    $flightService = $sm->get('FlightInfo\Service\Flight');
    $airportService = $sm->get('FlightInfo\Service\Airport');

    //FLIGHT FOUND
    //
    if (($flight = $flightService->get($this->params()->fromRoute('id', 0))) != false) {
      //Change times from EPOCH to Human readable
      $flight->date = strftime('%Y-%m-%d', $flight->date);
      $flight->scheduled_departure = strftime('%H:%M', $flight->scheduled_departure);
      $flight->estimated_departure = (isset($flight->estimated_departure)) ?
        strftime('%H:%M', $flight->estimated_departure)
        : null;
      $flight->actual_departure = (isset($flight->actual_departure))
        ? strftime('%H:%M', $flight->actual_departure)
        : null;
      $flight->scheduled_arrival = strftime('%H:%M', $flight->scheduled_arrival);
      $flight->estimated_arrival = (isset($flight->estimated_arrival))
        ? strftime('%H:%M', $flight->estimated_arrival)
        : null;
      $flight->actual_arrival = (isset($flight->actual_arrival))
        ? strftime('%H:%M', $flight->actual_arrival)
        : null;
      $form = new FlightForm($airportService);
      //$form->setAttribute('action', $this->url()->fromRoute('flight/update'));

      if ($this->request->isPost()) {
        $form->setData($this->request->getPost());
        if ($form->isValid()) {
          $data = $form->getData();
          unset($data['submit']);
          $id = $flightService->update($this->params()->fromRoute('id', 0), $data);

          return $this->redirect()->toRoute('flight/index', ['id' => $id]);
        }
        else {
          $this->getResponse()->setStatusCode(400);
          return new ViewModel([
            'form' => $form,
            'flight' => $flight,
          ]);
        }
        //QUERY
        //  http get request
      }
      else {
        $form->bind(new ArrayObject($flight));
        return new ViewModel(
          [
            'flight' => $flight,
            'form' => $form
          ]
        );
      }
    }
    else{
      return $this->notFoundAction();
    }
  }
}