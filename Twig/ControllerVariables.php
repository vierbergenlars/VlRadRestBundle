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

class ControllerVariables
{
    private $controller;

    public function __construct(RadRestControllerInterface $controller)
    {
        $this->controller = $controller;
    }

    public function route($action)
    {
        return $this->controller->getRouteName($action);
    }

    public function getAuthorizationChecker()
    {
        return $this->controller->getFrontendManager()->getAuthorizationChecker();
    }

    public function may($action, $object = null)
    {
        $action = strtolower($action);
        $map = array('cget'=>'list', 'get'=>'view', 'new'=>'create', 'post'=>'create','put'=>'edit', 'remove'=>'delete');
        if(isset($map[$action]))
            $action = $map[$action];
        $method = 'may'.ucfirst($action);
        return $this->getAuthorizationChecker()->{$method}($object);
    }
}
