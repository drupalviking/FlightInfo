<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
  'session' => array(
    'remember_me_seconds' => 2419200,
    'use_cookies' => true,
    'cookie_httponly' => true,
  ),
  'router' => array(
    'routes' => array(
      'home' => array(
        'type' => 'Zend\Mvc\Router\Http\Literal',
        'options' => array(
          'route'    => '/',
          'defaults' => array(
            'controller' => 'FlightInfo\Controller\Index',
            'action'     => 'index',
          ),
        ),
      ),
      'notandi' => array(
        'type' => 'Zend\Mvc\Router\Http\Literal',
        'options' => array(
          'route' => '/notandi',
          'defaults' => array(
            'controller' => 'FlightInfo\Controller\User',
            'action' => 'list'
          ),
        ),
        'may_terminate' => true,
        'child_routes' => array(
          'index' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
              'route' => '/:id',
              'constraints' => array(
                'id' => '[0-9]*',
              ),
              'defaults' => array(
                'controller' => 'FlightInfo\Controller\User',
                'action' => 'index'
              ),
            )
          ),
          'update' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
              'route' => '/:id/uppfaera',
              'constraints' => array(
                'id' => '[0-9]*',
              ),
              'defaults' => array(
                'controller' => 'FlightInfo\Controller\User',
                'action' => 'update'
              ),
            )
          ),
          'create' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
              'route' => '/stofna',
              'constraints' => array(
                'id' => '[0-9]*',
              ),
              'defaults' => array(
                'controller' => 'FlightInfo\Controller\Auth',
                'action' => 'create-user'
              ),
            )
          ),

          'company' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
              'route' => '/stofna/fyrirtaeki',
              'constraints' => array(
                'id' => '[0-9]*',
              ),
              'defaults' => array(
                'controller' => 'FlightInfo\Controller\Auth',
                'action' => 'create-user-company'
              ),
            )
          ),
          'login' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
              'route' => '/stofna/innskra',
              'constraints' => array(
                'id' => '[0-9]*',
              ),
              'defaults' => array(
                'controller' => 'FlightInfo\Controller\Auth',
                'action' => 'create-user-login'
              ),
            )
          ),


          'delete' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
              'route' => '/:id/eyda',
              'constraints' => array(
                'id' => '[0-9]*',
              ),
              'defaults' => array(
                'controller' => 'FlightInfo\Controller\User',
                'action' => 'delete'
              ),
            )
          ),
          'change-password' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
              'route' => '/:id/lykilord',
              'constraints' => array(
                'id' => '[0-9]*',
              ),
              'defaults' => array(
                'controller' => 'FlightInfo\Controller\User',
                'action' => 'change-password'
              ),
            )
          ),
        ),
      ),
      'access' => array(
        'type' => 'Zend\Mvc\Router\Http\Literal',
        'options' => array(
          'route' => '/adgangur',
          'defaults' => array(
            'controller' => 'FlightInfo\Controller\Auth',
            'action' => 'login'
          ),
        ),
        'child_routes' => array(
          'create' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
              'route' => '/stofna',
              'defaults' => array(
                'controller' => 'FlightInfo\Controller\Auth',
                'action' => 'create-user'
              ),
            )
          ),
          'company' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
              'route' => '/fyrirtaeki',
              'defaults' => array(
                'controller' => 'FlightInfo\Controller\Auth',
                'action' => 'create-user-company'
              ),
            )
          ),
          'login' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
              'route' => '/innskra',
              'defaults' => array(
                'controller' => 'FlightInfo\Controller\Auth',
                'action' => 'create-user-login'
              ),
            )
          ),
          'confirm' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
              'route' => '/stadfesta',
              'defaults' => array(
                'controller' => 'FlightInfo\Controller\Auth',
                'action' => 'create-user-confirm'
              ),
            )
          ),
          'lost-password' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
              'route' => '/tynt-lykilord',
              'defaults' => array(
                'controller' => 'FlightInfo\Controller\Auth',
                'action' => 'lost-password'
              ),
            )
          ),
        ),
      ),
      'auth-out' => array(
        'type' => 'Zend\Mvc\Router\Http\Literal',
        'options' => array(
          'route' => '/utskra',
          'defaults' => array(
            'controller' => 'FlightInfo\Controller\Auth',
            'action' => 'logout'
          ),
        ),
      ),
    ),
  ),
  'service_manager' => array(
    'abstract_factories' => array(
      'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
      'Zend\Log\LoggerAbstractServiceFactory',
    ),
    'aliases' => array(
      'translator' => 'MvcTranslator',
    ),
  ),
  'translator' => array(
    'locale' => 'en_US',
    'translation_file_patterns' => array(
      array(
        'type'     => 'gettext',
        'base_dir' => __DIR__ . '/../language',
        'pattern'  => '%s.mo',
      ),
    ),
  ),
  'controllers' => array(
    'invokables' => array(
      'FlightInfo\Controller\Index' => 'FlightInfo\Controller\IndexController',
      'FlightInfo\Controller\User' => 'FlightInfo\Controller\UserController',
    ),
  ),
  'view_helpers' => array(
    'invokables' => array(
      'paragrapher' => 'FlightInfo\View\Helper\Paragrapher',
    ),
  ),
  'view_manager' => array(
    'display_not_found_reason' => true,
    'display_exceptions'       => true,
    'doctype'                  => 'HTML5',
    'not_found_template'       => 'error/404',
    'exception_template'       => 'error/index',
    'base_path' => '/flight-info/',
    'strategies' => array(
      'ViewFeedStrategy',
      'ViewJsonStrategy',
    ),
    'template_map' => array(
      'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
      'layout/landing'          => __DIR__ . '/../view/layout/landing.phtml',
      'layout/anonymous'        => __DIR__ . '/../view/layout/anonymous.phtml',
      'layout/csv'           	  => __DIR__ . '/../view/layout/csv.phtml',
      'flight-info/index/index'  => __DIR__ . '/../view/flight-info/index/index.phtml',
      'error/404'               => __DIR__ . '/../view/error/404.phtml',
      'error/401'               => __DIR__ . '/../view/error/401.phtml',
      'error/index'             => __DIR__ . '/../view/error/index.phtml',
    ),
    'template_path_stack' => array(
      __DIR__ . '/../view',
    ),
  ),
    // Placeholder for console routes
  'console' => array(
    'router' => array(),
  ),
);
