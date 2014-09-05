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
use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Controller\TraitedController;

/**
 * @coversNothing
 */
class TraitedRadRestControllerTest extends AbstractControllerTest
{
    protected function setUp()
    {
        if(PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 4)
            $this->markTestSkipped('PHP 5.4 required to use traits');
        parent::setUp();
    }

    protected function route($path, $action, $method)
    {
        $route = new Route($path, array('_controller'=>'vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Controller\TraitedController::'.$action.'Action'));
        $route->setMethods($method);
        return $route;
    }

    protected function createController()
    {
        return new TraitedController($this->container);
    }
}
