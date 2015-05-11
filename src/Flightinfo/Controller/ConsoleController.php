<?php
/**
 * Created by PhpStorm.
 * User: drupalviking
 * Date: 11/05/15
 * Time: 09:38
 */
namespace Stjornvisi\Controller;

use Zend\Console\Request as ConsoleRequest;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver;

class ConsoleController extends AbstractActionController{
  public function processStream(){
    $sm = $this->getServiceLocator();
    $flightService = $sm->get('FlightInfo\Service\Flight');
    $airportService = $sm->get('FlightInfo\Service\Airport');

    $XMLService = $sm->get('FlightInfo\Service\XMLStream');
    $XMLStreamObject = $XMLService->bootstrap($airportService->fetchAll());
    $flightService->processStream( $XMLStreamObject );
  }
}