<?php
/**
 * Created by PhpStorm.
 * User: drupalviking
 * Date: 11/05/15
 * Time: 15:37
 */
namespace FlightInfo\Controller;

use ArrayObject;
use FlightInfo\Form\User;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;

use FlightInfo\Form\User as UserForm;
use FlightInfo\Form\Password as PasswordForm;

/**
 * Class UserController.
 *
 * @package FlightInfo\Controller
 */
class UserController extends AbstractActionController{
  /**
   * Get one user.
   *
   * @return array|ViewModel
   */
  public function indexAction(){
    $sm = $this->getServiceLocator();
    $userService = $sm->get('FlightInfo\Service\User');
      $authService = $sm->get('Zend\Authentication\AuthenticationService');
      /** @var $authService \FlightInfo\Auth\AuthenticationService */

    if($authService->isAdmin()) {
      if (($user = $userService->get($this->params()
          ->fromRoute('id', 0))) != FALSE
      ) {
        return new ViewModel(
          [
            'user' => $user,
          ]
        );
      }
      else {
        return $this->notFoundAction();
      }
    }
    else{
      return $this->notFoundAction();
    }
  }

  public function listAction(){
    $sm = $this->getServiceLocator();
    $userService = $sm->get('FlightInfo\Service\User');

      $authService = $sm->get('Zend\Authentication\AuthenticationService');
      /** @var $authService \FlightInfo\Auth\AuthenticationService */

    if($authService->isAdmin()) {
      if (($users = $userService->fetchAll()) != FALSE) {
        return new ViewModel(
          [
            'users' => $users,
          ]
        );
      }
      else {
        return $this->notFoundAction();
      }
    }
    else{
      return $this->notFoundAction();
    }
  }

  public function createAction(){
    $sm = $this->getServiceLocator();
    $userService = $sm->get('FlightInfo\Service\User');
    $airlineService = $sm->get('FlightInfo\Service\Airline');
      $authService = $sm->get('Zend\Authentication\AuthenticationService');
      /** @var $authService \FlightInfo\Auth\AuthenticationService */

    if($authService->isAdmin()) {
      $form = new UserForm($airlineService);

      if ($this->request->isPost()) {
        $form->setData($this->request->getPost());
        //VALID
        //  form is valid
        if ($form->isValid()) {
          $data = $form->getData();
          unset($data['submit']);
          $airportId = $userService->create($data);
          return $this->redirect()->toRoute('user/index', ['id' => $airportId]);
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
    $userService = $sm->get('FlightInfo\Service\User');
    $airlineService = $sm->get('FlightInfo\Service\Airline');
      $authService = $sm->get('Zend\Authentication\AuthenticationService');
      /** @var $authService \FlightInfo\Auth\AuthenticationService */

    if($authService->isAdmin()) {
      if (($user = $userService->get($this->params()->fromRoute('id')) ) != false) {
        $form = new UserForm($airlineService);
        $form->setAttribute('action', $this->url()->fromRoute('user/update', ['id'=>$user->id]));

        if ($this->request->isPost()) {
          $form->setData($this->request->getPost());
          //VALID FORM
          //
          if ($form->isValid()) {
            $data = $form->getData();

            $userService->update($user->id, $data);
            return $this->redirect()->toRoute('user/index', ['id'=>$user->id]);
            //INVALID
            //
          } else {
            $this->getResponse()->setStatusCode(400);
            return new ViewModel(['form' => $form, 'user' => $user]);
          }
          //QUERY
          //  get request
        } else {
          $form->bind(new ArrayObject($user));
          return new ViewModel(['form' => $form, 'user' => $user]);
        }
      }
    }
    else{
      return $this->notFoundAction();
    }
  }
}
