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
use Zend\Authentication\AuthenticationService;
use Zend\View\Model\ViewModel;
use FlightInfo\Form\Flight as FlightForm;

class FlightController extends AbstractActionController{
  public function indexAction(){
    $sm = $this->getServiceLocator();
    $flightService = $sm->get('FlightInfo\Service\Flight');
    $airportService = $sm->get('FlightInfo\Service\Airport');
    $auth = new AuthenticationService();

    //FLIGHT FOUND
    //
    if (($flight = $flightService->get($this->params()->fromRoute('id', 0))) != false) {
      $flight = $this->_convertEpochToHumanReadable($flight);
      $airport_from = $airportService->get($flight->airport_from_id);
      $airport_to = $airportService->get($flight->airport_to_id);

      return new ViewModel([
        'flight' => $flight,
        'airport_from' => $airport_from,
        'airport_to' => $airport_to,
        'auth'  => ($auth->hasIdentity()) ? $auth : null
      ]);
    }
  }

  public function listAction(){
    $sm = $this->getServiceLocator();
    $flightService = $sm->get('FlightInfo\Service\Flight');
    $airportService = $sm->get('FlightInfo\Service\Airport');
    $auth = new AuthenticationService();

    //////////////////////////////////////////
    //// XML STREAM FUNCTIONS
    //// @todo: Move to console controller
    /////////////////////////////////////////
    $XMLService = $sm->get('FlightInfo\Service\XMLStream');
    $XMLStreamObject = $XMLService->bootstrap($airportService->fetchAll());
    $flightService->processStream( $XMLStreamObject );
    /////////////////////////////////////////

    $date = strtotime(strftime('%d.%m.%Y', time()));
    $flights = $flightService->fetchAll($date);

    if($flights != false){
      $flights = $this->_covertArrayOfFlightsEpochTimeToHumanReadable($flights);
      return new ViewModel([
        'flights' => $flights,
        'auth'  => ($auth->hasIdentity()) ? $auth : null
      ]);
    }
  }

  public function createAction(){
    $sm = $this->getServiceLocator();
    $flightService = $sm->get('FlightInfo\Service\Flight');
    $airportService = $sm->get('FlightInfo\Service\Airport');
    $airlineService = $sm->get('FlightInfo\Service\Airline');
    $flightnumberService = $sm->get('FlightInfo\Service\Flightnumber');
    $auth = new AuthenticationService();

    if($auth->hasIdentity() ) {
      $form = new FlightForm($airportService);
      $form->setAttribute('action', $this->url()->fromRoute('flight/create'));

      if ($this->request->isPost()) {
        $form->setData($this->request->getPost());
        if ($form->isValid()) {
          $data = $form->getData();
          if($auth->getIdentity()->is_admin){
            $data['airline'] = $flightnumberService->getAirlineFromFlightNumber($data['flightnumber'])->id;
          }
          else{
            $data['airline'] = $auth->getIdentity()->airline;
          }
          unset($data['submit']);
          $id = $flightService->create($data);

          return $this->redirect()->toRoute('flight/index', ['id' => $id]);
        }
        else {
          $this->getResponse()->setStatusCode(400);
          return new ViewModel([
            'form' => $form,
            'airline' => $airlineService->get($auth->getIdentity()->airline)]);
        }
        //QUERY
        //  http get request
      }
      else {
        return new ViewModel([
          'form' => $form,
          'airline' => $airlineService->get($auth->getIdentity()->airline)]);
      }
    }
    else{
      return $this->notFoundAction();
    }
  }

  public function updateAction(){
    $sm = $this->getServiceLocator();
    $flightService = $sm->get('FlightInfo\Service\Flight');
    $airportService = $sm->get('FlightInfo\Service\Airport');
    $flightnumberService = $sm->get('FlightInfo\Service\Flightnumber');
    $authService = new AuthenticationService();

    //FLIGHT FOUND
    //
    if($authService->hasIdentity()) {
      if (($flight = $flightService->get($this->params()->fromRoute('id', 0))) != FALSE) {
        //Change times from EPOCH to Human readable
        $flight = $this->_convertEpochToHumanReadable($flight);

        $form = new FlightForm($airportService);

        if ($this->request->isPost()) {
          $form->setData($this->request->getPost());
          if ($form->isValid()) {
            $data = $form->getData();
            $data['airline'] = $flightnumberService->getAirlineFromFlightNumber($data['flightnumber'])->id;
            unset($data['submit']);
            $id = $flightService->update($this->params()
              ->fromRoute('id', 0), $data);

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
      else {
        return $this->notFoundAction();
      }
    }
    else{
      return $this->notFoundAction();
    }
  }

  protected function _covertArrayOfFlightsEpochTimeToHumanReadable($data){
    if( is_array($data)){
      $return_arr = array();
      foreach($data as $item){
        $return_arr[] = $this->_convertEpochToHumanReadable($item);
      }
      return $return_arr;
    }
    else{
      return $this->_convertEpochToHumanReadable($data);
    }
  }

  protected function _convertEpochToHumanReadable($flight){
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

    return $flight;
  }
}