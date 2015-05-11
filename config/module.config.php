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
      'auth-in' => array(
        'type' => 'Zend\Mvc\Router\Http\Literal',
        'options' => array(
          'route' => '/innskra',
          'defaults' => array(
            'controller' => 'FlightInfo\Controller\Auth',
            'action' => 'login'
          ),
        ),
      ),
      'airport' => array(
        'type' => 'Zend\Mvc\Router\Http\Literal',
        'options' => array(
          'route' => '/flugvollur',
          'defaults' => array(
            'controller' => 'FlightInfo\Controller\Airport',
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
                'controller' => 'FlightInfo\Controller\Airport',
                'action' => 'index'
              ),
            )
          ),
          'list' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
              'route' => '/sida/:no',
              'constraints' => array(
                'no' => '[0-9]*',
              ),
              'defaults' => array(
                'controller' => 'FlightInfo\Controller\Airport',
                'action' => 'list'
              ),
            )
          ),
          'create' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
              'route' => '/skra',
              'defaults' => array(
                'controller' => 'FlightInfo\Controller\Airport',
                'action' => 'create'
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
                'controller' => 'FlightInfo\Controller\Airport',
                'action' => 'update'
              ),
            )
          ),
        ),
      ),
      'airline' => array(
        'type' => 'Zend\Mvc\Router\Http\Literal',
        'options' => array(
          'route' => '/flugfelag',
          'defaults' => array(
            'controller' => 'FlightInfo\Controller\Airline',
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
                'controller' => 'FlightInfo\Controller\Airline',
                'action' => 'index'
              ),
            )
          ),
          'list' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
              'route' => '/sida/:no',
              'constraints' => array(
                'no' => '[0-9]*',
              ),
              'defaults' => array(
                'controller' => 'FlightInfo\Controller\Airline',
                'action' => 'list'
              ),
            )
          ),
          'create' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
              'route' => '/skra',
              'defaults' => array(
                'controller' => 'FlightInfo\Controller\Airline',
                'action' => 'create'
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
                'controller' => 'FlightInfo\Controller\Airline',
                'action' => 'update'
              ),
            )
          ),
        ),
      ),
      'flight' => array(
        'type' => 'Zend\Mvc\Router\Http\Literal',
        'options' => array(
          'route' => '/flug',
          'defaults' => array(
            'controller' => 'FlightInfo\Controller\Flight',
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
                'controller' => 'FlightInfo\Controller\Flight',
                'action' => 'index'
              ),
            )
          ),
          'list' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
              'route' => '/sida/:no',
              'constraints' => array(
                'no' => '[0-9]*',
              ),
              'defaults' => array(
                'controller' => 'FlightInfo\Controller\Flight',
                'action' => 'list'
              ),
            )
          ),
          'create' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
              'route' => '/skra',
              'defaults' => array(
                'controller' => 'FlightInfo\Controller\Flight',
                'action' => 'create'
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
                'controller' => 'FlightInfo\Controller\Flight',
                'action' => 'update'
              ),
            )
          ),
        ),
      ),
      'user' => array(
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
          'list' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
              'route' => '/sida/:no',
              'constraints' => array(
                'no' => '[0-9]*',
              ),
              'defaults' => array(
                'controller' => 'FlightInfo\Controller\User',
                'action' => 'list'
              ),
            )
          ),
          'create' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
              'route' => '/skra',
              'defaults' => array(
                'controller' => 'FlightInfo\Controller\User',
                'action' => 'create'
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
      'FlightInfo\Controller\Auth' => 'FlightInfo\Controller\AuthController',
      'FlightInfo\Controller\Airport' => 'FlightInfo\Controller\AirportController',
      'FlightInfo\Controller\Airline' => 'FlightInfo\Controller\AirlineController',
      'FlightInfo\Controller\Flight' => 'FlightInfo\Controller\FlightController',
      'FlightInfo\Controller\Console' => 'FlightInfo\Controller\ConsoleController',
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
    'router' => array(
      'routes' => array(
        'queue-events' => array(
          'options' => array(
            'route'    => 'process stream',
            'defaults' => array(
              'controller' => 'FlightInfo\Controller\Console',
              'action'     => 'process-stream'
            )
          )
        ),
      ),
    ),
  ),
);