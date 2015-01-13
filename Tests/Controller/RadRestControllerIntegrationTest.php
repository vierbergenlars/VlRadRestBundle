<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Tests\Controller;

use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Controller\UserController;
use Symfony\Component\Routing\Route;

/**
 * @coversNothing
 */
class RadRestControllerIntegrationTest extends AbstractControllerIntegrationTest
{
    protected function route($path, $action, $method)
    {
        $route = new Route($path, array('_controller'=>'vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Controller\UserController::'.$action.'Action'));
        $route->setMethods($method);
        return $route;
    }

    protected function createController()
    {
        $controller = new UserController();
        $controller->setContainer($this->container);
        return $controller;
    }
}
