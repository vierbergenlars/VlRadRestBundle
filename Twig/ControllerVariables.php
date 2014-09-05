<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Twig;

use vierbergenlars\Bundle\RadRestBundle\Controller\RadRestControllerInterface;
use vierbergenlars\Bundle\RadRestBundle\Security\AuthorizationCheckerInterface;

class ControllerVariables
{
    private $controller;

    public function __construct(RadRestControllerInterface $controller)
    {
        $this->controller = $controller;
    }

    /**
     * Gets the name of the route belonging to an action on the current controller
     * @param string $action
     * @return string
     */
    public function route($action)
    {
        return $this->controller->getRouteName($action);
    }

    /**
     * Gets the authorization checker of the current controller
     * @return AuthorizationCheckerInterface
     */
    public function getAuthorizationChecker()
    {
        return $this->controller->getFrontendManager()->getAuthorizationChecker();
    }

    /**
     * Checks if the currently logged in user is allowed to perform an action on the current controller
     * @param string $action The action to check. May be one of the standard actions on the controller,
     *                         or the method to call on the authorization checker without the may prefix.
     * @param object $object An object to pass to the authorization checker for actions that manipulate an existing object.
     * @return boolean
     */
    public function may($action, $object = null)
    {
        $action = strtolower($action);
        $map    = array('cget'=>'list', 'get'=>'view', 'new'=>'create', 'post'=>'create','put'=>'edit', 'remove'=>'delete');
        if(isset($map[$action])) {
            $action = $map[$action];
        }
        $method = 'may'.ucfirst($action);
        return $this->getAuthorizationChecker()->{$method}($object);
    }
}
