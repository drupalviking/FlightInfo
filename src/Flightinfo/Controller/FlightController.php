<?php
/**
 * Created by PhpStorm.
 * User: drupalviking
 * Date: 06/05/15
 * Time: 14:55
 */
namespace FlightInfo\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FlightInfo\Form\Flight as FlightForm;

class FlightController extends AbstractActionController{
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
      $form = new FlightForm($airportService);
      //$form->setAttribute('action', $this->url()->fromRoute('flight/update'));

      if ($this->request->isPost()) {
        $form->setData($this->request->getPost());
        if ($form->isValid()) {
          $data = $form->getData();
          unset($data['submit']);
          $id = $flightService->update($data);

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
        return new ViewModel(['form' => $form]);
      }
    }
    else{
      return $this->notFoundAction();
    }
  }
}