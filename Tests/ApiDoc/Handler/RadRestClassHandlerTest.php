<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Tests\ApiDoc\Handler;

use Symfony\Component\Routing\Route;
use vierbergenlars\Bundle\RadRestBundle\ApiDoc\Handler\RadRestClassHandler;

/**
 * @covers vierbergenlars\Bundle\RadRestBundle\ApiDoc\Handler\RadRestClassHandler
 * @covers vierbergenlars\Bundle\RadRestBundle\ApiDoc\Handler\AbstractRadRestHandler
 * @covers vierbergenlars\Bundle\RadRestBundle\Controller\RadRestController
 * @covers vierbergenlars\Bundle\RadRestBundle\Manager\FrontendManager
 */
class RadRestClassHandlerTest extends AbstractRadRestHandlerTest
{
    public function setUp()
    {
        parent::setUp();
        $this->handler = new RadRestClassHandler($this->container);
    }

    protected function getReflectionMethod(Route $route)
    {
        return new \ReflectionMethod($route->getDefault('_controller'));
    }

    protected function getRouteControllerString($type, $action)
    {
        $controller = 'vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Controller\\';
        switch($type) {
            case self::ROUTE_TYPE_DEFAULT:
                $controller .= 'UserController';
                break;
            case self::ROUTE_TYPE_OVERRIDDEN:
                $controller .= 'OverriddenMethodController';
                break;
            case self::ROUTE_TYPE_SWITCHED_SERIALIZATION:
                $controller .= 'SwitchedSerializationController';
                break;
        }
        return $controller.'::'.$action.'Action';
    }
}
