<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 13/05/15
 * Time: 7:37 AM
 */

namespace FlightInfo\Auth;

use Zend\Authentication\AuthenticationService as BaseAuthService;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthenticationService extends BaseAuthService implements ServiceLocatorAwareInterface
{
    protected $serviceLocator;

    /**
     * @todo implement
     * @return bool
     */
    public function assert()
    {
        return true;
    }

    /**
     * Check if logged in user is Admin
     *
     * @return bool
     */
    public function isAdmin()
    {
        if ($this->hasIdentity()) {
            return $this->getIdentity()->is_admin;
        } else {
            return false;
        }
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return $this
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
}
