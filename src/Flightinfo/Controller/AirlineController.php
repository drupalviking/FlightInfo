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
use FlightInfo\Form\Airline as AirlineForm;

/**
 * Class AirlineController.
 *
 * @package FlightInfo\Controller
 */
class AirlineController extends AbstractActionController
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

    $airlineService = $sm->get('FlightInfo\Service\Airline');

    if(!$airline = $airlineService->get($this->params()->fromRoute('id'))){
      return $this->notFoundAction();
    }

    return new ViewModel(['airline' => $airline, 'message' => null]);
  }

  public function listAction(){
    $sm = $this->getServiceLocator();

    $airlineService = $sm->get('FlightInfo\Service\Airline');

    if(!$airlines = $airlineService->fetchAll()){
      return $this->notFoundAction();
    }
    return new ViewModel(['airlines' => $airlines, 'message' => null]);
  }

  public function createAction(){
    $sm = $this->getServiceLocator();
    $airlineService = $sm->get('FlightInfo\Service\Airline');
      $authService = $sm->get('Zend\Authentication\AuthenticationService');
      /** @var $authService \FlightInfo\Auth\AuthenticationService */

      if ($authService->isAdmin()) {
      $form = new AirlineForm();
      //POST
      //  http post request
      if ($this->request->isPost()) {
        $form->setData($this->request->getPost());
        //VALID
        //  form is valid
        if ($form->isValid()) {
          $data = $form->getData();
          unset($data['submit']);
          $airlineId = $airlineService->create($data);
          return $this->redirect()->toRoute('airline/index', ['id'=>$airlineId]);
          //INVALID
          //  form data is invalid
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
    else{
      return $this->notFoundAction();
    }
  }

  public function updateAction(){
    $sm = $this->getServiceLocator();
    $airlineService = $sm->get('FlightInfo\Service\Airline');
      $authService = $sm->get('Zend\Authentication\AuthenticationService');
      /** @var $authService \FlightInfo\Auth\AuthenticationService */

    if($authService->isAdmin()){
      $form = new AirlineForm();
      if (($airline = $airlineService->get($this->params()->fromRoute('id')) ) != false) {
        //POST
        //  post request
        if ($this->request->isPost()) {
          $form->setData($this->request->getPost());

          //VALID FORM
          //  form data is valid
          if ($form->isValid()) {
            $data = $form->getData();
            unset($data['submit']);
            $airlineService->update($airline->id, $data);
            return $this->redirect()
              ->toRoute('airline/index', ['id' => $airline->id]);
            //INVALID
            //  form data is invalid
          }
          else {
            $this->getResponse()->setStatusCode(400);
            return new ViewModel(
              [
                'airline' => $airline,
                'form' => $form,
              ]
            );
          }
          //QUERY
          //  get request
        }
        else {
          $form->bind(new ArrayObject((array) $airline));
          $view = new ViewModel(
            [
              'airline' => $airline,
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
