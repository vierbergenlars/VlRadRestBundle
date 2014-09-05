<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Tests\Controller\Traits\Routing;

use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\Loader\ClosureLoader;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
/**
 * @covers vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routing\DefaultRoutingTrait
 */
class DefaultRoutingTraitTest extends \PHPUnit_Framework_TestCase
{
    private $router;
    private $routeCollection;
    private $routingTrait;

    protected function setUp()
    {
        if(PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 4)
            $this->markTestSkipped('PHP 5.4 required to use traits');

        $this->routeCollection = $rc = new RouteCollection();
        $this->router = new Router(new ClosureLoader(), function() use($rc) {
            return $rc;
        });
        $this->routeCollection->add('logout', new Route('/logout'));
        $this->routeCollection->add('get_users', new Route('/users', array('_controller'=>'abcde:cgetAction')));
        $this->routeCollection->add('get_user', new Route('/user/{id}', array('_controller'=>'abcde:getAction')));

        $this->routingTrait = $this->getMockBuilder('vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routing\DefaultRoutingTrait')
            ->enableProxyingToOriginalMethods()
            ->getMockForTrait();
    }

    public function testGetRouteName()
    {
        $this->routingTrait->expects($this->once())
            ->method('getLogger')
            ->with()
            ->willReturn(null);

        $this->routingTrait->expects($this->once())
            ->method('getActionResourceName')
            ->with('get')
            ->willReturn('abcde:getAction');

        $this->routingTrait->expects($this->atLeastOnce())
            ->method('getRouter')
            ->with()
            ->willReturn($this->router);

        $this->assertEquals('get_user', $this->routingTrait->getRouteName('get'));
    }

    public function testGetRouteNameLoggerWarning()
    {
        $this->routingTrait->expects($this->atLeastOnce())
            ->method('getLogger')
            ->with()
            ->willReturn($logger = $this->getMock('Psr\Log\LoggerInterface'));

        $logger->expects($this->once())
            ->method('warning')
            ->with($this->anything(), $this->anything());

        $this->routingTrait->expects($this->once())
            ->method('getActionResourceName')
            ->with('get')
            ->willReturn('abcde:getAction');

        $this->routingTrait->expects($this->atLeastOnce())
            ->method('getRouter')
            ->with()
            ->willReturn($this->router);

        $this->assertEquals('get_user', $this->routingTrait->getRouteName('get'));
    }

    /**
     * @expectedException LogicException
     */
    public function testGetRouteNameFail()
    {
        $this->routingTrait->expects($this->once())
            ->method('getLogger')
            ->with()
            ->willReturn(null);

        $this->routingTrait->expects($this->once())
            ->method('getActionResourceName')
            ->with('put')
            ->willReturn('abcde:putAction');

        $this->routingTrait->expects($this->atLeastOnce())
            ->method('getRouter')
            ->with()
            ->willReturn($this->router);

        $this->routingTrait->getRouteName('put');
    }

    /**
     * @expectedException LogicException
     */
    public function testGetRouteNameNoRouter()
    {
        $this->routingTrait->expects($this->once())
            ->method('getActionResourceName')
            ->with('get')
            ->willReturn('abcde:getAction');

        $this->routingTrait->expects($this->atLeastOnce())
            ->method('getRouter')
            ->willReturn(null);

        $this->routingTrait->getRouteName('get');
    }

    /**
     * @expectedException LogicException
     */
    public function testGetRouteNameNoResourceName()
    {
        $this->routingTrait->expects($this->once())
            ->method('getActionResourceName')
            ->with('get')
            ->willReturn(null);

        $this->routingTrait->expects($this->never())
            ->method('getRouter')
            ->willReturn($this->router);

        $this->routingTrait->getRouteName('get');
    }

}
