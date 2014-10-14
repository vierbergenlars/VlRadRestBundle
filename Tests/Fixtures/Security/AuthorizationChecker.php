<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Security;

use vierbergenlars\Bundle\RadRestBundle\Security\AbstractAuthorizationChecker;

class AuthorizationChecker extends AbstractAuthorizationChecker
{
    public function _getSecurityContext()
    {
        return $this->getSecurityContext();
    }

    public function _getTrustResolver()
    {
        return $this->getTrustResolver();
    }

    public function _getRoleHierarchy()
    {
        return $this->getRoleHierarchy();
    }

    public function _getToken()
    {
        return $this->getToken();
    }

    public function _getRoles()
    {
        return $this->getRoles();
    }

    public function _getUser()
    {
        return $this->getUser();
    }

    public function _hasRole($r)
    {
        return $this->hasRole($r);
    }

    public function mayList()
    {
        return true;
    }

    public function mayCreate($object)
    {
        return true;
    }

    public function mayDelete($object)
    {
        return true;
    }

    public function mayEdit($object)
    {
        return true;
    }

    public function mayView($object)
    {
        return true;
    }
}
