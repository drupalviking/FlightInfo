<?php
/**
 * Created by PhpStorm.
 * User: drupalviking
 * Date: 06/05/15
 * Time: 10:48
 */
namespace FlightInfo\Controller;

date_default_timezone_set('UTC');
setlocale(LC_ALL, 'is_IS');

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\ArrayObject;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;
//use FlightInfo\Form\Airport as AirportForm;

/**
 * Class AirportController.
 *
 * @package FlightInfo\Controller
 */
class AirportController extends AbstractActionController
{
  /**
   * Entries per page
   */
  const AIRPORT_COUNT_PER_PAGE = 15;

  /**
   * Display one airport entry.
   *
   * @return array|ViewModel
   */
  public function indexAction() {
    $sm = $this->getServiceLocator();

    $airportService = $sm->get('FlightInfo\Service\Airport');

    if(!$airport = $airportService->get($this->params()->fromRoute('id'))){
      return $this->notFoundAction();
    }
    return new ViewModel(['airport' => $airport, 'message' => null]);
  }

  public function listAction(){
    $sm = $this->getServiceLocator();

    $airportService = $sm->get('FlightInfo\Service\Airport');

    if(!$airports = $airportService->fetchAll()){
      return $this->notFoundAction();
    }
    return new ViewModel(['airports' => $airports, 'message' => null]);
  }
}