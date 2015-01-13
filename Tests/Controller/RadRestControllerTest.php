<?php

namespace vierbergenlars\Bundle\RadRestBundle\Tests\Controller;

use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Controller\UserController;
use Symfony\Component\DependencyInjection\Container;

/**
 * @covers vierbergenlars\Bundle\RadRestBundle\Controller\RadRestController
 */
class RadRestControllerTest extends \PHPUnit_Framework_TestCase
{
    private $controller;
    private $container;

    protected function setUp()
    {
        $this->controller = new UserController();
        $this->container = new Container();
        $this->controller->setContainer($this->container);
    }

    public function testGetLogger()
    {
        $logger = $this->getMock('Psr\Log\LoggerInterface');
        $this->container->set('logger', $logger);

        $this->assertSame($logger, $this->controller->_getLogger());
    }

    public function testGetLoggerNull()
    {
        $this->assertNull($this->controller->_getLogger());
    }

    public function testGetRouter()
    {
        $router = $this->getMock('Symfony\Component\Routing\RouterInterface');
        $this->container->set('router', $router);

        $this->assertSame($router, $this->controller->_getRouter());
    }

    public function testGetRouterNull()
    {
        $this->assertNull($this->controller->_getRouter());
    }

    public function testGetFormFactory()
    {
        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $this->container->set('form_factory', $formFactory);

        $this->assertSame($formFactory, $this->controller->_getFormFactory());
    }
}