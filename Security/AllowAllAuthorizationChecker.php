<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Security;

/**
 * Authorization checker interface for all objects controlled by the REST controller
 *
 * @author Lars Vierbergen <vierbergenlars@gmail.com>
 */
class AllowAllAuthorizationChecker implements  AuthorizationCheckerInterface
{

    public function mayList()
    {
        return true;
    }

    public function mayCreate($object)
    {
        return true;
    }

    public function mayView($object)
    {
        return true;
    }

    public function mayEdit($object)
    {
        return true;
    }

    public function mayDelete($object)
    {
        return true;
    }
}
