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
use vierbergenlars\Bundle\RadRestBundle\ApiDoc\Handler\RadRestServiceHandler;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @covers vierbergenlars\Bundle\RadRestBundle\ApiDoc\Handler\RadRestServiceHandler
 * @covers vierbergenlars\Bundle\RadRestBundle\ApiDoc\Handler\AbstractRadRestHandler
 * @covers vierbergenlars\Bundle\RadRestBundle\Controller\ControllerServiceController
 * @covers vierbergenlars\Bundle\RadRestBundle\Manager\FrontendManager
 */
class RadRestServiceHandlerTest extends AbstractRadRestHandlerTest
{
    public function setUp()
    {
        parent::setUp();
        $this->container->register('controller.user')
        ->setClass('vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Controller\UserServiceController')
        ->setArguments(array(new Reference('resource_manager'), new Reference('form'), new Reference('form_factory')));
        $this->container->register('controller.overridden_method')
        ->setClass('vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Controller\OverriddenMethodServiceController')
        ->setArguments(array(new Reference('resource_manager'), new Reference('form'), new Reference('form_factory')));
        $this->handler = new RadRestServiceHandler($this->container);

    }

    protected function getReflectionMethod(Route $route)
    {
        $pieces = explode(':', $route->getDefault('_controller'));
        $controller = get_class($this->container->get($pieces[0]));
        return new \ReflectionMethod($controller.'::'.$pieces[1]);
    }

    protected function getRouteControllerString($type, $action)
    {
        switch($type) {
            case self::ROUTE_TYPE_DEFAULT:
                $controller = 'controller.user';
                break;
            case self::ROUTE_TYPE_OVERRIDDEN:
                $controller = 'controller.overridden_method';
                break;
            case self::ROUTE_TYPE_SWITCHED_SERIALIZATION:
                $controller = 'controller.switched_serialization';
                break;
        }
        return $controller.':'.$action.'Action';
    }
}
