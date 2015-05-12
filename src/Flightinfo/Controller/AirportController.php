<?php
/**
 * Created by PhpStorm.
 * User: drupalviking
 * Date: 06/05/15
 * Time: 10:48
 */
namespace FlightInfo\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\ArrayObject;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;
use FlightInfo\Form\Airport as AirportForm;

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

  public function createAction(){
    $sm = $this->getServiceLocator();
    $airportService = $sm->get('FlightInfo\Service\Airport');
    $auth = new AuthenticationService();

    if( $auth->hasIdentity() && $auth->getIdentity()->id == 1) {
      $form = new AirportForm();
      //POST
      //  http post request
      if ($this->request->isPost()) {
        $form->setData($this->request->getPost());
        //VALID
        //  form is valid
        if ($form->isValid()) {
          $data = $form->getData();
          unset($data['submit']);
          $data['created_by'] = 1;
          $data['last_modified_by'] = 1;
          $airportId = $airportService->create($data);
          return $this->redirect()
            ->toRoute('airport/index', ['id' => $airportId]);
          //INVALID
          //  form data is invalid
        }
        else {
          $this->getResponse()->setStatusCode(400);
          return new ViewModel(['form' => $form]);
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

  public function updateAction(){
    $sm = $this->getServiceLocator();
    $airportService = $sm->get('FlightInfo\Service\Airport');
    $auth = new AuthenticationService();

    if( $auth->hasIdentity() && $auth->getIdentity()->id == 1) {
      $form = new AirportForm();
      if (($airport = $airportService->get($this->params()->fromRoute('id')) ) != false) {
        //POST
        //  post request
        if ($this->request->isPost()) {
          $form->setData($this->request->getPost());

          //VALID FORM
          //  form data is valid
          if ($form->isValid()) {
            $data = $form->getData();
            unset($data['submit']);
            $airportService->update($airport->id, $data);
            return $this->redirect()
              ->toRoute('airport/index', ['id' => $airport->id]);
            //INVALID
            //  form data is invalid
          }
          else {
            $this->getResponse()->setStatusCode(400);
            return new ViewModel(
              [
                'airport' => $airport,
                'form' => $form,
              ]
            );
          }
          //QUERY
          //  get request
        }
        else {
          $form->bind(new ArrayObject((array) $airport));
          $view = new ViewModel(
            [
              'airport' => $airport,
              'form' => $form,
            ]
          );

          $view->setTerminal($this->request->isXmlHttpRequest());
          return $view;
        }
      }
    }
    else{
      return $this->notFoundAction();
    }
  }
}
