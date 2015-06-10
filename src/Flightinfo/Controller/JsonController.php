<?php
/**
 * Created by PhpStorm.
 * User: drupalviking
 * Date: 13/05/15
 * Time: 10:34
 */
namespace FlightInfo\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\View\Model\JsonModel;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver;

class JsonController extends AbstractActionController{
  public function flightsAction(){
    $sm = $this->getServiceLocator();
    $jsonService = $sm->get('FlightInfo\Service\Json');

    $date = ($this->params()->fromRoute('date')) ? $this->params()->fromRoute('date') : strftime('%Y-%m-%d');
    $type = ($this->params()->fromRoute('type') == 'arrival') ? true : false;
    $airports = ($this->params()->fromRoute('airports')) ? explode(",", $this->params()->fromRoute('airports') ) : null;
    $carriers = ($this->params()->fromRoute('carriers')) ? explode(",", $this->params()->fromRoute('carriers') ) : null;

    return new JsonModel($jsonService->fetchByParameters($type, $date, $airports, $carriers));
  }

  /**
   * Returns all airports from database in JSON format
   *
   * @return \Zend\View\Model\JsonModel
   */
  public function airportsAction(){
    $sm = $this->getServiceLocator();
    $jsonService = $sm->get('FlightInfo\Service\Json');

    $airports = $jsonService->getAirports();
    return new JsonModel($airports);
  }

  /**
   * Returns all airlines from database in JSON format
   *
   * @return \Zend\View\Model\JsonModel
   */
  public function airlinesAction(){
    $sm = $this->getServiceLocator();
    $jsonService = $sm->get('FlightInfo\Service\Json');

    $airlines = $jsonService->getAirlines();
    return new JsonModel($airlines);
  }
}