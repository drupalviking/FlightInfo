<?php
/**
 * Created by PhpStorm.
 * User: drupalviking
 * Date: 05/05/15
 * Time: 15:55
 */
namespace FlightInfo\Controller;

use Zend\Authentication\AuthenticationService;
use Zend\Http\Header\SetCookie;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\SessionManager;
use Zend\Session\Storage\SessionStorage;
use Zend\View\Model\ViewModel;

use Zend\Session\Container;

use FlightInfo\Form\Login;

date_default_timezone_set('UTC');

/**
 * Login / Logout. Create Users
 *
 * Class AuthController
 *
 * @package FlightInfo\Controller
 */
class AuthController extends AbstractActionController
{
  /**
   * Create user.
   *
   * First installment of creating new user in the system.
   *
   * If POST, all information is collected and stored in a session,
   * nothing is written to the database....
   */
  public function createUserAction()
  {
    $session = new Container('create');
    $sm = $this->getServiceLocator();

    $form = $sm->get('FlightInfo\Form\NewUserCredentials');
    /** @var $form \FlightInfo\Form\NewUserCredentials */
    $form->setAttribute('action', $this->url()->fromRoute('access/create'));

    //POST
    if ($this->request->isPost()) {
      $form->setData($this->request->getPost());

      //VALIDATE FORM
      //	validate the form and if valid, store the values in session
      //	and move on to the next part of the creation process.
      if ($form->isValid()) {
        //$data = (array)$form->getData();
        $session->name = $form->get('name')->getValue();
        $session->email = $form->get('email')->getValue();

        return $this->redirect()->toRoute('access/company');
      } else {
        return new ViewModel(['form' => $form]);
      }
      //QUERY
    } else {
      return new ViewModel(['form' => $form]);
    }
  }

    public function switchAction()
    {
        $sm = $this->getServiceLocator();
        $auth = $sm->get('Zend\Authentication\AuthenticationService');

        $sm->get('FlightInfo\Auth\SwitchAdapter');

        $authAdapter =  $sm->get('FlightInfo\Auth\Adapter');
        $authAdapter->setIdentifier($this->params('id'));
        $result = $auth->authenticate($authAdapter);
        if ($result->isValid()) {
            return $this->redirect()->toRoute('home');
        } else {
            throw new \Exception("User [{$this->params('id')}] not found");
        }
    }

  /**
   * Last installment of creating user in the system.
   *
   * @return ViewModel
   */
  public function createUserLoginAction()
  {
    $session = new Container('create_user');

    $form = new NewUserPassword();
    $form->get('name')->setValue($session->email);

    if ($this->getRequest()->isPost()) {
      $form->setData($this->getRequest()->getPost());
      if ($form->isValid()) {
        $session->password = $form->get('password')->getValue();
        return $this->redirect()->toRoute('access/confirm');
      } else {
        return new ViewModel(
          [
            'data' => (object)$session->getArrayCopy(),
            'form' => $form
          ]
        );
      }
    } else {
      return new ViewModel(
        [
          'data' => (object)$session->getArrayCopy(),
          'form' => $form
        ]
      );
    }
  }

  /**
   * Login user.
   *
   * @return \Zend\Http\Response|ViewModel
   */
  public function loginAction()
  {
      $sm = $this->getServiceLocator();
      $auth = $sm->get('Zend\Authentication\AuthenticationService');

    //IS LOGGED IN
    //  user is logged in
    if ($auth->hasIdentity()) {
      return $this->redirect()->toRoute('notandi/index', ['id'=> $auth->getIdentity()->id]);
      //NOT LOGGED IN
      //  user is not logged in
    } else {
      //POST
      //  http post request, trying to log in
      if ($this->request->isPost()) {
        $form = new Login();
        $form->setData($this->getRequest()->getPost());
        //VALID
        //  valid login form
        if ($form->isValid()) {
          //AUTH
          //  get auth adapter, sen it the credentials,
          //  authenticate, through the adapter and
          //  take appropriate steps.
          $data = $form->getData();
          $sm = $this->getServiceLocator();
          $authAdapter =  $sm->get('FlightInfo\Auth\Adapter');
          $authAdapter->setCredentials($data['email'], $data['passwd']);
          $result = $auth->authenticate($authAdapter);
          if ($result->isValid()) {
            $this->getResponse()->getHeaders()->addHeader(
              new SetCookie(
                'backpfeifengesicht',
                $this->getServiceLocator()
                  ->get('FlightInfo\Service\User')
                  ->createHash($auth->getIdentity()->id),
                time() + 365 * 60 * 60 * 24,
                '/'
              )
            );
            return $this->redirect()->toRoute('home');
          } else {
            $form->get('email')->setMessages(["Rangt lykilorð"]);

            return new ViewModel(['form' => $form]);
          }
          //INVALID
          //  invalid login form
        } else {
          return new ViewModel(
            [
              'form' => $form,
            ]
          );
        }
        //lost-password

        //QUERY
        //  http get request, user gets login form
      } else {
        return new ViewModel(['form' => new Login()]);
      }
    }
  }

  /**
   * Logout and destroy session.
   *
   * @return \Zend\Http\Response
   */
  public function logoutAction()
  {
    $auth = new AuthenticationService();
    $this->getResponse()
      ->getHeaders()
      ->addHeader(new SetCookie('backpfeifengesicht', '', strtotime('-1 Year', time()), '/'));
    $auth->clearIdentity();

    return $this->redirect()->toRoute('home');
  }

  /**
   * Request new password.
   *
   * @return ViewModel
   */
  public function lostPasswordAction()
  {
    $sm = $this->getServiceLocator();
    $userService = $sm->get('FlightInfo\Service\User');
    $form = new LostPasswordForm();
    $form->setAttribute('action', $this->url()->fromRoute('access/lost-password'));
    if ($this->request->isPost()) {
      $form->setData($this->request->getPost());
      if ($form->isValid()) {
        $user = $userService->get($form->get('email')->getValue());
        if ($user) {
          $password = $this->createPassword(20);
          $userService->setPassword($user->id, $password);
          $this->getEventManager()->trigger(
            'notify',
            $this,
            array(
              'action' => 'Stjornvisi\Notify\Password',
              'data' => (object)array(
                'recipients' => $user,
                'password' => $password,
              ),
            )
          );
          return new ViewModel(['message' => 'Nýtt lykilorð hefur verið sent', 'form' => $form]);
        } else {
          $form->get('email')->setMessages(array('Notandi fannst ekki'));
          return new ViewModel(['form' => $form, 'message' => null]);
        }
      } else {
        return new ViewModel(['form' => $form, 'message' => null]);
      }
    } else {
      return new ViewModel(['form' => $form, 'message' => null]);
    }
  }

  /**
   * Create a random password.
   *
   * @param  int $length
   * @return string
   */
  private function createPassword($length)
  {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*()_-=+;:?";
    $password = substr(str_shuffle($chars), 0, $length);
    return $password;
  }
}
